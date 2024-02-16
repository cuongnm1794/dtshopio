<?php

include "../db.php";

// get key in url
$key = $_GET["key"];

// check if not equal J7pR9kEw5XqL3u2yV1Z4
if ($key != "J7pR9kEw5XqL3u2yV1Z4") {
    die("Key không hợp lệ");
}

// get data from request
$data = json_decode(file_get_contents('php://input'), true);


// get id product
$id_product = $data["id_product"];

// get message
$message = $data["message"];

// get status
$status = $data["status"];

// check if not exist id_product or message or status
if (!$id_product || !$message || !$status) {
    die("Thiếu thông tin");
}


// get product
$sql = "SELECT * FROM products WHERE id_product = '" . $id_product . "'";
$result = $db->query($sql);
$product = $result->fetch();

// check if not exist product
if (!$product) {
    die("Không tồn tại sản phẩm");
}

if ($status) {
    // update status bought and status buy = message
    $sql = "UPDATE products SET status = 'Bought', status_buy = '" . $message . "', time_buy ='" . date('Y-m-d H:i:s') . "' WHERE id_product = '" . $id_product . "'";
    $db->query($sql);
} else {
    // update status bought and status buy = message
    $sql = "UPDATE products SET status = 'Available', status_buy = '" . $message . "' WHERE id_product = '" . $id_product . "'";
    $db->query($sql);
}
