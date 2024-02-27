<?php

include "../db.php";
libxml_use_internal_errors(true);

// turn off debug
ini_set('display_errors', 1);

$time_start = microtime(true);
// get all setting price
$sql = "SELECT * FROM setting_price where price > 0";
$result = $db->query($sql);
$prices = $result->fetchAll();

$time_end_get_setting = microtime(true);
// write log to 
file_put_contents("log_debug.txt", "Time get setting price: " . ($time_end_get_setting - $time_start) . "\n", FILE_APPEND);



// get all products
$sql = "SELECT * FROM products where status = 'Available'";
$result = $db->query($sql);
$products = $result->fetchAll();
$products_buy = [];

$time_end_get_products = microtime(true);
// write log to
file_put_contents("log_debug.txt", "Time get products: " . ($time_end_get_products - $time_end_get_setting) . "\n", FILE_APPEND);

// get all product prices
$sql = "SELECT * FROM product_prices";
$result = $db->query($sql);
$product_prices = $result->fetchAll();

$time_end_get_product_prices = microtime(true);

// write log to
file_put_contents("log_debug.txt", "Time get product prices: " . ($time_end_get_product_prices - $time_end_get_products) . "\n", FILE_APPEND);

// loop all products
foreach ($products as $product) {
    // check if product name has ソフトバンク -> continue

    // check if product isdemo
    if ($product["isDemo"] == 1) {
        if (strpos($product["name"], "ソフトバンク") !== false) {
            continue;
        }

        if (strpos($product["name"], "SB") !== false) {
            continue;
        }
    }


    // // get last price of product in log
    // $sql = "SELECT * FROM product_prices WHERE id_product = '" . $product["id_product"] . "' ORDER BY id " . ($_GET["reverse"] ? "ASC" : "DESC") . " LIMIT 1";
    // $result = $db->query($sql);
    // $price_product = $result->fetch();
    // $price_product = $price_product["price"];

    // find price_product
    $price_product = 0;
    if($_GET["reverse"]){
        foreach ($product_prices as $product_price) {
            if($product_price["id_product"] == $product["id_product"]){
                $price_product = $product_price["price"];
                break;
            }
        }
    }else{
        foreach (array_reverse($product_prices) as $product_price) {
            if($product_price["id_product"] == $product["id_product"]){
                $price_product = $product_price["price"];
                break;
            }
        }
    }

    // loop all price
    foreach ($prices as $price) {

        // keywords
        $keywords = explode(",", $price["keywords"]);

        // loop all keywords
        foreach ($keywords as $keyword) {

            // check if keyword in name
            if ($product["model"] == $keyword) {

                // check if price < price in setting price
                if ($price_product <= $price["price"] && $price["isDemo"] == $product["isDemo"]) {
                    $product["price"] = $price_product;
                    // push product to products_buy
                    $products_buy[] = $product;
                }
            }
        }
    }


//     // loop all price
//     foreach ($prices as $price) {

//         // keywords
//         $keywords = explode(",", $price["keywords"]);

//         // loop all keywords
//         foreach ($keywords as $keyword) {

//             // check if keyword in name
//             if ($product["model"] == $keyword) {

//                 // check if price < price in setting price
//                 if ($price_product <= $price["price"] && $price["isDemo"] == $product["isDemo"]) {
//                     $product["price"] = $price_product;
//                     // push product to products_buy
//                     $products_buy[] = $product;
//                 }
//             }
//         }
//     }
// }

// return json
echo json_encode(["data" => $products_buy]);