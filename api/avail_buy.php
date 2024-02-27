<?php

include "../db.php";

// turn off debug
ini_set('display_errors', 1);

die("oke test");


// get all setting price
$sql = "SELECT * FROM setting_price where price > 0";
$result = $db->query($sql);
$prices = $result->fetchAll();

// get all products
$sql = "SELECT * FROM products where status = 'Available'";
$result = $db->query($sql);
$products = $result->fetchAll();
$products_buy = [];

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


    // get last price of product in log
    $sql = "SELECT * FROM product_prices WHERE id_product = '" . $product["id_product"] . "' ORDER BY id " . ($_GET["reverse"] ? "ASC" : "DESC") . " LIMIT 1";
    $result = $db->query($sql);
    $price_product = $result->fetch();
    $price_product = $price_product["price"];


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
}

// return json
echo json_encode(["data" => $products_buy]);
