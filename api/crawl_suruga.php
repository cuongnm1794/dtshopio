<?php
include "../db.php";
include "./helper.php";
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
    // accept redirect
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

$url_iphone = "https://www.suruga-ya.jp/search?category=6500000&search_word=";

function crawl($url,  $page = 1)
{

    global $db;

    $url = $url . "&page=" . $page;

    $html = curl_get($url);


    $dom = new DOMDocument();
    $dom->encoding = 'UTF-8';
    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    // get ul class itemList
    $finder = new DomXPath($dom);
    $classname = "itemList";

    $nodes = $finder->query("//div[@class='item']");

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

    foreach ($nodes as $node) {

        $item = [
            'id_product' => '',
            'name' => '',
            'price' => '',
            'link' => '',
            'status' => '',
            'detail' => '',
            'model' => '',
        ];

        $finder = new DomXPath($node->ownerDocument);
        $title = $finder->query(".//*[@class='title']//a", $node)[0];
        $item['link'] = $title->getAttribute("href");
        $id = explode("/", $item['link']);
        $item['id_product'] = $id[count($id) - 1];
        $item['name'] = $title->textContent;

        //iPhone 15 Pro 1TB (docomo/ブルーチタニウム) [MTUU3J/A]
        //iPhone 15 Pro Max 256GB (SIMフリー/ブラックチタニウム) [MU6P3J/A]

        // regex (.*?)(TB|GB) and get first
        $pattern = "/(.*?)(TB|GB)/";
        preg_match($pattern, $item['name'], $matches);

        if (count($matches) > 0) {
            $item['model'] = $matches[0];
        }

        // check if link host has https://www.suruga-ya.jp
        if (strpos($item['link'], "https://www.suruga-ya.jp") === false) {
            // add https://www.suruga-ya.jp
            $item['link'] = "https://www.suruga-ya.jp" . $item['link'];
            $item['status'] = "Hết hàng";

            // id => split ? and get first
            $id = explode("?", $item['id_product']);
            $item['id_product'] = $id[0];
        }


        // price => div class item_price -> span class text-red -> strong
        $classname = "item_price";
        $price = $finder->query(".//*[@class='$classname']//span[contains(@class,'text-red')]//strong", $node)[0];
        $item['price'] = $price->textContent;

        // remove ￥ & ,
        $item['price'] = str_replace("￥", "", $item['price']);
        $item['price'] = str_replace(",", "", $item['price']);

        $html_detail = curl_get($item['link']);
        $dom_detail = new DOMDocument();
        $dom_detail->loadHTML($html_detail);

        $finder_detail = new DomXPath($dom_detail);
        // find div contains text 備考
        $xpath = ".//div//h3[contains(text(),'備考')]";
        $detail = $finder_detail->query($xpath);
        if ($detail->length > 0) {

            // get parent of h3
            $parent = $detail[0]->parentNode;

            // find p note
            $note = $finder_detail->query(".//p", $parent);
            if ($note->length > 0) {
                $item['detail'] = $note[0]->textContent;
            }
        }
        $products[] = $item;

        // check if product exist in table ec_geos
        $sql = "SELECT * FROM surugas WHERE id_product = '" . $item['id_product'] . "'";
        $result = $db->query($sql);

        if ($result->rowCount() == 0) {
            // insert in to surugas
            $sql = "INSERT INTO surugas (id_product, name, link, status, detail, last_price, model) VALUES ('" . $item['id_product'] . "', '" . $item['name'] . "', '" . $item['link'] . "', '" . $item['status'] . "', '" . $item['detail'] . "', '" . $item['price'] . "', '" . $item['model'] . "')";
            $db->query($sql);


            // insert in to product_prices
            $sql = "INSERT INTO product_prices (id_product, price, site) VALUES ('" . $item['id_product'] . "', '" . $item['price'] . "', 'surugas')";
            $db->query($sql);
        } else {
            // check if price is different
            $sql = "SELECT * FROM product_prices WHERE id_product = '" . $item['id_product'] . "' AND site = 'surugas'";
            $result = $db->query($sql);

            if ($result->rowCount() > 0) {
                $row = $result->fetch(PDO::FETCH_ASSOC);
                if ($row['price'] != $item['price']) {
                    // insert in to product_prices
                    $sql = "INSERT INTO product_prices (id_product, price, site) VALUES ('" . $item['id_product'] . "', '" . $item['price'] . "', 'surugas')";
                    $db->query($sql);

                    // update last price
                    $sql = "UPDATE surugas SET last_price = '" . $item['price'] . "' WHERE id_product = '" . $item['id_product'] . "'";
                    $db->query($sql);
                }
            } else {
                // insert in to product_prices
                $sql = "INSERT INTO product_prices (id_product, price, site) VALUES ('" . $item['id_product'] . "', '" . $item['price'] . "', 'surugas')";
                $db->query($sql);

                // update last price
                $sql = "UPDATE surugas SET last_price = '" . $item['price'] . "' WHERE id_product = '" . $item['id_product'] . "'";
                $db->query($sql);
            }
        }

        // get product in database
        $sql = "SELECT * FROM surugas WHERE id_product = '" . $item['id_product'] . "'";
        $result = $db->query($sql);

        $item_product = $result->fetch(PDO::FETCH_ASSOC);

        // check if send_message = 1 continue
        if ($item_product['send_message'] == 1) {
            continue;
        }

        // check price
        $sql = "SELECT * FROM setting_price_surugas";
        $result = $db->query($sql);

        $setting_prices = $result->fetchAll();
        foreach ($setting_prices as $setting_price) {
            // check empty continue
            if (empty($setting_price['keywords']) || empty($setting_price['price'])) {
                continue;
            }
            $keywords = explode(",", $setting_price['keywords']);
            foreach ($keywords as $keyword) {
                if ($item['model'] == $keyword) {
                    if ($item['price'] <= $setting_price['price']) {
                        $content = "Sản phẩm " . $item['name'] . " có giá " . number_format($item['price']) . " thấp hơn giá cài đặt " . number_format($setting_price['price']) . " của từ khóa " . $keyword . "\n Link: " . $item['link'];
                        sendMessage($content);

                        // update send_message in surugas
                        $sql = "UPDATE surugas SET send_message = 1 WHERE id_product = '" . $item['id_product'] . "'";
                        $db->query($sql);
                    }
                }
            }
        }
    }


    return true;
}

function writeLog($content)
{
    // $myfile = fopen("log.txt", "a") or die("Unable to open file!");
    // $txt = $content . "\n";
    // fwrite($myfile, $txt);
    // fclose($myfile);
}

for ($page = 1; $page <= 100; $page++) {
    writeLog("<h3>Crawl page: " . $page . "</h3>");
    $result_check = crawl($url_iphone,  $page);
    if (!$result_check) {
        break;
    }
}