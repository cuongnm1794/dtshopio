<?php
include "../db.php";
include "./helper.php";
libxml_use_internal_errors(true);

set_time_limit(0);
ini_set('display_errors', 1);

function curl_get($link)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

$url_iphone = "https://ec.treasure-f.com/search?category=1029&category2=1129&category3=1426_1427&size=grid&order=newarrival&number=90&step=11";


// Get model settings once at the start
$sql = "SELECT * FROM setting_model_treasures where name != '' ORDER BY priority ASC";
$result = $db->query($sql);
$modelSettings = $result->fetchAll();


function getModelByName($name)
{
    global $modelSettings;

    // echo "Getting model for: " . $name . "\n";
    // echo count($modelSettings) . "\n";

    foreach ($modelSettings as $model) {
        // explode 
        $keywords = explode(",", $model['keyword']);
        foreach ($keywords as $keyword) {
            // echo "Check ============> Key: " . $keyword . " |||| name: " . $name . " ||| vị trí " . (strpos($name, $keyword) ?? -1) . "\n";
            if (strpos($name, $keyword) !== false) {
                return $model['name'];
            }
        }
    }
    return '';
}



function crawl($url, $page = 1)
{
    global $db;
    $url = $url . "&step=" . $page;
    $html = curl_get($url);

    $dom = new DOMDocument();
    $dom->encoding = 'UTF-8';
    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

    $finder = new DomXPath($dom);
    $nodes = $finder->query("//section[@id='js-items_wrapper']//li[contains(@class, 'pj-search_item')]");

    $total = 0;
    foreach ($nodes as $node) {
        $total++;
    }

    if (!$total) {
        return false;
    }

    foreach ($nodes as $node) {
        $item = [
            'id_product' => '',
            'name' => '',
            'price' => '',
            'link' => '',
            'status' => '',
            'detail' => '',
            'model' => '',
            'store_name' => '',
            'store_link' => '',
        ];

        $finder = new DomXPath($node->ownerDocument);

        // Get link and ID
        $link_element = $finder->query(".//a", $node)[0];
        $item['link'] = $link_element->getAttribute("href");
        $id = explode("/", $item['link']);
        $item['id_product'] = end($id);

        // Get name
        $name_element = $finder->query(".//div[contains(@class, 'cm-itemlist_text_wrap')]", $node)[0];
        $item['name'] = trim($name_element->textContent);

        $item['name'] = str_replace("\n", '', trim($name_element->textContent));

        // Get price
        $price_element = $finder->query(".//p[contains(@class, 'cm-typo_head4 cm-itemlist_price')]", $node)[0];
        $item['price'] = str_replace(['￥', ','], '', $price_element->textContent);

        // Get status
        $status_element = $finder->query(".//div[contains(@class, 'cm-tag_area')]", $node);
        if ($status_element->length > 0) {
            $item['status'] = trim($status_element[0]->textContent);
            // remove 店頭受取可能	
            $item['status'] = str_replace('店頭受取可能', '', $item['status']);
        }

        // Add base URL if link is relative
        if (strpos($item['link'], 'http') === false) {
            $item['link'] = "https://ec.treasure-f.com" . $item['link'];
        }

        // Get store link and name
        $store_element = $finder->query(".//div[contains(@class, 'cm-itemlist_shop_link')]", $node)[0];
        $store_link = $store_element->getElementsByTagName('a')[0]->getAttribute('href');
        $store_name = $store_element->getElementsByTagName('span')[0]->textContent;

        $item['store_link'] = 'https://ec.treasure-f.com/' . $store_link;
        $item['store_name'] = trim($store_name);

        $item['model'] = getModelByName($item['name']);

        // Database operations remain the same
        $sql = "SELECT * FROM treasures WHERE id_product = '" . $item['id_product'] . "'";
        $result = $db->query($sql);

        if ($result->rowCount() == 0) {
            $sql = "INSERT INTO treasures (id_product, name, link, status, detail, last_price, model, store_name, store_link) VALUES ('" . $item['id_product'] . "', '" . $item['name'] . "', '" . $item['link'] . "', '" . $item['status'] . "', '" . $item['detail'] . "', '" . $item['price'] . "', '" . $item['model'] . "', '" . $item['store_name'] . "', '" . $item['store_link'] . "')";
            $db->query($sql);

            $sql = "INSERT INTO product_prices (id_product, price, site) VALUES ('" . $item['id_product'] . "', '" . $item['price'] . "', 'treasures')";
            $db->query($sql);
        } else {
            $sql = "SELECT * FROM product_prices WHERE id_product = '" . $item['id_product'] . "' AND site = 'treasures'";
            $result = $db->query($sql);

            if ($result->rowCount() > 0) {
                $row = $result->fetch(PDO::FETCH_ASSOC);
                if ($row['price'] != $item['price']) {
                    $sql = "INSERT INTO product_prices (id_product, price, site) VALUES ('" . $item['id_product'] . "', '" . $item['price'] . "', 'treasures')";
                    $db->query($sql);

                    $sql = "UPDATE treasures SET last_price = '" . $item['price'] . "' WHERE id_product = '" . $item['id_product'] . "'";
                    $db->query($sql);
                }
            }
        }






        // Price monitoring logic remains the same
        $sql = "SELECT * FROM treasures WHERE id_product = '" . $item['id_product'] . "'";
        $result = $db->query($sql);
        $item_product = $result->fetch(PDO::FETCH_ASSOC);

        if ($item_product['send_message'] == 1) {
            continue;
        }

        $sql = "SELECT * FROM setting_price_treasures";
        $result = $db->query($sql);
        $setting_prices = $result->fetchAll();

        foreach ($setting_prices as $setting_price) {
            if (empty($setting_price['keywords']) || empty($setting_price['price'])) {
                continue;
            }
            $keywords = explode(",", $setting_price['keywords']);
            foreach ($keywords as $keyword) {
                if ($item['model'] == $keyword) {
                    if ($item['price'] <= $setting_price['price']) {
                        $content = "Sản phẩm " . $item['name'] . " có giá " . number_format($item['price']) . " thấp hơn giá cài đặt " . number_format($setting_price['price']) . " của từ khóa " . $keyword . "\n Link: " . $item['link'];
                        sendMessage($content);

                        $sql = "UPDATE treasures SET send_message = 1 WHERE id_product = '" . $item['id_product'] . "'";
                        $db->query($sql);
                    }
                }
            }
        }
    }
    return true;
}


for ($page = 1; $page <= 10; $page++) {
    $result_check = crawl($url_iphone, $page);
    if (!$result_check) {
        break;
    }
}
