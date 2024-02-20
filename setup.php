<?php

include "db.php";
include "schema.php";


// check if not exist key or key is not correct
if (!isset($_GET["key"]) || $_GET["key"] != "manhcuong") {
    die("Key không hợp lệ");
}

$db->query("DROP TABLE IF EXISTS products");
$db->query("DROP TABLE IF EXISTS product_prices");
$db->query("DROP TABLE IF EXISTS setting_price");

$schema = new Schema($db);
$products_table = [
    "id" => "INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
    'id_product' => 'TEXT NOT NULL',
    "name" => "VARCHAR(255)  NULL",
    "link" => "TEXT NOT NULL",
    "status" => "VARCHAR(255)",
    "model" => "VARCHAR(255)",
    "created_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
    "updated_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
    "status_buy" => "VARCHAR(255) NULL",
    "time_buy" => "TIMESTAMP NULL",
    "isDemo" => "INT(11) UNSIGNED NOT NULL DEFAULT 0"
];

$schema->createTable("products", $products_table);

$product_prices_table = [
    "id" => "INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
    'id_product' => 'TEXT NOT NULL',
    "price" => "INT(11) UNSIGNED NOT NULL",
    "created_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
    "updated_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"

];

$schema->createTable("product_prices", $product_prices_table);


$setting_price = [
    "id" => "INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
    "price" => "INT(11) UNSIGNED NOT NULL",
    "keywords" => "TEXT NOT NULL",
    "created_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
    "updated_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
    "isDemo" => "INT(11) UNSIGNED NOT NULL DEFAULT 0"
];

$schema->createTable("setting_price", $setting_price);

// loop 20 row and insert to setting_price
for ($i = 0; $i < 1000; $i++) {
    $sql = "INSERT INTO setting_price (price, keywords) VALUES ('0', '')";
    $db->query($sql);
}
