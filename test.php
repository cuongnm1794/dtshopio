<?php

include "db.php";
include "schema.php";


// loop 20 row and insert to setting_price
for ($i = 0; $i < 1000; $i++) {
    $sql = "INSERT INTO setting_price (price, keywords) VALUES ('0', '')";
    $db->query($sql);
}



// loop 20 row and insert to setting_price
for ($i = 0; $i < 1000; $i++) {
    $sql = "INSERT INTO setting_price_ec_geos (price, keywords) VALUES ('0', '')";
    $db->query($sql);
}



// loop 20 row and insert to setting_price
for ($i = 0; $i < 1000; $i++) {
    $sql = "INSERT INTO setting_price_surugas (price, keywords) VALUES ('0', '')";
    $db->query($sql);
}
