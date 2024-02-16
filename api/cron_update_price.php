<?php
include "../db.php";
libxml_use_internal_errors(true); // Tắt cảnh báo

// set time out
set_time_limit(0);

// show error
ini_set('display_errors', 1);

$sql = "select * from products";

$result = $db->query($sql);

$products = $result->fetchAll();
foreach($products as $product ){
    
    
    $sql1 = "select price from product_prices where id_product = '".$product['id_product']."' order by id desc";
    $result = $db->query($sql1);
    $price = $result->fetchColumn();
    
    $sql_update = "update products set last_price = '$price' where id = '".$product['id']."'";
    
    $db->query($sql_update);
}