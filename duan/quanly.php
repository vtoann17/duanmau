<?php
session_start();
require_once "db_utils.php";
$db_util = new DB_UTILS();

$sanphams = $db_util->getAll("
    SELECT sp.*, dm.tenDanhMuc AS tenDM 
    FROM sanpham sp 
    LEFT JOIN danhmuc dm ON sp.danhMucID = dm.id 
");

$donhangs = $db_util->getAll("
    SELECT * FROM donhang 
    ORDER BY ngayDat DESC
");

$nguoidungs = $db_util->getAll("
    SELECT * FROM nguoidung 
    ORDER BY ngayTao DESC
");

$danhmucs = $db_util->getAll("
    SELECT * FROM danhmuc 
    ORDER BY id DESC
");
$doanhThuNgay = $db_util->getOne("
    SELECT SUM(tongTien) AS total 
    FROM donhang 
    WHERE DATE(ngayDat) = CURDATE()
")['total'] ?? 0;

$doanhThuTuan = $db_util->getOne("
    SELECT SUM(tongTien) AS total 
    FROM donhang 
    WHERE YEARWEEK(ngayDat, 1) = YEARWEEK(CURDATE(), 1)
")['total'] ?? 0;

$doanhThuThang = $db_util->getOne("
    SELECT SUM(tongTien) AS total 
    FROM donhang 
    WHERE YEAR(ngayDat) = YEAR(CURDATE()) 
      AND MONTH(ngayDat) = MONTH(CURDATE())
")['total'] ?? 0;
$donHangMoiNhat = $db_util->getAll("
    SELECT dh.*, nd.ten AS tenKH
    FROM donhang dh
    JOIN nguoidung nd ON dh.nguoiDungID = nd.id
    ORDER BY dh.ngayDat DESC
    LIMIT 10
");
$sanPhamBanChay = $db_util->getAll("
    SELECT sp.id, sp.ten, SUM(ct.soLuong) AS tongBan
    FROM chitietdonhang ct
    JOIN kichco kc ON ct.bienTheID = kc.id
    JOIN sanpham sp ON kc.idSanPham = sp.id
    GROUP BY sp.id, sp.ten
    ORDER BY tongBan DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Fashion eCommerce HTML Template</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">

    <!-- CSS 
    ========================= -->


    <!-- Plugins CSS -->
    <link rel="stylesheet" href="assets/css/plugins.css">

    <!-- Main Style CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>
    <header class="header_area header_three">
        <!--header top start-->
        <div class="header_top">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-lg-7 col-md-12">

                    </div>
                    <div class="col-lg-5 col-md-12">
                        <div class="top_right text-right">
                            <ul>
                                <li class="top_links"><a href="#">
                                        <?php
        if (isset($_SESSION['user'])) {
            echo $_SESSION['user']['vaiTro'] == 'admin' ? 'Admin' : $_SESSION['user']['ten'];
        } else {
            echo 'Tài khoản của tôi';
        }
     ?>
                                        <i class="ion-chevron-down"></i></a>
                                    <ul class="dropdown_links">
                                        <?php if (isset($_SESSION['user'])): ?>
                                        <?php if ($_SESSION['user']['vaiTro'] == 'admin'): ?>
                                        <li><a href="quanly.php">Quản lý cửa hàng</a></li>
                                        <li><a href="logout.php">Đăng xuất</a></li>
                                        <?php else: ?>
                                        <li><a href="taikhoan.php">Thông tin tài khoản</a></li>
                                        <li><a href="logout.php">Đăng xuất</a></li>
                                        <?php endif; ?>
                                        <?php else: ?>
                                        <li><a href="login.php">Đăng nhập</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--header top start-->

        <!-- Đổ dữ liệu vào giỏ hàng -->
        <div class="header_middel">
            <div class="container-fluid">
                <div class="middel_inner">
                    <div class="row align-items-center">
                        <div class="col-lg-4">
                            <div class="search_bar">
                                <form action="#">
                                    <input placeholder="Tìm kiếm sản phẩm..." type="text">
                                    <button type="submit"><i class="ion-ios-search-strong"></i></button>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="logo">
                                <a href="index.php"><img src="assets/img/logo/logo2.png" alt=""></a>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="cart_area">
                                <div class="cart_link">
                                    <a href="#"><i class="fa fa-shopping-basket"></i></a>
                                    <!--mini cart-->
                                    <div class="mini_cart">
                                        <?php $tongTien = 0;
                                     foreach($gioHang as $gh):
                                     $tongTien += $gh['thanhTien'];
                                     $anh = $db_utils->getOne("SELECT anh FROM anhsanpham WHERE sanPhamID = ? AND anhChinh = 1", [$gh['sanPhamID']]); ?>
                                        <div class="cart_item top">

                                            <div class="cart_img">
                                                <a href="#"><img src="<?= $anh['anh']?>" alt=""></a>
                                            </div>
                                            <div class="cart_info">
                                                <a href="#"><?= $gh['tensp']?></a>

                                                <span><?= $gh['soLuong']?> </span>
                                                <span><?= number_format($gh['gia'])?>đ</span>

                                            </div>
                                            <div class="cart_remove">
                                                <a href="cart.php?delete_id=<?= $gh['id'] ?>"><i
                                                        class="ion-android-close"></i></a>
                                            </div>
                                        </div><?php endforeach;?>
                                        <div class="cart__table">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-left">Tổng phụ:</td>
                                                        <td class="text-right"><?= number_format($tongTien) ?>đ</td>
                                                    </tr>

                                                    <tr>
                                                        <td class="text-left">Tổng cộng :</td>
                                                       <td class="text-right"><?= number_format($tongTien) ?>đ</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="cart_button view_cart">
                                            <a href="cart.php">Giỏ hàng</a>
                                        </div>
                                        <div class="cart_button checkout">
                                            <a href="dathang.php">Thanh toán</a>
                                        </div>
                                    </div>
                                    <!--mini cart end-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="horizontal_menu">
                    <div class="left_menu">
                        <div class="main_menu">
                            <nav>
                                <ul>
                                    <li class="active"><a href="index.php">Trang chủ <i
                                                class="fa fa-angle-down"></i></a>
                                    </li>
                                    <li class="mega_items"><a href="products.php">Sản phẩm <i
                                                class="fa fa-angle-down"></i></a>
                                    </li>
                                    <li><a href="blog.php">Blog <i class="fa fa-angle-down"></i></a>
                                    </li>
                                    <li><a href="#">Trang <i class="fa fa-angle-down"></i></a>
                                        <ul class="sub_menu pages">
                                            <li><a href="about.php">Giới thiệu</a></li>
                                            <li><a href="login.php">Đăng nhập</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="logo_container">
                        <a href="index.php"><img src="assets/img/logo/logo2.png" alt=""></a>
                    </div>
                    <div class="right_menu">
                        <div class="main_menu">
                            <nav>
                                <ul>
                                    <li><a href="#">Đặc biệt</a></li>
                                    <li><a href="about.php">Giới thiệu</a></li>
                                    <li><a href="contact.php">Liên hệ</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--header middel end-->

        <!--header bottom satrt-->
        <div class="header_bottom sticky-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-12">
                        <div class="main_menu_inner">
                            <div class="main_menu">
                                <nav>
                                    <ul>
                                        <li class="active"><a href="index.php">Trang chủ </a></li>
                                        <li><a href="products.php">Sản phẩm </a></li>
                                        <li><a href="about.php">Giới thiệu</a></li>
                                        <li><a href="#">Trang <i class="fa fa-angle-down"></i></a>
                                            <ul class="sub_menu pages">
                                                <li><a href="about.php">Giới thiệu</a></li>
                                                <li><a href="login.php">Đăng nhập</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="blog.php">blog</a></li>

                                        <li><a href="contact.php">Liên hệ</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--header bottom end-->
    </header>
    <div class="d-flex">
    <!-- Sidebar -->
    <div style="background-color: white; color: black; padding: 1rem; height: 100vh; width: 250px; border: 1px solid #ccc; border-radius: 8px;">
      <h2 class="text-center">Admin</h2>
      <ul class="nav flex-column mt-4">
        <li class="nav-item"><a href="quanly.php" class="nav-link text-black"><i class="fa fa-chart-line"></i> Thống kê</a></li>
        <li class="nav-item"><a href="quanly_sanpham.php" class="nav-link text-black"><i class="fa fa-shirt"></i> Sản phẩm</a></li>
        <li class="nav-item"><a href="quanly_danhmuc.php" class="nav-link text-black"><i class="fa fa-tags"></i> Danh mục</a></li>
        <li class="nav-item"><a href="quanly_donhang.php" class="nav-link text-black"><i class="fa fa-box"></i> Đơn hàng</a></li>
        <li class="nav-item"><a href="quanly_khachhang.php" class="nav-link text-black"><i class="fa fa-users"></i> Khách hàng</a></li>
        <li class="nav-item"><a href="quanly_magiamgia.php" class="nav-link text-black"><i class="fa fa-gift"></i> Mã giảm giá</a></li>
        <li class="nav-item"><a href="reviews.html" class="nav-link text-black"><i class="fa fa-comments"></i> Đánh giá</a></li>
        <li class="nav-item"><a href="settings.html" class="nav-link text-black"><i class="fa fa-cog"></i> Cài đặt</a></li>
        <li class="nav-item"><a href="login.html" class="nav-link text-black"><i class="fa fa-sign-out"></i> Đăng xuất</a></li>
      </ul>
    </div>

    <!-- Main content -->
    <div style="flex: 1; padding: 1rem;">
        <div class="row">
            <div class="col-md-4">
                <div class="card text-bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Doanh thu hôm nay</h5>
                        <p class="card-text"><?= number_format($doanhThuNgay) ?> VNĐ</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Doanh thu tuần</h5>
                        <p class="card-text"><?= number_format($doanhThuTuan) ?> VNĐ</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Doanh thu tháng</h5>
                        <p class="card-text"><?= number_format($doanhThuThang) ?> VNĐ</p>
                    </div>
                </div>
            </div>
        </div>

        <h3>Top 10 đơn hàng mới nhất</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donHangMoiNhat as $dh): ?>
                    <tr>
                        <td><?= $dh['id'] ?></td>
                        <td><?= $dh['tenKH'] ?></td>
                        <td><?= $dh['ngayDat'] ?></td>
                        <td><?= number_format($dh['tongTien']) ?> VNĐ</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h3>Top 10 sản phẩm bán chạy</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Số lượng bán</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sanPhamBanChay as $sp): ?>
                    <tr>
                        <td><?= $sp['ten'] ?></td>
                        <td><?= $sp['tongBan'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Plugins JS -->
    <script src="assets/js/plugins.js"></script>

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>
</body>

</html>