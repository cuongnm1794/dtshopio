<?php
include "./header.php";
include "./nav.php";
include "./top.php";
include "./db.php";

?>

<h1 class="h3 mb-2 text-gray-800">Danh sách sản phẩm</h1>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách</h6>
    </div>
    <div class="card-body">
        <div id="app">
            <p>Last updated: {{ lastUpdate }}</p>
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
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
                        <tr v-for="product in products" :key="product.id">
                            <td>{{ product.model }}</td>
                            <td><a :href="`https://online.nojima.co.jp/app/catalog/detail/addcart/1/`+product.id_product+`?quantity=1&shopCode=1&giftCode=99&optionCommodity=99&selectSkuCode=`+product.id_product+`&reorderFlg=true&shippingShopCode=1&oldAddreessNo=0&shippingAddress=928782&deliveryTypeCode=0`" target="_blank">{{ product.id_product }}</a>
                            </td>
                            <td>{{ product.price }}</td>
                            <td>
                                <a :href="`https://online.nojima.co.jp/`+product.link"
                                    target="_blank">{{ product.name }}</a>
                            </td>
                            <td>{{ product.isDemo }}</td>
                            <td>{{ product.status }}</td>
                            <td>
                                {{ product.created_at}}
                            </td>
                            <td>{{ product.updated_at}}</td>
                            <td>{{ product.status_buy }}</td>
                            <td>
                                <a :href="`log.php?id=`+product.id_product" class="btn btn-primary btn-sm">Xem
                                    log</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


    </div>
</div>



<?php
include "./footer.php";
?>
<script src="https://cdn.jsdelivr.net/npm/axios@0.21.1/dist/axios.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<script>
var app = new Vue({
    el: '#app',
    data: {
        message: 'Xin chào từ Vue!',
        products: [],
        lastUpdate: ''
    },
    created: function() {
        this.loadDataFromApi(); // Gọi lần đầu
        setInterval(this.loadDataFromApi, 5000);
    },
    methods: {
        loadDataFromApi: function() {

            this.lastUpdate = new Date().toLocaleString();

            axios.get('/api/avail_buy2.php')
                .then(response => {
                    // handle success
                    console.log(response.data.data);
                    this.products = response.data.data;
                })
                .catch(function(error) {
                    // handle error
                    console.log(error);
                })
        }
    }
});
</script>