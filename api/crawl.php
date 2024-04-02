<?php
include "../db.php";
include "./helper.php";
libxml_use_internal_errors(true); // Tắt cảnh báo

// set time out
set_time_limit(0);

// show error
ini_set('display_errors', 1);


// function curl get 
function curl_get($link)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function crawl($page = 1)
{

    global $db;

    $link = "https://online.nojima.co.jp/app/catalog/list/init?searchCategoryCode=0&mode=image&pageSize=120&currentPage=1&alignmentSequence=2&searchDispFlg=true&discontinuedFlg=1&immediateDeliveryDispFlg=1&searchWord=iPhone%20%E4%B8%AD%E5%8F%A4%E5%93%81&searchExclusionWord=SE";
    $html = curl_get($link);

    // file_put_contents("test.html",$html);

    // find dom class commoditylistitem
    $dom = new DOMDocument();

    // load html
    $dom->loadHTML($html);

    // find dom class commoditylistitem
    $finder = new DomXPath($dom);
    $classname = "commoditylistitem";
    $nodes = $finder->query("//*[@class='$classname']//li");
    $total = 0;
    // total 

    $products = [];

    // loop nodes
    foreach ($nodes as $node) {
        // find dom class commoditylistitem
        $product = new stdClass();
        $product->name = "";
        $product->link = "";
        $product->status = "";
        $product->price = "";
        $product->id = "";
        $product->model = "";

        // find dom class commoditylistitem
        // $finder = new DomXPath($node);

        // find dom class commoditylistitem
        $classname = "cmdty_iteminfo";

        // find dom class commoditylistitem
        $nodesText = $finder->query(".//*[@class= '$classname']//a//span[2]", $node);

        // get first
        $nodeText = $nodesText->item(0);

        // get text
        $product->name = trim($nodeText->nodeValue);

        // $myfile = fopen("log.txt", "a") or die("Unable to open file!");
        // $txt = $product->name . "\n";
        // fwrite($myfile, $txt);
        // fclose($myfile);

        // split by 】 and get last
        $product->name = explode("】", $product->name)[count(explode("】", $product->name)) - 1];

        // ltrim
        $product->name = ltrim($product->name, "\t");

        // check product name has デモ機

        $product->isDemo = strpos($product->name, "デモ機") !== false;



        $pattern_model = '/iPhone[^\s]+/u'; // regex pattern

        // get model in name
        // if (preg_match($pattern_model, $product->name, $matches)) {
        //     $product->model = $matches[0];
        // } else {
        $pattern = '/iPhone(.*?)GB/u';

        if (preg_match($pattern, $product->name, $matches)) {
            $product->model =  $matches[0];


            $pattern = '/\s*\d+GB\s*/u';
            $product->model = preg_replace($pattern, '', $product->model);


            // remove last after space
            // $info = explode(" ", $product->model);
            // // delete last
            // unset($info[count($info) - 1]);

            // $product->model = implode(" ", $info);
        }
        // }



        // get price with class=price
        $classname = "price";

        // find dom class commoditylistitem
        $nodesPrice = $finder->query(".//*[@class= '$classname']", $node);
        $nodePrice = $nodesPrice->item(0);
        $product->price = trim($nodePrice->nodeValue);

        // price = split new line -> get first
        $product->price = explode("\n", $product->price)[0];

        // get only number
        $product->price = preg_replace("/[^0-9]/", "", $product->price);

        // get status by class name lcogreen
        $classname = "lcogreen";

        // find dom class commoditylistitem
        $nodesStatus = $finder->query(".//*[@class= '$classname']", $node);

        // check if 0 item
        if ($nodesStatus->length > 0) {

            $product->status = trim("Available");
        } else {
            $product->status = trim("");
        }


        // find tag a
        $nodesText = $finder->query(".//a", $node);

        $nodeText = $nodesText->item(0);

        // get href
        $product->link = $nodeText->getAttribute("href");
        // split link by / and get last - 1
        $product->id = explode("/", $product->link)[count(explode("/", $product->link)) - 2];


        // push product to products
        $products[] = $product;
    }


    // loop products and check if exist in db -> add price to db else create new products
    foreach ($products as $product) {
        $sql = "SELECT count(1) as total FROM products WHERE id_product = '$product->id'";
        $result = $db->query($sql);
        $total = $result->fetchColumn();

        if ($total > 0) {

            // update isDemo
            if ($product->isDemo) {
                $sql = "UPDATE products SET isDemo = '" . ($product->isDemo ? 1 : '0') . "' WHERE id_product = '$product->id'";
                $db->query($sql);
            }

            // get first 
            $sql = "SELECT * FROM products WHERE id_product = '$product->id' ORDER BY id DESC LIMIT 1";
            $result = $db->query($sql);
            $product_db = $result->fetch();

            // check if status is bought
            if ($product_db["status"] == "Bought") {
                // update status to available
                continue;
            }


            // check price not equal
            $sql = "SELECT * FROM product_prices WHERE id_product = '$product->id' ORDER BY id DESC LIMIT 1";
            $result = $db->query($sql);
            $row = $result->fetch();

            // check if status not equal

            if ($product_db["status"] != $product->status) {

                // update status
                $sql = "UPDATE products SET status = '$product->status' WHERE id_product = '$product->id'";
                $db->query($sql);
            }

            $sql = "UPDATE products SET updated_at = CURRENT_TIMESTAMP,last_price='$product->price'  WHERE id_product = '$product->id'";
            $db->query($sql);

            if ($row["price"] != $product->price) {
                // update price
                $sql = "INSERT INTO product_prices (id_product, price) VALUES ('$product->id', '$product->price')";
                $db->query($sql);
            } else {
                // updated at 
                $sql = "UPDATE products SET updated_at = CURRENT_TIMESTAMP WHERE id_product = '$product->id'";
                $db->query($sql);
            }
        } else {
            // create new product
            $sql = "INSERT INTO products (id_product, name, link,model, status, isDemo, last_price) VALUES ('$product->id', '$product->name', '$product->link','$product->model', '$product->status', '" . ($product->isDemo ? 1 : '0') . "','$product->price')";
            $db->query($sql);


            // create new price
            $sql = "INSERT INTO product_prices (id_product, price) VALUES ('$product->id', '$product->price')";
            $db->query($sql);
        }

        // get product in database
        $sql = "SELECT * FROM products WHERE id_product = '" . $product->id . "'";
        $result = $db->query($sql);

        $item_product = $result->fetch(PDO::FETCH_ASSOC);

        // check if send_message = 1 continue
        if ($item_product['send_message'] == 1) {
            continue;
        }

        // check price
        $sql = "SELECT * FROM setting_price";
        $result = $db->query($sql);

        $setting_prices = $result->fetchAll();
        foreach ($setting_prices as $setting_price) {
            // check empty continue
            if (empty($setting_price['keywords']) || empty($setting_price['price'])) {
                continue;
            }
            $keywords = explode(",", $setting_price['keywords']);
            foreach ($keywords as $keyword) {
                if ($product->model == $keyword) {
                    if ($product->price <= $setting_price['price']) {
                        $link = "https://online.nojima.co.jp/app/catalog/detail/addcart/1/" . $item_product['id_product'] . '?quantity=1&shopCode=1&giftCode=99&optionCommodity=99&selectSkuCode=' . $item_product['id_product'] . '&reorderFlg=true&shippingShopCode=1&oldAddreessNo=0&shippingAddress=928782&deliveryTypeCode=0';
                        $content = "Sản phẩm " . $product->name . " có giá " . number_format($product->price) . " thấp hơn giá cài đặt " . number_format($setting_price['price']) . " của từ khóa " . $keyword . "\n Link: " . $link;

                        try{
                        sendMessage($content);

                        }catch(Exception  $e){

                        }

                        // update send_message in surugas
                        $sql = "UPDATE products SET send_message = 1 WHERE id = '" . $product->id . "'";
                        $db->query($sql);
                        break;
                    }
                }
            }
        }
    }
    return $html;
}

$time_start = microtime(true);

$first = crawl(1);

// write file first
// file_put_contents("first.html", $first);

$total = 0;
$turns = 1;

$pattern = '/PC等\((\d+)\)/';  // Biểu thức chính quy để tìm số trong dấu ngoặc đơn

if (preg_match($pattern, $first, $matches)) {
    $number = $matches[1];

    echo "Tìm thấy số $number \n";

    // turns = number / 120
    $turns = $number / 60;

    sleep(5);

    // loop 2 to turns
    for ($i = 2; $i <= $turns + 1; $i++) {
        echo "Crawl page $i <br>";
        crawl($i);
        sleep(5);
    }
} else {
    echo "Không tìm thấy số trong chuỗi.";
}


$time_end = microtime(true);

//dividing with 60 will give the execution time in minutes other wise seconds
$execution_time = ($time_end - $time_start);

//execution time of the script
echo '<b>Total Execution Time:</b> ' . $execution_time . ' s';

?>

<script>
    // reload page after 2 minutes
    setTimeout(() => {
        window.location.reload();
    }, 120000);
</script>