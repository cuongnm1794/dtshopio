<?php


// show error
ini_set('display_errors', 1);

include "./header.php";
include "./nav.php";
include "./top.php";

// function curl get 
function curl_get($link)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

// check if has post
$imeis = [];


// echo $data;

// check if has post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // get imei from post
    $links = $_POST['link'];
    // split new line to array
    $links = explode("\r\n", $links);


    foreach ($links as $key => $link) {


        $data = curl_get($link);

        // get number last
        // write to file
        // file_put_contents("data_".$key.".txt", json_encode($link)." => ". $data);


        $re = '/>[0-9]{15}</m';
        preg_match_all($re, $data, $matches, PREG_SET_ORDER, 0);

        // check if not found
        if (count($matches) == 0) {
            $imeis[] = "not found";
            continue;
        }

        $imei = $matches[0][0];

        // replace > <
        $imei = str_replace(">", "", $imei);
        $imei = str_replace("<", "", $imei);
        $imeis[] = $imei;
    }
}


?>


<h1 class="h3 mb-2 text-gray-800">Quét imei</h1>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách</h6>
    </div>
    <div class="card-body">

        <form action="" method="POST">
            <div class="form-group">
                <label for="">Link</label>
                <textarea name="link" id="" cols="30" rows="10" class="form-control"><?php

                                                                                        if (isset($_POST['link'])) {
                                                                                            echo $_POST['link'];
                                                                                        }

                                                                                        ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Quét</button>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <?php foreach ($imeis as $imei) { ?>
                    <tr>
                        <td><?= $imei ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>


    </div>
</div>


<?php
include "./footer.php";
?>