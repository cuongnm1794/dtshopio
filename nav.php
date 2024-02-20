 <!-- Sidebar -->
 <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

     <!-- Sidebar - Brand -->
     <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
         <div class="sidebar-brand-icon rotate-n-15">
             <i class="fas fa-laugh-wink"></i>
         </div>
         <div class="sidebar-brand-text mx-3">Tool Crawl and Order <sup>v1</sup></div>
     </a>

     <!-- Divider -->
     <hr class="sidebar-divider my-0">
     <!-- Nav Item - Dashboard -->
     <li class="nav-item <?= ($_SERVER['REQUEST_URI'] == '/') ? 'active' : ""; ?>">
         <a class="nav-link" href="/">
             <i class="fas fa-fw fa-tachometer-alt"></i>
             <span>Danh sách sản phẩm</span></a>
     </li>
     <li class="nav-item <?= ($_SERVER['REQUEST_URI'] == '/realtime.php') ? 'active' : ""; ?>">
         <a class="nav-link" href="/realtime.php">
             <i class="fas fa-fw fa-tachometer-alt"></i>
             <span>Có thể mua - realtime</span></a>
     </li>
     <li class="nav-item <?= ($_SERVER['REQUEST_URI'] == '/realtime2.php') ? 'active' : ""; ?>">
         <a class="nav-link" href="/realtime2.php">
             <i class="fas fa-fw fa-tachometer-alt"></i>
             <span>Có thể mua - realtime2</span></a>
     </li>

     <hr class="sidebar-divider">
     <li class="nav-item <?= ($_SERVER['REQUEST_URI'] == '/ec-geo.php') ? 'active' : ""; ?>">
         <a class="nav-link" href="/ec-geo.php">
             <i class="fas fa-fw fa-list"></i>
             <span>Ec.Geo-online</span></a>
     </li>
     <li class="nav-item <?= ($_SERVER['REQUEST_URI'] == '/suruga.php') ? 'active' : ""; ?>">
         <a class="nav-link" href="/suruga.php">
             <i class="fas fa-fw fa-list"></i>
             <span>Suruga</span></a>
     </li>


     <!-- Divider -->
     <hr class="sidebar-divider">

     <!-- Heading -->
     <div class="sidebar-heading">
         Cài đặt
     </div>
     <li class="nav-item <?= ($_SERVER['REQUEST_URI'] == '/setting_price.php') ? 'active' : ""; ?>">
         <a class="nav-link" href="/setting_price.php">
             <i class="fas fa-fw fa-chart-area"></i>
             <span>Cài đặt giá</span></a>
     </li>
     <li class="nav-item <?= ($_SERVER['REQUEST_URI'] == '/setting_price_ec-geo.php') ? 'active' : ""; ?>">
         <a class="nav-link" href="/setting_price_ec-geo.php">
             <i class="fas fa-fw fa-chart-area"></i>
             <span>Cài đặt giá Ec geo</span></a>
     </li>
     <li class="nav-item <?= ($_SERVER['REQUEST_URI'] == '/setting_price_suruga.php') ? 'active' : ""; ?>">
         <a class="nav-link" href="/setting_price_suruga.php">
             <i class="fas fa-fw fa-chart-area"></i>
             <span>Cài đặt giá Suruga</span></a>
     </li>
     <li class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], '/log.php') !== false) ? 'active' : ""; ?>">
         <a class="nav-link" href="/log.php">
             <i class="fas fa-fw fa-chart-area"></i>
             <span>Lịch sử giá</span></a>
     </li>
     <!-- Nav Item - Pages Collapse Menu -->
     <li class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], '/getImei.php') !== false) ? 'active' : ""; ?>">
         <a class="nav-link" href="/getImei.php">
             <i class="fas fa-fw fa-chart-area"></i>
             <span>Get Imei</span></a>
     </li>
     <!-- Divider -->

     <!-- Sidebar Toggler (Sidebar) -->
     <div class="text-center d-none d-md-inline">
         <button class="rounded-circle border-0" id="sidebarToggle"></button>
     </div>

 </ul>