<?php
include "./header.php";
include "./nav.php";
include "./top.php";
include "./db.php";
// get products with last price
$sql = "SELECT products.id_product, products.isDemo, products.name,products.status_buy, products.model, products.link, products.status, product_prices.price, products.created_at, products.updated_at  FROM products INNER JOIN product_prices ON products.id_product = product_prices.id_product WHERE product_prices.id IN (SELECT MAX(id) FROM product_prices GROUP BY id_product) 
ORDER BY product_prices.updated_at DESC";

$sql = "SELECT * from products order BY updated_at DESC";


$result = $db->query($sql);

$products = $result->fetchAll();
?>

<h1 class="h3 mb-2 text-gray-800">Danh sách sản phẩm</h1>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách</h6>
               

    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <th>Model</th>
                    <th>ID</th>
                    <th>Giá bán</th>
                    <th>Tên</th>
                    <th>Demo</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Ngày cập nhật</th>
                    <th>Trạng thái mua</th>
                    <th>Hành dộng</th>
                </thead>
                <tbody>
                    <?php foreach ($products as $product) { ?>
                        <tr>
                            <td><?= $product['model'] ?></td>
                            <td>
                                <a href="https://online.nojima.co.jp/app/catalog/detail/addcart/1/<?= $product['id_product'] ?>?quantity=1&shopCode=1&giftCode=99&optionCommodity=99&selectSkuCode=<?= $product['id_product'] ?>&reorderFlg=true&shippingShopCode=1&oldAddreessNo=0&shippingAddress=928782&deliveryTypeCode=0" target="_blank"><?= $product['id_product'] ?></a>
                                </td>
                            <td><?= $product['last_price'] ?></td>
                            <td>
                                <a href="https://online.nojima.co.jp/<?= $product['link'] ?>" target="_blank"><?= $product['name'] ?></a>
                            </td>
                            <td><?= $product['isDemo'] ?></td>
                            <td><?= $product['status'] ?></td>
                            <td><?=
                                // convert format to 09/08/17:00
                            //date('d/m H:i', strtotime($product['created_at']))
                                $product['created_at']
                                ?></td>
                            <td><?= date('d/m H:i', strtotime($product['updated_at'])) ?></td>
                            <td><?= $product["status_buy"] ?></td>
                            <td>
                                <!-- button view log -->
                                <a href="log.php?id=<?= $product['id_product'] ?>" class="btn btn-primary btn-sm">Xem
                                    log</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include "./footer.php";
?>