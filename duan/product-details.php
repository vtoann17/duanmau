<?php
session_start();
require_once "db_utils.php";
$db_util = new DB_UTILS();

$tbloi = '';

// Lấy ID sản phẩm từ URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin sản phẩm chính và kiểm tra tồn tại
$sanPham = $db_util->getOne("SELECT sp.*, dm.tenDanhMuc 
                        FROM sanpham sp 
                        LEFT JOIN danhmuc dm ON sp.danhMucID = dm.id 
                        WHERE sp.id = ?", [$id]);
if (!$sanPham) {
    die("Không tìm thấy sản phẩm!");
}

// Lấy ảnh sản phẩm
$anhSP = $db_util->getOne("SELECT anh FROM anhsanpham WHERE sanPhamID = ? AND anhChinh = 1", [$id]);
$anhPhu = $db_util->getAll("SELECT anh FROM anhsanpham WHERE sanPhamID = ? AND anhChinh = 0", [$id]);

// Lấy kích cỡ
$kichco = $db_util->getAll("SELECT * FROM kichco WHERE idSanPham = ?", [$id]);

// Lấy sản phẩm liên quan (trừ sản phẩm hiện tại)
$sanPhamLienQuan = $db_util->getAll("SELECT * FROM sanpham WHERE danhMucID = ? AND id != ? LIMIT 8", [$sanPham['danhMucID'], $id]);

// Lấy sản phẩm upsell
$upsellSanPham = $db_util->getAll("SELECT * FROM sanpham WHERE id != ? ORDER BY RAND() LIMIT 4", [$id]);

// ======= XỬ LÝ THÊM VÀO GIỎ HÀNG =======
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
    if (empty($_SESSION["user"])) {
        header('Location: login.php');
        exit();
    }

    $nguoiDungID = $_SESSION["user"]["id"];
    $sanPhamID = intval($_POST['sanPhamID']);
    $kichCoID = isset($_POST['kichCoID']) ? intval($_POST['kichCoID']) : 1;
    $soLuong = isset($_POST['soLuong']) ? intval($_POST['soLuong']) : 1;

    if (empty($kichCoID)) {
        echo "Vui lòng chọn size!";
        exit();
    }
    $kichCo = $db_util->getOne("SELECT soLuong FROM kichco WHERE id = ?", [$kichCoID]);
    if (!$kichCo) {
        $tbloi = "Size không tồn tại!";
    } elseif ($soLuong > $kichCo['soLuong']) {
        $tbloi = "Số lượng vượt quá tồn kho!";
    } else {
        $cart = $db_util->getOne(
            "SELECT * FROM giohang WHERE nguoiDungID = ? AND sanPhamID = ? AND kichCoID = ?",
            [$nguoiDungID, $sanPhamID, $kichCoID]
        );

        if (!$cart) {
            $sp = $db_util->getOne("SELECT gia FROM sanpham WHERE id = ?", [$sanPhamID]);
            $gia = $sp ? $sp['gia'] : 0;

            $db_util->execute(
                "INSERT INTO giohang (nguoiDungID, sanPhamID, kichCoID, soLuong, gia) VALUES (?, ?, ?, ?, ?)",
                [$nguoiDungID, $sanPhamID, $kichCoID, $soLuong, $gia]
            );
        } else {
            $new_quantity = $cart['soLuong'] + $soLuong;
            if ($new_quantity > $kichCo['soLuong']) {
                $tbloi = "Số lượng vượt quá tồn kho!";
            } else {
                $db_util->execute("UPDATE giohang SET soLuong = ? WHERE id = ?", [$new_quantity, $cart['id']]);
            }
        }
    }
     if (empty($tbloi)) {
        header("Location: cart.php");
        exit();
    }
}

// ======= XÓA SẢN PHẨM KHỎI GIỎ HÀNG =======
if (isset($_GET['delete_id']) && !empty($_SESSION['user'])) {
    $idCanXoa = intval($_GET['delete_id']);
    $nguoiDungID = $_SESSION['user']['id'];
    $db_util->execute("DELETE FROM giohang WHERE id = ? AND nguoiDungID = ?", [$idCanXoa, $nguoiDungID]);
    header("Location: cart.php");
    exit();
}

