<?php

include "db.php";
include "schema.php";

// check if not exist key or key is not correct
if (!isset($_GET["key"]) || $_GET["key"] != "manhcuong") {
    die("Key không hợp lệ");
}
$schema = new Schema($db);

$treasures_table = [
    "id" => "INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
    'id_product' => 'TEXT NOT NULL',
    "name" => "VARCHAR(255)  NULL",
    "link" => "TEXT NOT NULL",
    "status" => "VARCHAR(255)",
    "detail" => "TEXT NULL",
    "created_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
    "updated_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
    "status_buy" => "VARCHAR(255) NULL",
    'last_price' => 'VARCHAR(255) NULL',
    'store_name' => 'VARCHAR(255) NULL',
    'store_link' => 'TEXT NULL',
    'send_message' => 'INT(11) UNSIGNED NOT NULL DEFAULT 0',
    'model' => 'VARCHAR(255) NULL',
];

$schema->createTable("treasures", $treasures_table);

$treasure_price = [
    "id" => "INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
    "price" => "INT(11) UNSIGNED NOT NULL",
    "keywords" => "TEXT NOT NULL",
    "created_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
    "updated_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
];

$schema->createTable("setting_price_treasures", $treasure_price);


$setting_model_treasures = [
    "id" => "INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
    "name" => "VARCHAR(255) NOT NULL",
    "keyword" => "TEXT NOT NULL",
    "priority" => "INT(11) UNSIGNED NOT NULL",
];

$schema->createTable("setting_model_treasures", $setting_model_treasures);


echo "Cài đặt thành công!";
