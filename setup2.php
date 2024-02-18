<?php

include "db.php";
include "schema.php";


// check if not exist key or key is not correct
if (!isset($_GET["key"]) || $_GET["key"] != "manhcuong") {
    die("Key không hợp lệ");
}

$db->query("DROP TABLE IF EXISTS ec_geos");
$db->query("DROP TABLE IF EXISTS surugas");


$schema = new Schema($db);
$ec_geos_table = [
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
    '`type`' => 'VARCHAR(255) NULL',
    '`rank`' => 'VARCHAR(255) NULL',
    'network' => 'VARCHAR(255) NULL',
    'imei' => 'VARCHAR(255) NULL',
    'capacity' => 'VARCHAR(255) NULL',
    'inventory' => 'VARCHAR(255) NULL',
    'last_price' => 'VARCHAR(255) NULL',
];

$schema->createTable("ec_geos", $ec_geos_table);


$surugas_table = [
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
];

$schema->createTable("surugas", $surugas_table);
try {
    $db->query("ALTER TABLE `product_prices` ADD `site` VARCHAR(255) NULL AFTER `id_product`");
} catch (Exception $e) {
    echo $e->getMessage();
}
// sql add columns site to product price
echo '<hr>';



echo "Tạo bảng thành công";
