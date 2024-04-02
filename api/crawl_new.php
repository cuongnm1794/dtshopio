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

function addProduct($products)
{

    global $db;


    // loop products and check if exist in db -> add price to db else create new products
    foreach ($products as $product) {
        echo "Run add ". $product->id.'<br/>';

            // check empty product continue
            if(empty($product->id))
                continue;

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
            echo $sql;
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
        $break_all = false;
        foreach ($setting_prices as $setting_price) {
            // check empty continue
            if (empty($setting_price['keywords']) || empty($setting_price['price']) || $setting_price['isDemo'] != $item_product['isDemo']) {
                continue;
            }

            // check if break all
            if ($break_all) {
                break;
            }

            $keywords = explode(",", $setting_price['keywords']);
            foreach ($keywords as $keyword) {
                if ($product->model == $keyword) {
                    if ($product->price <= $setting_price['price']) {
                        $link = "https://online.nojima.co.jp/app/catalog/detail/addcart/1/" . $item_product['id_product'] . '?quantity=1&shopCode=1&giftCode=99&optionCommodity=99&selectSkuCode=' . $item_product['id_product'] . '&reorderFlg=true&shippingShopCode=1&oldAddreessNo=0&shippingAddress=928782&deliveryTypeCode=0';
                        $link2 = "https://online.nojima.co.jp//commodity/1/" . $item_product['id_product'];
                        $content = "Sản phẩm <b>" . $product->name . " </b> - " . ($item_product['isDemo'] ? "Demo" : "Không demo") . " -  có giá " . number_format($product->price) . " thấp hơn giá cài đặt " . number_format($setting_price['price']) . " của từ khóa " . $keyword . "\n Link: <a href='" . $link . "'>Link mua</a>\n Link2: <a href='$link2'>Link chi tiết</a>";
                        sendMessage($content);

                        // update send_message in surugas
                        $sql = "UPDATE products SET send_message = 1 WHERE id = '" . $item_product['id'] . "'";
                        echo $sql;
                        $db->query($sql);
                        $break_all = true;
                        break;
                    }
                }
            }
        }
    }
}


function crawl($page = 1)
{

  

    $link = "https://online.nojima.co.jp/app/catalog/list/init?searchCategoryCode=0&mode=image&pageSize=120&currentPage=".$page."&alignmentSequence=3&searchDispFlg=true&discontinuedFlg=1&immediateDeliveryDispFlg=1&searchWord=iPhone%20%E4%B8%AD%E5%8F%A4%E5%93%81&searchExclusionWord=SE";
    echo urldecode($link) .'<br>';
    $html = curl_get($link);

    //file_put_contents("text.html",$html);

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
        $product->isDemo = false;

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
    $class_demo = "katabandasu";
     $nodesText = $finder->query(".//*[@class= '$class_demo']//span[1]", $node);

        // get first
        $nodeText = $nodesText->item(0);
         $html_demo = trim($nodeText->nodeValue);
         $product->html_demo = $html_demo;
    $html_demo = explode('-',$html_demo);
    foreach($html_demo as $demo){
        if(strlen($demo) == 6 && str_starts_with($demo,3)){
            $product->isDemo = true;            
            break;
        }
    }
       


        // $myfile = fopen("log.txt", "a") or die("Unable to open file!");
        // $txt = $product->name . "\n";
        // fwrite($myfile, $txt);
        // fclose($myfile);

        // split by 】 and get last
        $product->name = explode("】", $product->name)[count(explode("】", $product->name)) - 1];

        // ltrim
        $product->name = ltrim($product->name, "\t");

        // check product name has デモ機

       // $product->isDemo = strpos($product->name, "デモ機") !== false;



      
            $pattern = '/iPhone[^\s]+/u'; // regex pattern
            
            $info = explode('iPhone', $product->name);
            $product->model = $info[count($info) - 1];
            $product->model= 'iPhone'. explode("GB", $product->model)[0];
            // replace space
            $product->model = str_replace([" ","　 "],["",""],$product->model);

        if (preg_match($pattern, $product->name, $matches)) {
           
        }



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

    echo json_encode($products);


    addProduct($products);
}

$time_start = microtime(true);

$start = $_GET['start'] ;
$end = $_GET['end'];

if(empty($start))
    $start = 1;
    
    if(empty($end))
        $end = 10;



$file_log = "log_number_new.txt";
$file_log = fopen($file_log,"a");
$current_link = $_SERVER['REQUEST_URI'];


 for ($i = $start; $i <= $end; $i++) {
      $current_time = date('H:i:s d-m-Y');
         fwrite($file_log,json_encode([
             'current_time' => $current_time,
             'key'=>$time_start,
            'start'=>$start,
            'end' => $end,
            'time'=>time(),
            'turn'=>$i,
            'current_link'=>$current_link
                    ])."\n");
        echo "Crawl page $i <br>";
        crawl($i);
        sleep(5);
    }


die();
