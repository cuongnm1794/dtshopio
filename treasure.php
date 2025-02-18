<?php
include "./header.php";
include "./nav.php";
include "./top.php";
include "./db.php";

// get products with last price
$sql = "SELECT * from treasures order BY updated_at DESC";

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
                    <th>ID</th>
                    <th>Model</th>
                    <th>Giá bán</th>
                    <th>Tên</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Ngày cập nhật</th>
                    <th>Ghi chú</th>
                    <th>Store Name</th>
                    <th>Hành động</th>
                </thead>
                <tbody>
                    <?php foreach ($products as $product) { ?>
                        <tr>
                            <td>
                                <a href="<?= $product['link'] ?>" target="_blank"><?= $product['id_product'] ?></a>
                            </td>
                            <td><?= $product['model'] ?></td>
                            <td><?= $product['last_price'] ?></td>
                            <td>
                                <a href="<?= $product['link'] ?>" target="_blank"><?= $product['name'] ?></a>
                            </td>
                            <td><?= $product['status'] ?></td>
                            <td><?= $product['created_at'] ?></td>
                            <td><?= date('d/m H:i', strtotime($product['updated_at'])) ?></td>
                            <td><?= $product['detail'] ?></td>
                            <td>
                                <a href="<?= $product['store_link'] ?>" target="_blank"><?= $product['store_name'] ?></a>
                            </td>
                            <td>
                                <a href="log.php?id=<?= $product['id_product'] ?>&site=treasure"
                                    class="btn btn-primary btn-sm">Xem log</a>
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