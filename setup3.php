<?php

include "db.php";
include "schema.php";


// check if not exist key or key is not correct
if (!isset($_GET["key"]) || $_GET["key"] != "manhcuong") {
    die("Key không hợp lệ");
}
$schema = new Schema($db);

$setting_price = [
    "id" => "INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
    "price" => "INT(11) UNSIGNED NOT NULL",
    "keywords" => "TEXT NOT NULL",
    "created_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
    "updated_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
];

$schema->createTable("setting_price_ec_geos", $setting_price);

$schema->createTable("setting_price_surugas", $setting_price);

// add column send noti to products
$db->query("ALTER TABLE products ADD COLUMN send_message INT(11) UNSIGNED NOT NULL DEFAULT 0");

// add column send noti to ec_geo
$db->query("ALTER TABLE ec_geos ADD COLUMN send_message INT(11) UNSIGNED NOT NULL DEFAULT 0");

// add column send noti to suruga
$db->query("ALTER TABLE surugas ADD COLUMN send_message INT(11) UNSIGNED NOT NULL DEFAULT 0");

// add column model to surugas
$db->query("ALTER TABLE surugas ADD COLUMN model VARCHAR(255) NULL");

echo "Cài đặt thành công!";
