<?php
include "./db.php";

// check if method post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // loop all id
    foreach ($_POST['id'] as $key => $id) {
        // update price and keywords
        $sql = "UPDATE setting_price_surugas SET price = '" . $_POST['price'][$key] . "', keywords = '" . $_POST['keywords'][$key] . "' WHERE id = '" . $id . "'";
        $db->query($sql);
    }

    // refresh page
    header("Refresh:0");
}

include "./header.php";
include "./nav.php";
include "./top.php";

// get all setting price
$sql = "SELECT * FROM setting_price_surugas";
$result = $db->query($sql);
$products = $result->fetchAll();


?>

<h1 class="h3 mb-2 text-gray-800">Danh sách sản phẩm</h1>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách</h6>
    </div>
    <div class="card-body">
        <form action="" method="POST">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <th>Keywords ( cách nhau bằng dấu "," )</th>
                        <th>Giá có thể mua</th>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product) { ?>
                        <tr>
                            <td>
                                <input type="hidden" name="id[]" value="<?= $product['id'] ?>">
                                <input type="text" name="keywords[]" value="<?= $product['keywords'] ?>"
                                    class="form-control" placeholder="Nhập từ khóa">
                            </td>

                            <td>
                                <input type="text" name="price[]" value="<?= $product['price'] ?>" class="form-control"
                                    placeholder="Nhập giá có thể mua">
                            </td>

                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- button save center -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>

        </form>
    </div>



</div>

<?php
include "./footer.php";
?>