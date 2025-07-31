<?php
session_start();
require_once "db_utils.php";
$db_utils = new DB_UTILS();
$limit =  $_GET["limit"] ?? 5;
$page = $_GET["page"] ??  1;
$offset = ($page -1) * $limit;

$tongdong = $db_utils->getValue("SELECT COUNT(*) FROM sanpham");
$sotrang = ceil($tongdong/$limit);
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user']['id'];
$dsDonHang = $db_utils->getAll("SELECT * FROM donhang WHERE nguoiDungID = ? ORDER BY ngayDat DESC", [$userId]);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <title>Quản lý sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
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
                                    <input placeholder="Search entire store here..." type="text">
                                    <button type="submit"><i class="ion-ios-search-strong"></i></button>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="logo">
                                <a href="index.php"><img src="assets/img/logo/logo.png" alt=""></a>
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
                                                        <td class="text-left">Tổng phụ</td>
                                                        <td class="text-right"><?= number_format($tongTien) ?>đ</td>
                                                    </tr>

                                                    <tr>
                                                        <td class="text-left">Total :</td>
                                                        <td class="text-right">$184.00</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="cart_button view_cart">
                                            <a href="cart.php">View Cart</a>
                                        </div>
                                        <div class="cart_button checkout">
                                            <a href="checkout.php">Checkout</a>
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
                        <a href="index.php"><img src="assets/img/logo/logo.png" alt=""></a>
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
        <div
            style="background-color: white; color: black; padding: 1rem; height: 100vh; width: 250px; border: 1px solid #ccc; border-radius: 8px;">
            <h2 class="text-center">Menu tài khoản</h2>
            <ul class="nav flex-column mt-4">
                <li class="nav-item"><a href="taikhoan.php" class="nav-link text-black"><i class="fas fa-user"></i>
                        Thông tin tài khoản</a></li>
                <li class="nav-item"><a href="dsyeuthich.php" class="nav-link text-black"><i class="fa fa-heart-o"></i>
                        Danh sách yêu thích</a></li>
                <li class="nav-item"><a href="diachi.php" class="nav-link text-black"><i class="fa fa-map-marker"></i>
                        Địa chỉ</a></li>
                <li class="nav-item"><a href="donhang.php" class="nav-link text-black"><i
                            class="fas fa-clipboard-list"></i> Đơn hàng</a></li>
                <li class="nav-item"><a href="magiamgiauser.php" class="nav-link text-black"><i class="fa fa-gift"></i>
                        Mã giảm giá</a></li>
                <li class="nav-item"><a href="index.php" class="nav-link text-black"><i class="fas fa-home"></i>
                        Trang chủ</a></li>
            </ul>
        </div>

        <!-- Main content -->
        <div class="p-4 flex-grow-1">
            <h1 class="nav-link text-black">Quản lý sản phẩm</h1>
            <?php foreach ($dsDonHang as $donHang): ?>
                 <?php 
                       $chiTiet = $db_utils->getAll("
    SELECT ct.*, sp.ten AS tenSP, asp.anh, kc.size AS tenSize
    FROM chitietdonhang ct
    JOIN kichco kc ON ct.bienTheID = kc.id
    JOIN sanpham sp ON kc.idSanPham = sp.id
    LEFT JOIN anhsanpham asp ON sp.id = asp.sanPhamID AND asp.anhChinh = 1
    WHERE ct.donHangID = ?
", [$donHang['id']]);?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ảnh</th>
                        <th>Tên</th>
                        <th>Size</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                   <?php foreach ($chiTiet as $ct):?>
                    <tr>

                        <td>#<?= $donHang['id'] ?></td>
                        <td><img src="<?= $ct['anh'] ?>" width="60"></td>
                        <td><?= $ct['tenSP'] ?></td>
                        <td><?= $ct['tenSize'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($donHang['ngayDat'])) ?></td>
                        <td><?= number_format($donHang['tongTien']) ?>đ</td>
                        <td>
                            <?php
                switch ($donHang['trangThai']) {
                    case 'cho_xac_nhan': echo 'Chờ xác nhận'; break;
                    case 'dang_xu_ly': echo 'Đang xử lý'; break;
                    case 'dang_giao': echo 'Đang giao'; break;
                    case 'hoan_thanh': echo 'Hoàn thành'; break;
                    case 'da_huy': echo 'Đã hủy'; break;
                    default: echo 'Không rõ'; break;
                }
                ?>
                        </td>
                    </tr>
<?php endforeach;?>
                </tbody>
            </table>
                <?php endforeach;?>
            <!-- Phân trang -->
            <nav>
                <ul class="pagination">
                    <?php for($index=1;$index<=$sotrang;$index++){?>
                    <li class="page-item <?= $index == $page ? "active":"" ?>">
                        <a class="page-link"
                            href="?<?=http_build_query(array_merge($_GET,["page"=>$index])) ?>"><?= $index ?></a>
                    </li>
                    <?php } ?>
                </ul>

            </nav>

        </div>
    </div>
    <script src="assets/js/plugins.js"></script>

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>
</body>

</html>