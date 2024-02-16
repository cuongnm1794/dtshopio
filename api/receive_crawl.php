<?php
include "../db.php";
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
    }
}

$time_start = microtime(true);

$x= file_get_contents("php://input");
$xx = json_decode($x);
$data = $xx->products;
crawl($data);