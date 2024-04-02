<?php


// show error
ini_set('display_errors', 1);

include "./header.php";
include "./nav.php";
include "./top.php";


?>



<h1 class="h3 mb-2 text-gray-800">Tàu nhanh</h1>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách</h6>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="">Link</label>
            <textarea name="link" id="links" cols="30" rows="10" class="form-control"></textarea>
        </div>
        <button type="button" id="go" class="btn btn-primary">Đi tàu</button>

    </div>
</div>
<script>
    // add event button go
    document.getElementById("go").addEventListener("click", function() {

        console.log("go");

        // get links
        var links = document.getElementById("links").value;
        // split new line to array
        links = links.split("\n");


        // loop links
        links.forEach(function(link, key) {

            let link_sample = "https://online.nojima.co.jp/app/catalog/detail/addcart/1/" + link +
                "?quantity=1&shopCode=1&giftCode=99&optionCommodity=99&selectSkuCode=`+product.id_product+`&reorderFlg=true&shippingShopCode=1&oldAddreessNo=0&shippingAddress=928782&deliveryTypeCode=0"

            // open new tab
            window.open(link_sample, "_blank");


        });
    });
</script>

<?php
include "./footer.php";
?>