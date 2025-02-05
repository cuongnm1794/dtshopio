<?php
include "./db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['id'] as $key => $id) {
        $sql = "UPDATE setting_model_treasures SET name = '" . $_POST['name'][$key] . "', keyword = '" . $_POST['keyword'][$key] . "', priority = '" . $_POST['priority'][$key] . "' WHERE id = '" . $id . "'";
        $db->query($sql);
    }
    header("Refresh:0");
}

include "./header.php";
include "./nav.php";
include "./top.php";

$sql = "SELECT COUNT(*) as count FROM setting_model_treasures";
$result = $db->query($sql);
$row = $result->fetch();
if ($row['count'] < 100) {
    for ($i = 0; $i < 500; $i++) {
        $sql = "INSERT INTO setting_model_treasures (name, keyword, priority) VALUES ('', '', 0)";
        $db->query($sql);
    }
}

$sql = "SELECT * FROM setting_model_treasures ORDER BY priority DESC";
$result = $db->query($sql);
$models = $result->fetchAll();
?>



<h1 class="h3 mb-2 text-gray-800">Cài đặt Model</h1>


<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách</h6>
    </div>
    <div class="card-body">
        <form action="" method="POST">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <th>Tên Model</th>
                        <th>Từ khóa</th>
                        <th>Độ ưu tiên</th>
                    </thead>
                    <tbody>
                        <?php foreach ($models as $model) { ?>
                            <tr>
                                <td>
                                    <input type="hidden" name="id[]" value="<?= $model['id'] ?>">
                                    <input type="text" name="name[]" value="<?= $model['name'] ?>" class="form-control"
                                        placeholder="Nhập tên model">
                                </td>
                                <td>
                                    <input type="text" name="keyword[]" value="<?= $model['keyword'] ?>"
                                        class="form-control" placeholder="Nhập từ khóa">
                                </td>
                                <td>
                                    <input type="number" name="priority[]" value="<?= $model['priority'] ?>"
                                        class="form-control" placeholder="Nhập độ ưu tiên">
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<div id="accordion">
    <div class="card">
        <div class="card-header" id="headingOne">
            <h5 class="mb-0">
                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false"
                    aria-controls="collapseOne">
                    Ví dụ
                </button>
            </h5>
        </div>

        <div id="collapseOne" class="collapse " aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
                <h2>Ví dụ về các loại Model</h2>
                Mô tả: Hệ thống này sẽ sắp xếp các model theo độ ưu tiên từ thấp đến cao. Khi tên của một sản phẩm chứa
                một
                trong những từ khóa đã được thiết lập, hệ thống sẽ chọn tên model tương ứng cho sản phẩm đó.
                <div class="model-example">
                    <h3 class="model-title" style="color: blue;">Ví dụ 1: iPhone 12 Pro</h3>
                    <p>Mô tả: Nếu tên sản phẩm là "iPhone 12 Pro" và có hai dòng model như sau:</p>
                    <ul>
                        <li>iPhone 12 Pro Max chứa keyword: "iphone 12 pro max", mức độ ưu tiên là 1</li>
                        <li>iPhone 12 Pro chứa keyword: "iphone 12 pro", mức độ ưu tiên là 2</li>
                    </ul>
                    <p>Khi đó, hệ thống sẽ chọn model có độ ưu tiên cao hơn và có chứa từ khóa, tức là "iPhone 12 Pro"
                        cho
                        sản
                        phẩm
                        "iPhone 12 Pro" vì nó chứa từ khóa "iphone 12 pro" với độ ưu tiên cao nhất.</p>
                </div>
                <div class="model-example">
                    <h3 class="model-title" style="color: blue;">Ví dụ 2: iPhone 12 Pro</h3>
                    <p>Mô tả: Nếu tên sản phẩm là "iPhone 12 Pro" và có hai dòng model như sau:</p>
                    <ul>
                        <li>iPhone 12 Pro Max chứa keyword: "iphone 12 pro" và "iphone 12 pro max", mức độ ưu tiên là 1
                        </li>
                        <li>iPhone 12 Pro chứa keyword: "iphone 12 pro", mức độ ưu tiên là 2</li>
                    </ul>
                    <p>Khi đó, hệ thống sẽ chọn model có độ ưu tiên cao hơn và có chứa từ khóa, tức là "iPhone 12 Pro
                        Max"
                        cho
                        sản
                        phẩm
                        "iPhone 12 Pro" vì nó chứa từ khóa "iphone 12 pro" với độ ưu tiên cao nhất.</p>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
include "./footer.php";
?>