<?php
require_once './class/FMI.php';
$fmi = new FMI;
$appleID = strtolower($_GET["appleID"]);
echo $fmi->apiRemove($appleID);
