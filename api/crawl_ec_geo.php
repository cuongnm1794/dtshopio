<?php
include "../db.php";
libxml_use_internal_errors(true); // Tắt cảnh báo

// set time out
set_time_limit(0);

// show error
ini_set('display_errors', 1);

function curl_get($link)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

$url_iphone = "https://ec.geo-online.co.jp/shop/goods/search.aspx?&search.x=0&tree={code}&ps=40";
$url_samsung = "https://ec.geo-online.co.jp/shop/c/c1012/?search.x=0&keyword=&goods_code=&store=&tree={code}&genre_tree=&capacity=&color=&price=&flg=&q_not=&ps=40";

$type = $_GET["type"] ?? "iphone";
$page = $_GET["page"] ?? 1;

$iphone_codes = [
    100150, 100151, 100152, 100153, 100154, 100155, 100156, 100157, 100158, 100159, 100160, 100161, 100162, 100163, 100164, 100165, 100166, 100167, 100168, 100169, 100170, 100171, 100172, 100173, 100174, 100175, 100176
];

$ss_codes = [
    101251,    101252,    101258,    101259,    101266,    101267,    101277,    101254,    101255,    101261,    101262,    101263,    101271,    101273,    101274,    101275,    101284,    101285,    101288,    101289,    101269,    101270,    101279,    101280,    101287,
];

function crawl($url, $code, $page = 1)
{
    global $db;
    global $type;

    $url = $url . "&p=" . $page;

    // replace {code} with code
    $url = str_replace("{code}", $code, $url);
    $html = curl_get($url);
    $dom = new DOMDocument();
    $dom->loadHTML($html);

    // get ul class itemList
    $finder = new DomXPath($dom);
    $classname = "itemList";

    $nodes = $finder->query("//ul[@class='$classname']//li");

    // echo count
    $total = 0;
    foreach ($nodes as $node) {
        $total++;
    }

    if (!$total) {
        return false;
    }

    $products = [];
    $links = [];

    // find ul class conditions_list
    $classname = "conditions_list";
    $conditions_list = $finder->query("//ul[@class='$classname']//li//a");
    // get content
    $model = $conditions_list[0]->textContent;

    foreach ($nodes as $node) {


        $finder = new DomXPath($node->ownerDocument);
        $a = $node->getElementsByTagName("a")[0];
        $link = $a->getAttribute("href");
        $item = [
            'id_product' => '',
            'name' => '',
            'price' => '',
            'link' => '',
            'status' => '',
            'model' => '',
            'type' => $type,
            'rank' => '',
            'network' => '',
            'imei' => '',
            'capacity' => '',
            'inventory' => ''
        ];
        $item['link'] = $link;

        // id is last part of link
        $id = explode("/", $link);
        $id = $id[count($id) - 2];
        $item['id_product'] = $id;

        if (empty($id)) {
            continue;
        }

        $item['model'] = $model;


        // find image in tag a
        $img = $a->getElementsByTagName("img")[0];
        // get alt
        $item['name'] = $img->getAttribute("alt");

        // rank
        $classname = "labelSituation";
        $rank = $finder->query(".//*[@class='$classname']", $node)[0];
        $item['rank'] = $rank->textContent;

        // capacity
        $classname = "labelCapacity";
        $capacity = $finder->query(".//*[@class='$classname']", $node)[0];
        $item['capacity'] = $capacity->textContent;

        // network = itemCarrier
        $classname = "itemCarrier";
        $network = $finder->query(".//*[@class='$classname']", $node)[0];
        $item['network'] = $network->textContent;

        // price = sellPtnLeftPrice
        $classname = "sellPtnLeftPrice";
        $price = $finder->query(".//*[@class='$classname']/b", $node)[0];
        $item['price'] = $price->textContent;

        // remove , 
        $item['price'] = str_replace(",", "", $item['price']);


        $products[] = $item;

        // check if product exist in table ec_geos
        $sql = "SELECT * FROM ec_geos WHERE id_product = '" . $item['id_product'] . "'";
        $result = $db->query($sql);


        if ($result->rowCount() == 0) {
            $sql = "INSERT INTO ec_geos (id_product, name, link, status, model, `type`, `rank`, network, imei, capacity, inventory, last_price) VALUES ('" . $item['id_product'] . "', '" . $item['name'] . "', '" . $item['link'] . "', '" . $item['status'] . "', '" . $item['model'] . "', '" . $item['type'] . "', '" . $item['rank'] . "', '" . $item['network'] . "', '" . $item['imei'] . "', '" . $item['capacity'] . "', '" . $item['inventory'] . "', '" . $item['price'] . "')";
            $db->query($sql);

            // insert in to product_prices
            $sql = "INSERT INTO product_prices (id_product, price, site) VALUES ('" . $item['id_product'] . "', '" . $item['price'] . "', 'ec_geo')";
            $db->query($sql);
        } else {
            // check if price is different
            $sql = "SELECT * FROM product_prices WHERE id_product = '" . $item['id_product'] . "' AND site = 'ec_geo'";
            $result = $db->query($sql);

            if ($result->rowCount() > 0) {
                $row = $result->fetch(PDO::FETCH_ASSOC);
                if ($row['price'] != $item['price']) {
                    // insert in to product_prices
                    $sql = "INSERT INTO product_prices (id_product, price, site) VALUES ('" . $item['id_product'] . "', '" . $item['price'] . "', 'ec_geo')";
                    $db->query($sql);

                    // update last price
                    $sql = "UPDATE ec_geos SET last_price = '" . $item['price'] . "' WHERE id_product = '" . $item['id_product'] . "'";
                    $db->query($sql);
                }
            }
        }
    }

    return true;
}

function writeLog($content)
{
    $myfile = fopen("log.txt", "a") or die("Unable to open file!");
    $txt = $content . "\n";
    fwrite($myfile, $txt);
    fclose($myfile);
}

if ($type == "iphone") {
    foreach ($iphone_codes as $code) {
        writeLog("<h2>Crawl code: " . $code . "</h2>");
        for ($page = 1; $page <= 100; $page++) {
            writeLog("<h3>Crawl page: " . $page . "</h3>");
            $result_check = crawl($url_iphone, $code, $page);
            if (!$result_check) {
                break;
            }
        }
        writeLog("<hr>");
    }
} else if ($type == "samsung") {
    foreach ($ss_codes as $code) {
        writeLog("<h2>Crawl code: " . $code . "</h2>");
        for ($page = 1; $page <= 100; $page++) {
            writeLog("<h3>Crawl page: " . $page . "</h3>");
            $result_check = crawl($url_iphone, $code, $page);
            if (!$result_check) {
                break;
            }
        }
        writeLog("<hr>");
    }
}