// ======= LẤY DỮ LIỆU GIỎ HÀNG =======
$gioHang = [];
if (!empty($_SESSION['user'])) {
    $nguoiDungID = $_SESSION['user']['id'];
    $gioHang = $db_util->getAll("
        SELECT 
            gh.id,
            gh.sanPhamID,
            sp.ten AS tensp,
            kc.size,
            gh.soLuong,
            gh.gia,
            (gh.soLuong * gh.gia) AS thanhTien
        FROM giohang gh
        JOIN sanpham sp ON gh.sanPhamID = sp.id
        JOIN kichco kc ON gh.kichCoID = kc.id
        WHERE gh.nguoiDungID = ?
    ", [$nguoiDungID]);
}
if (empty($_SESSION["user"])) {
    header('Location: login.php');
    exit();
}
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Product details</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <!--header area start-->
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
                                        foreach ($gioHang as $gh):
                                            $tongTien += $gh['thanhTien'];
                                            $anh = $db_util->getOne("SELECT anh FROM anhsanpham WHERE sanPhamID = ? AND anhChinh = 1", [$gh['sanPhamID']]); ?>
                                            <div class="cart_item top">

                                                <div class="cart_img">
                                                    <a href="#"><img src="<?= $anh['anh'] ?>" alt=""></a>
                                                </div>
                                                <div class="cart_info">
                                                    <a href="#"><?= $gh['tensp'] ?></a>

                                                    <span><?= $gh['soLuong'] ?> </span>
                                                    <span><?= number_format($gh['gia']) ?>đ</span>

                                                </div>
                                                <div class="cart_remove">
                                                    <a href="cart.php?delete_id=<?= $gh['id'] ?>"><i
                                                            class="ion-android-close"></i></a>
                                                </div>
                                            </div><?php endforeach; ?>
                                        <div class="cart__table">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-left">Tổng phụ</td>
                                                        <td class="text-right"><?= number_format($tongTien) ?>đ</td>
                                                    </tr>

                                                    <tr>
                                                        <td class="text-left">Tộng cộng :</td>
                                                        <td class="text-right"><?= number_format($tongTien) ?>đ</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="cart_button view_cart">
                                            <a href="cart.php">Xem giỏ hàng</a>
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
    <!--header middel start-->
    <div class="breadcrumbs_area product_bread">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb_content">
                        <ul>
                            <li><a href="index.php">Trang chủ</a></li>
                            <li>/</li>
                            <li>Chi tiết sản phẩm</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--breadcrumbs area end-->

    <!--product details start-->
    <div class="product_details">
        <div class="container">
            <div class="row">
                <?php if (!empty($tbloi)): ?>
                    <div style="color:red; font-weight:bold; margin-bottom:10px;">
                        <?= htmlspecialchars($tbloi) ?>
                    </div>
                <?php endif; ?>
                <div class="col-lg-5 col-md-5">

                    <div class="product-details-tab">

                        <div id="img-1" class="zoomWrapper single-zoom">
                            <a href="#">
                                <img id="zoom1" src="<?= $anhSP['anh'] ?>"
                                    data-zoom-image="<?php echo $anhSP['anh']; ?>" alt="big-1">
                            </a>
                        </div>

                        <div class="single-zoom-thumb">
                            <ul class="s-tab-zoom owl-carousel single-product-active" id="gallery_01">
                                <?php foreach ($anhPhu as $ap): ?>
                                    <li>
                                        <a href="#" class="elevatezoom-gallery active" data-update=""
                                            data-image="<?= $ap['anh'] ?>" data-zoom-image="<?= $ap['anh'] ?>">
                                            <img id="zoom1" src="<?= $ap['anh'] ?>" data-zoom-image="<?= $ap['anh']; ?>"
                                                alt="big-1">
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                </div>
                <div class="col-lg-7 col-md-7">
                    <div class="product_d_right">
                        <form action="" method="post">
                            <input type="hidden" name="action" value="add_to_cart">
                            <input type="hidden" name="sanPhamID" value="<?= $sanPham['id'] ?>">
                            <h1><?php echo $sanPham['ten']; ?></h1>
                            <div class=" product_ratting">
                                <ul>
                                    <li><a href="#"><i class="fa fa-star"></i></a></li>
                                    <li><a href="#"><i class="fa fa-star"></i></a></li>
                                    <li><a href="#"><i class="fa fa-star"></i></a></li>
                                    <li><a href="#"><i class="fa fa-star"></i></a></li>
                                    <li><a href="#"><i class="fa fa-star"></i></a></li>
                                    <li class="review"><a href="#"> 1 review </a></li>
                                    <li class="review"><a href="#"> Write a review </a></li>
                                </ul>
                            </div>
                            <div class="product_price">
                                <span class="current_price"><?php echo number_format($sanPham['gia']); ?>đ</span>
                            </div>
                            <div class="product_desc">
                                <p><?php echo $sanPham['moTa']; ?></p>
                            </div>
                            <div class="product_variant size">
                                <h3>size</h3>
                                <select class="niceselect_option" id="color1" name="kichCoID">
                                    <option value="">Chọn size</option>
                                    <?php foreach ($kichco as $kc): ?>

                                        <option value="<?= $kc['id'] ?>" data-stock="<?= $kc['soLuong'] ?>">
                                            <?= $kc['size'] ?></option>

                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="product_variant quantity">
                                <label>Số lượng</label>
                                <input id="soLuong" min="1" max="100" value="1" type="number" name="soLuong">
                                <button class="button" type="submit">Thêm vào giỏ hàng</button>
                            </div>
                            <div class=" product_d_action">
                                <ul>
                                    <li><a href="dsyeuthich.php?id=<?= $sanPham['id'] ?>" title="Add to wishlist"><i
                                                class="fa fa-heart-o" aria-hidden="true"></i> Thêm vào danh sách yêu
                                            thích</a></li>
                                </ul>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--product details end-->

    <!--product info start-->
    <div class="product_d_info">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="product_d_inner">
                        <div class="product_info_button">
                            <ul class="nav" role="tablist">
                                <li>
                                    <a class="active" data-toggle="tab" href="#info" role="tab" aria-controls="info"
                                        aria-selected="false">Thông tin thêm</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#sheet" role="tab" aria-controls="sheet"
                                        aria-selected="false">Bảng dữ liệu</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#reviews" role="tab" aria-controls="reviews"
                                        aria-selected="false">Đánh giá</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="info" role="tabpanel">
                                <div class="product_info_content">
                                    <p>Fashion has been creating well-designed collections since 2010. The brand offers
                                        feminine designs delivering stylish separates and statement dresses which have
                                        since evolved into a full ready-to-wear collection in which every item is a
                                        vital part of a woman's wardrobe. The result? Cool, easy, chic looks with
                                        youthful elegance and unmistakable signature style. All the beautiful pieces are
                                        made in Italy and manufactured with the greatest attention. Now Fashion extends
                                        to a range of accessories including shoes, hats, belts and more!</p>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="sheet" role="tabpanel">
                                <div class="product_d_table">
                                    <form action="#">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td class="first_child">Compositions</td>
                                                    <td>Polyester</td>
                                                </tr>
                                                <tr>
                                                    <td class="first_child">Styles</td>
                                                    <td>Girly</td>
                                                </tr>
                                                <tr>
                                                    <td class="first_child">Properties</td>
                                                    <td>Short Dress</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                                <div class="product_info_content">
                                    <p>Fashion has been creating well-designed collections since 2010. The brand offers
                                        feminine designs delivering stylish separates and statement dresses which have
                                        since evolved into a full ready-to-wear collection in which every item is a
                                        vital part of a woman's wardrobe. The result? Cool, easy, chic looks with
                                        youthful elegance and unmistakable signature style. All the beautiful pieces are
                                        made in Italy and manufactured with the greatest attention. Now Fashion extends
                                        to a range of accessories including shoes, hats, belts and more!</p>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="reviews" role="tabpanel">
                                <div class="product_info_content">
                                    <p>Fashion has been creating well-designed collections since 2010. The brand offers
                                        feminine designs delivering stylish separates and statement dresses which have
                                        since evolved into a full ready-to-wear collection in which every item is a
                                        vital part of a woman's wardrobe. The result? Cool, easy, chic looks with
                                        youthful elegance and unmistakable signature style. All the beautiful pieces are
                                        made in Italy and manufactured with the greatest attention. Now Fashion extends
                                        to a range of accessories including shoes, hats, belts and more!</p>
                                </div>
                                <div class="product_info_inner">
                                    <div class="product_ratting mb-10">
                                        <ul>
                                            <li><a href="#"><i class="fa fa-star"></i></a></li>
                                            <li><a href="#"><i class="fa fa-star"></i></a></li>
                                            <li><a href="#"><i class="fa fa-star"></i></a></li>
                                            <li><a href="#"><i class="fa fa-star"></i></a></li>
                                            <li><a href="#"><i class="fa fa-star"></i></a></li>
                                        </ul>
                                        <strong>Posthemes</strong>
                                        <p>09/07/2018</p>
                                    </div>
                                    <div class="product_demo">
                                        <strong>demo</strong>
                                        <p>That's OK!</p>
                                    </div>
                                </div>
                                <div class="product_review_form">
                                    <form action="#">
                                        <h2>Thêm đánh giá </h2>
                                        <p>Địa chỉ email của bạn sẽ không được công bố. Các trường bắt buộc được đánh
                                            dấu </p>
                                        <div class="row">
                                            <div class="col-12">
                                                <label for="review_comment">Đánh giá của bạn </label>
                                                <textarea name="comment" id="review_comment"></textarea>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <label for="author">Tên</label>
                                                <input id="author" type="text">

                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <label for="email">Email </label>
                                                <input id="email" type="text">
                                            </div>
                                        </div>
                                        <button type="submit">Nộp</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--product info end-->

    <!--product section area start-->
    <section class="product_section related_product">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section_title">
                        <h2>Sản phẩm liên quan</h2>
                        <p>Các thiết kế đương đại, tối giản và hiện đại thể hiện chữ viết tay Lavish Alice</p>
                    </div>
                </div>
            </div>
            <div class="product_area">
                <div class="row">
                    <div class="product_carousel product_three_column4 owl-carousel">
                        <?php foreach ($sanPhamLienQuan as $sp): ?>
                            <div class="col-lg-3">
                                <div class="single_product">
                                    <div class="product_thumb">
                                        <?php
                                        $anh = $db_util->getOne("SELECT anh FROM anhsanpham WHERE sanPhamID = {$sp['id']} AND anhChinh = 1");
                                        ?>
                                        <a class="primary_img" href="product-details.php?id=<?= $sp['id'] ?>">
                                            <img src="<?= $anh['anh'] ?>" alt="<?= $sp['ten'] ?>">
                                        </a>
                                        <div class="product_action">
                                            <div class="hover_action">
                                                <a href="#"><i class="fa fa-plus"></i></a>
                                                <div class="action_button">
                                                    <ul>
                                                        <li><a title="add to cart" href="#"><i class="fa fa-shopping-basket"
                                                                    aria-hidden="true"></i></a></li>
                                                        <li><a href="dsyeuthich.php?id=<?= $sanPham['id'] ?>"
                                                                title="Add to Wishlist"><i class="fa fa-heart-o"
                                                                    aria-hidden="true"></i></a></li>
                                                        <li><a href="compare.html" title="Add to Compare"><i
                                                                    class="fa fa-sliders" aria-hidden="true"></i></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="quick_button">
                                            <a href="product-details.php?id=<?= $sp['id'] ?>" title="Xem nhanh">Xem chi
                                                tiết</a>
                                        </div>
                                    </div>
                                    <div class="product_content">
                                        <h3><a href="product-details.php?id=<?= $sp['id'] ?>"><?= $sp['ten'] ?></a></h3>
                                        <span class="current_price"><?= number_format($sp['gia']) ?>đ</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>

        </div>
    </section>
    <!--product section area end-->

    <!--product section area start-->
    <section class="product_section upsell_product">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section_title">
                        <h2>Sản phẩm upsell</h2>
                        <p>Các thiết kế đương đại, tối giản và hiện đại thể hiện chữ viết tay Lavish Alice</p>
                    </div>
                </div>
            </div>
            <div class="product_area">
                <div class="row">
                    <div class="product_carousel product_three_column4 owl-carousel">
                        <?php foreach ($upsellSanPham as $sp): ?>
                            <div class="col-lg-3">
                                <div class="single_product">
                                    <div class="product_thumb">
                                        <?php
                                        $anh = $db_util->getOne("SELECT anh FROM anhsanpham WHERE sanPhamID = {$sp['id']} AND anhChinh = 1");
                                        ?>
                                        <a class="primary_img" href="product-details.php?id=<?= $sp['id'] ?>">
                                            <img src="<?= $anh['anh'] ?>" alt="<?= $sp['ten'] ?>">
                                        </a>
                                        <div class="product_action">
                                            <div class="hover_action">
                                                <a href="#"><i class="fa fa-plus"></i></a>
                                                <div class="action_button">
                                                    <ul>
                                                        <li><a title="add to cart" href="#"><i class="fa fa-shopping-basket"
                                                                    aria-hidden="true"></i></a></li>
                                                        <li><a href="dsyeuthich.php?id=<?= $sanPham['id'] ?>"
                                                                title="Add to Wishlist"><i class="fa fa-heart-o"
                                                                    aria-hidden="true"></i></a></li>
                                                        <li><a href="compare.html" title="Add to Compare"><i
                                                                    class="fa fa-sliders" aria-hidden="true"></i></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="quick_button">
                                            <a href="product-details.php?id=<?= $sp['id'] ?>" title="Xem nhanh">Xem chi
                                                tiết</a>
                                        </div>
                                    </div>
                                    <div class="product_content">
                                        <h3><a href="product-details.php?id=<?= $sp['id'] ?>"><?= $sp['ten'] ?></a></h3>
                                        <span class="current_price"><?= number_format($sp['gia']) ?>đ</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>

        </div>
    </section>
    <!--product section area end-->

    <!--footer area start-->
    <footer class="footer_widgets">
        <div class="footer_top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-2 col-md-6 col-sm-6 col-6">
                        <div class="widgets_container">
                            <h3>Thông tin</h3>
                            <div class="footer_menu">
                                <ul>
                                    <li><a href="about.php">Giới thiệu</a></li>
                                    <li><a href="#">Thông tin giao hàng</a></li>
                                    <li><a href="privacy-policy.html">Chính sách bảo mật</a></li>
                                    <li><a href="#">Điều khoản</a></li>
                                    <li><a href="contact.html">Liên hệ với chúng tôi</a></li>
                                    <li><a href="#">Quay lại</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-6 col-6">
                        <div class="widgets_container">
                            <h3>Thêm vào</h3>
                            <div class="footer_menu">
                                <ul>
                                    <li><a href="#">Thương hiệu</a></li>
                                    <li><a href="#">Phiếu quà tặng</a></li>
                                    <li><a href="#">Liên kết</a></li>
                                    <li><a href="#">Đặc biệt</a></li>
                                    <li><a href="contact.html">Sơ đồ web</a></li>
                                    <li><a href="my-account.html">Tài khoản của tôi</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="widgets_container contact_us">
                            <h3>Contact Us</h3>
                            <div class="footer_contact">
                                <p>Địa chỉ: 6688 Princess Road, London, Greater London BAS 23JK, UK</p>
                                <p>Số điện thoại: <a href="tel:+(+012)800456789-987">(+012) 800 456 789 - 987</a> </p>
                                <p>Email: demo@example.com</p>
                                <ul>
                                    <li><a href="#" title="Twitter"><i class="fa fa-twitter"></i></a></li>
                                    <li><a href="#" title="google-plus"><i class="fa fa-google-plus"></i></a></li>
                                    <li><a href="#" title="facebook"><i class="fa fa-facebook"></i></a></li>
                                    <li><a href="#" title="youtube"><i class="fa fa-youtube"></i></a></li>
                                </ul>

                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="widgets_container newsletter">
                            <h3>Tham gia bản tin của chúng tôi ngay bây giờ</h3>
                            <div class="newleter-content">
                                <p>Chất lượng vượt trội. Nhà máy có đạo đức. Đăng ký để được miễn phí vận chuyển và trả
                                    hàng tại Hoa Kỳ cho đơn hàng đầu tiên của bạn.</p>
                                <div class="subscribe_form">
                                    <form id="mc-form" class="mc-form footer-newsletter">
                                        <input id="mc-email" type="email" autocomplete="off"
                                            placeholder="Nhập địa chỉ email của bạn..." />
                                        <button id="mc-submit">Đặt mua!</button>
                                    </form>
                                    <!-- mailchimp-alerts Start -->
                                    <div class="mailchimp-alerts text-centre">
                                        <div class="mailchimp-submitting"></div><!-- mailchimp-submitting end -->
                                        <div class="mailchimp-success"></div><!-- mailchimp-success end -->
                                        <div class="mailchimp-error"></div><!-- mailchimp-error end -->
                                    </div><!-- mailchimp-alerts end -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer_bottom">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="copyright_area">
                            <p> &copy; 2021 <strong> </strong> Mede with ❤️ by <a href="https://hasthemes.com/"
                                    target="_blank"><strong>HasThemes</strong></a></p>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="footer_custom_links">
                            <ul>
                                <li><a href="#">Lịch sử đơn hàng</a></li>
                                <li><a href="wishlist.html">Danh sách yêu thích</a></li>
                                <li><a href="#">Bản tin</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!--footer area end-->
    <!-- modal area start-->

    <script>
        document.getElementById('color1').addEventListener('change', function() {
            let stock = this.options[this.selectedIndex].getAttribute('data-stock');
            let qtyInput = document.getElementById('soLuong');
            qtyInput.max = stock || 1;
            qtyInput.value = 1;
        });
    </script>
    <!-- JS
============================================ -->

    <!-- Plugins JS -->
    <script src="assets/js/plugins.js"></script>

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>



</body>

</html>