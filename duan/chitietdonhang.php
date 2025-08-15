<?php
require_once "db_utils.php";
$db_util = new DB_UTILS();
$limit =  $_GET["limit"] ?? 5;
$page = $_GET["page"] ??  1;
$offset = ($page -1) * $limit;

$tongdong = $db_util->getValue("SELECT COUNT(*) FROM sanpham");
$sotrang = ceil($tongdong/$limit);

$id = $_GET['id'] ?? 0;
$donhang = $db_util->getOne("SELECT * FROM donhang WHERE id = ?", [$id]);

$chiTiet = $db_util->getAll("
    SELECT ctdh.*, kc.size, sp.ten AS tenSP
    FROM chitietdonhang ctdh
    JOIN kichco kc ON ctdh.bienTheID = kc.id
    JOIN sanpham sp ON kc.idSanPham = sp.id
    WHERE ctdh.donHangID = ?
", [$id]);
if(isset($_GET["sort"])){
    $sort = $_GET["sort"];
    if($sort == "asc"){
        $chiTiet = $db_util->getAll("
            SELECT ctdh.*, kc.size, sp.ten AS tenSP
            FROM chitietdonhang ctdh
            JOIN kichco kc ON ctdh.bienTheID = kc.id
            JOIN sanpham sp ON kc.idSanPham = sp.id
            WHERE ctdh.donHangID = ?
            ORDER BY ctdh.gia ASC
            LIMIT $limit OFFSET $offset
        ", [$id]);
    } elseif($sort == "desc"){
        $chiTiet = $db_util->getAll("
            SELECT ctdh.*, kc.size, sp.ten AS tenSP
            FROM chitietdonhang ctdh
            JOIN kichco kc ON ctdh.bienTheID = kc.id
            JOIN sanpham sp ON kc.idSanPham = sp.id
            WHERE ctdh.donHangID = ?
            ORDER BY ctdh.gia DESC
            LIMIT $limit OFFSET $offset
        ", [$id]);
    }
}
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
        <div
            style="background-color: white; color: black; padding: 1rem; height: 100vh; width: 250px; border: 1px solid #ccc; border-radius: 8px;">
            <h2 class="text-center">Admin</h2>
            <ul class="nav flex-column mt-4">
                <li class="nav-item"><a href="quanly.php" class="nav-link text-black"><i class="fa fa-chart-line"></i>
                        Dashboard</a></li>
                <li class="nav-item"><a href="quanly_sanpham.php" class="nav-link text-black"><i
                            class="fa fa-shirt"></i> Sản phẩm</a></li>
                <li class="nav-item"><a href="quanly_danhmuc.php" class="nav-link text-black"><i class="fa fa-tags"></i>
                        Danh mục</a></li>
                <li class="nav-item"><a href="quanly_donhang.php" class="nav-link text-black"><i class="fa fa-box"></i>
                        Đơn hàng</a></li>
                <li class="nav-item"><a href="quanly_khachhang.php" class="nav-link text-black"><i
                            class="fa fa-users"></i> Khách hàng</a></li>
                <li class="nav-item"><a href="quanly_magiamgia.php" class="nav-link text-black"><i
                            class="fa fa-gift"></i> Mã giảm giá</a></li>
                <li class="nav-item"><a href="reviews.html" class="nav-link text-black"><i class="fa fa-comments"></i>
                        Đánh giá</a></li>
                <li class="nav-item"><a href="settings.html" class="nav-link text-black"><i class="fa fa-cog"></i> Cài
                        đặt</a></li>
                <li class="nav-item"><a href="login.html" class="nav-link text-black"><i class="fa fa-sign-out"></i>
                        Đăng xuất</a></li>
            </ul>
        </div>

        <!-- Main content -->
        <div class="p-4 flex-grow-1">
            <h1 class="nav-link text-black">Quản lý sản phẩm</h1>
            <?php if (isset($_SESSION['message'])): ?>
            <div
                style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 5px;">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            <!-- Tìm kiếm & Sắp xếp -->
            <div class="row mb-3 align-items-center">
                <div class="col-md-4">
                    <form action="" method="get" class="d-flex">

                        <input type="text" class="form-control me-2" name="search"
                            placeholder="Tìm theo tên sản phẩm hoặc danh mục..."
                            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
                <div class="col-md-3">
                    <select name="sort" onchange="this.form.submit()" class="form-select">
                        <option value="">Sắp xếp theo giá</option>
                        <option value="asc">Sắp xếp theo giá tăng dần</option>
                        <option value="desc">Sắp xếp theo giảm giần </option>
                    </select>
                    </form>
                </div>
            </div>


            <!-- Bảng -->
            <form action="" method="get">
                <select name="limit" onchange="this.form.submit()" id="">
                    <option <?= isset($_GET["limit"]) && $_GET["limit"]==5 ? "selected":"" ?> value="5">5</option>
                    <option <?= isset($_GET["limit"]) && $_GET["limit"]==20 ? "selected":"" ?> value="20">20</option>
                    <option <?= isset($_GET["limit"]) && $_GET["limit"]==50 ? "selected":"" ?> value="50">50</option>
                    <option <?= isset($_GET["limit"]) && $_GET["limit"]==100 ? "selected":"" ?> value="100">100</option>
                </select>
            </form>
            <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
            <?php endif; ?>

            <table class="table table-bordered">
    <thead>
        <tr>
            <th>Tên sản phẩm</th>
            <th>Size</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Thành tiền</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($chiTiet as $ct): ?>
        <tr>
            <td><?= $ct['tenSP'] ?></td>
            <td><?= $ct['size'] ?></td>
            <td><?= number_format($ct['gia']) ?>đ</td>
            <td><?= $ct['soLuong'] ?></td>
            <td><?= number_format($ct['tong']) ?>đ</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
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