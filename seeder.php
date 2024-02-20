<?php

include "db.php";


// create 500 row of setting_price_surugas and setting_price_ec_geos

for ($i = 0; $i < 500; $i++) {
    $sql = "INSERT INTO setting_price_surugas (price, keywords) VALUES ('0', '')";
    $db->query($sql);
}

for ($i = 0; $i < 500; $i++) {
    $sql = "INSERT INTO setting_price_ec_geos (price, keywords) VALUES ('0', '')";
    $db->query($sql);
}
