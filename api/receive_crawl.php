<?php
include "../db.php";
include "./helper.php";
// libxml_use_internal_errors(true); // Tắt cảnh báo
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

function crawl($products)
{

    global $db;


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
                        $link2 = "https://online.nojima.co.jp//commodity/1/" . $item_product['id_product'];
                        $content = "Sản phẩm " . $product->name . " có giá " . number_format($product->price) . " thấp hơn giá cài đặt " . number_format($setting_price['price']) . " của từ khóa " . $keyword . "\n Link: " . $link . "\n Link2: " . $link2;
                        sendMessage($content);

                        // update send_message in surugas
                        $sql = "UPDATE products SET send_message = 1 WHERE id = '" . $item_product['id'] . "'";
                        echo $sql;
                        $db->query($sql);
                        break;
                    }
                }
            }
        }
    }
}

$time_start = microtime(true);


$x = file_get_contents("php://input");
$xx = json_decode($x);
$data = $xx->products;
crawl($data);

echo "Time: " . (microtime(true) - $time_start);
die();
