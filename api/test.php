<?php 
include "../db.php";
libxml_use_internal_errors(true); // Tắt cảnh báo

// set time out
set_time_limit(0);

// show error
ini_set('display_errors', 1);


// function curl get 
function curl_get($link)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOTP_USERAGENT,"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function crawl($page = 1)
{

    global $db;

    $link = "https://online.nojima.co.jp";
    $html = curl_get($link);
    
    echo json_encode($html);
    
    file_put_contents("test11.html",$html);
    
    
}

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://online.nojima.co.jp/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "^\"^\u001f^^\b^");
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

$headers = array();
$headers[] = 'Authority: online.nojima.co.jp';
$headers[] = 'Accept: application/x-clarity-gzip';
$headers[] = 'Accept-Language: en-US,en;q=0.9,vi;q=0.8';
$headers[] = 'Cache-Control: no-cache';
$headers[] = 'Cookie: MUID=09DAF6EEE56E66FB2B4AE41FE4346777; ANON=A=45609B2B49DD1E88414EB0C7FFFFFFFF&E=1cff&W=1; NAP=V=1.9&E=1caf&C=FbmG3GRxChmTXLUDLXbcRV6wwLc3AOsHGn0UHnQ8HTqw0cO67oUP-g&W=1';
$headers[] = 'Pragma: no-cache';
$headers[] = 'Sec-Ch-Ua: ^^Not_A';
$headers[] = 'Sec-Ch-Ua-Mobile: ?0';
$headers[] = 'Sec-Ch-Ua-Platform: ^^Windows^^\"\"';
$headers[] = 'Sec-Fetch-Dest: empty';
$headers[] = 'Sec-Fetch-Mode: cors';
$headers[] = 'Sec-Fetch-Site: cross-site';
$headers[] = 'Sec-Fetch-User: ?1';
$headers[] = 'Upgrade-Insecure-Requests: 1';
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
$headers[] = 'Connection: keep-alive';
$headers[] = 'Origin: https://online.nojima.co.jp';
$headers[] = 'Referer: https://online.nojima.co.jp/';
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);
echo json_encode($result);

// $first = crawl(1);