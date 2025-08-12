<?php
session_start();
  require_once "./db_utils.php";
  $db_utils = new DB_UTILS();

$dsSanPham = $db_utils->getAll("
    SELECT sp.id, sp.ten, sp.gia, asp.anh
    FROM sanpham sp
    LEFT JOIN anhsanpham asp ON sp.id = asp.sanPhamID
    GROUP BY sp.id
");

if (isset($_SESSION['user'])) {
    $nguoiDungID = $_SESSION['user']['id'];
    if (isset($_GET['delete_id'])) {
        $idCanXoa = intval($_GET['delete_id']);
        $db_utils->execute("DELETE FROM giohang WHERE id = ? AND nguoiDungID = ?", [$idCanXoa, $nguoiDungID]);
        header("Location: index.php"); 
        exit();
    }
    $gioHang = $db_utils->getAll("
        SELECT 
            gh.id,
            gh.sanPhamID,
            sp.ten AS tensp,
            kc.size,
            gh.soLuong,
            gh.gia AS gia,
            (gh.soLuong * gh.gia) AS thanhTien
        FROM giohang gh
        JOIN sanpham sp ON gh.sanPhamID = sp.id
        JOIN kichco kc ON gh.kichCoID = kc.id
        WHERE gh.nguoiDungID = ?
    ", [$nguoiDungID]);
}
$spMoiNhat = $db_utils->getAll("
    SELECT sp.id, sp.ten, sp.gia, asp.anh
    FROM sanpham sp
    LEFT JOIN anhsanpham asp ON sp.id = asp.sanPhamID
    GROUP BY sp.id
    ORDER BY sp.ngayTao DESC
    LIMIT 8
");
$spBanChay = $db_utils->getAll("
    SELECT sp.id, sp.ten, sp.gia, asp.anh, SUM(ctdh.soLuong) AS tongBan
    FROM chitietdonhang ctdh
    JOIN sanpham sp ON ctdh.bienTheID = sp.id
    LEFT JOIN anhsanpham asp ON sp.id = asp.sanPhamID
    GROUP BY sp.id
    ORDER BY tongBan DESC
    LIMIT 8
");


?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Fashion eCommerce HTML Template</title>
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
    <style>
    .product_thumb {
        position: relative;
        overflow: hidden;
    }

    .wishlist_icon {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .wishlist_icon a {
        background: white;
        border-radius: 50%;
        padding: 6px;
        color: #ff4c4c;
        font-size: 16px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .product_hover_buttons {
        position: absolute;
        bottom: 15px;
        left: 50%;
        transform: translateX(-50%);
        opacity: 0;
        text-align: center;
        transition: all 0.3s ease;
        z-index: 10;
    }

    .product_hover_buttons .btn {
        display: block;
        background-color: #000;
        color: white;
        padding: 8px 15px;
        margin: 5px 0;
        text-decoration: none;
        border-radius: 5px;
        font-size: 14px;
    }

    .product_hover_buttons .btn:hover {
        background-color: #ff4c4c;
    }

    .single_product:hover .wishlist_icon,
    .single_product:hover .product_hover_buttons {
        opacity: 1;
    }
    </style>

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
                                    <input placeholder="Tìm kiếm sản phẩm..." type="text">
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
    <!--header area end-->

    <!--slider area start-->
    <div class="slider_area slider_style home_three_slider owl-carousel">
        <div class="single_slider" data-bgimg="assets/img/slider/slider4.jpg">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-12">
                        <div class="slider_content content_one">
                            <img src="assets/img/slider/content3.png" alt="">
                            <p>the wooboom clothing summer collection is back at half price</p>
                            <a href="shop.html">Discover Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="single_slider" data-bgimg="assets/img/slider/slider5.jpg">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-12">
                        <div class="slider_content content_two">
                            <img src="assets/img/slider/content4.png" alt="">
                            <p>the wooboom clothing summer collection is back at half price</p>
                            <a href="shop.html">Discover Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="single_slider" data-bgimg="assets/img/slider/slider6.jpg">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-12">
                        <div class="slider_content content_three">
                            <img src="assets/img/slider/content5.png" alt="">
                            <p>the wooboom clothing summer collection is back at half price</p>
                            <a href="shop.html">Discover Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--slider area end-->

    <!--banner area start-->
    <div class="banner_section banner_section_three">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-lg-4 col-md-6">
                    <div class="banner_area">
                        <div class="banner_thumb">
                            <a href="shop.html"><img src="assets/img/bg/banner8.jpg" alt="#"></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="banner_area">
                        <div class="banner_thumb">
                            <a href="shop.html"><img src="assets/img/bg/banner9.jpg" alt="#"></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="banner_area bottom">
                        <div class="banner_thumb">
                            <a href="shop.html"><img src="assets/img/bg/banner10.jpg" alt="#"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--banner area end-->
    <section class="product_section womens_product bottom">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section_title">
                        <h2>Sản phẩm mới nhất</h2>
                    </div>
                </div>
            </div>
            <div class="product_area">
                <div class="row">
                    <div class="product_carousel product_three_column4 owl-carousel">
                        <?php foreach($spMoiNhat as $sanpham): ?>
                        <div class="col-lg-3">
                            <div class="single_product">
                                <div class="product_thumb">
                                    <div class="wishlist_icon">
                                        <ul>
                                            <li><a href="dsyeuthich.php?id=<?= $sanpham['id'] ?>"
                                                    title="Thêm vào danh sách yêu thích"><i class="fa fa-heart-o"
                                                        aria-hidden="true"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <a class="primary_img" href="product-details.php?id=<?= $sanpham['id']?>"><img
                                            src="<?php echo $sanpham['anh']; ?>" alt=""></a>
                                    <a class="secondary_img" href="product-details.php?id=<?= $sanpham['id']?>"><img
                                            src="<?php echo $sanpham['anh']; ?>" alt=""></a>

                                    <div class="quick_button">
                                        <a href="product-details.php?id=<?= $sanpham['id'] ?>" title="Xem nhanh">Xem chi
                                            tiết</a> <br>
                                    </div>

                                </div>
                                <div class="product_content">
                                    <h3><a href="product-details.php"><?php echo $sanpham['ten']; ?></a></h3>
                                    <span class="current_price"><?php echo number_format ($sanpham['gia']); ?></span>

                                </div>
                            </div>
                        </div>
                        <?php endforeach;?>
                    </div>

                </div>

            </div>
    </section>
    <section class="product_section womens_product bottom">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section_title">
                        <h2>Sản phẩm bán chạy nhất</h2>
                    </div>
                </div>
            </div>
            <div class="product_area">
                <div class="row">
                    <div class="product_carousel product_three_column4 owl-carousel">
                        <?php foreach($spBanChay as $sanpham): ?>
                        <div class="col-lg-3">
                            <div class="single_product">
                                <div class="product_thumb">
                                    <div class="wishlist_icon">
                                        <ul>
                                            <li><a href="dsyeuthich.php?id=<?= $sanpham['id'] ?>"
                                                    title="Thêm vào danh sách yêu thích"><i class="fa fa-heart-o"
                                                        aria-hidden="true"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <a class="primary_img" href="product-details.php?id=<?= $sanpham['id']?>"><img
                                            src="<?php echo $sanpham['anh']; ?>" alt=""></a>
                                    <a class="secondary_img" href="product-details.php?id=<?= $sanpham['id']?>"><img
                                            src="<?php echo $sanpham['anh']; ?>" alt=""></a>

                                    <div class="quick_button">
                                        <a href="product-details.php?id=<?= $sanpham['id'] ?>" title="Xem nhanh">Xem chi
                                            tiết</a> <br>
                                    </div>

                                </div>
                                <div class="product_content">
                                    <h3><a href="product-details.php"><?php echo $sanpham['ten']; ?></a></h3>
                                    <span class="current_price"><?php echo number_format ($sanpham['gia']); ?></span>

                                </div>
                            </div>
                        </div>
                        <?php endforeach;?>
                    </div>

                </div>

            </div>
    </section>

    <!--product section area start-->
    <section class="product_section womens_product">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section_title">
                        <h2>Sản phẩm của chúng tôi</h2>
                        <p>Các sản phẩm thiết kế hiện đại,mới nhất</p>
                    </div>
                </div>
            </div>
            <div class="product_area">
                <div class="row">
                    <div class="col-12">
                        <div class="product_tab_button">
                            <ul class="nav" role="tablist">
                                <li>
                                    <a data-toggle="tab" href="#handbag" role="tab" aria-controls="handbag"
                                        aria-selected="false">Nam</a>
                                        <a data-toggle="tab" href="#handbag" role="tab" aria-controls="handbag"
                                        aria-selected="false">Nữ</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="clothing" role="tabpanel">
                        <div class="product_container">
                            <div class="row product_column4">
                                <?php foreach($dsSanPham as $sanpham): ?>
                                <div class="col-lg-3">
                                    <div class="single_product">
                                        <div class="product_thumb">
                                            <div class="wishlist_icon">
                                                <ul>
                                                    <li><a href="dsyeuthich.php?id=<?= $sanpham['id'] ?>"
                                                            title="Thêm vào danh sách yêu thích"><i
                                                                class="fa fa-heart-o" aria-hidden="true"></i></a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <a class="primary_img"
                                                href="product-details.php?id=<?= $sanpham['id']?>"><img
                                                    src="<?php echo $sanpham['anh']; ?>" alt=""></a>
                                            <a class="secondary_img"
                                                href="product-details.php?id=<?= $sanpham['id']?>"><img
                                                    src="<?php echo $sanpham['anh']; ?>" alt=""></a>

                                            <div class="quick_button">
                                                <a href="product-details.php?id=<?= $sanpham['id'] ?>"
                                                    title="Xem nhanh">Xem chi tiết</a> <br>
                                            </div>


                                        </div>
                                        <div class="product_content">
                                            <h3><a href="product-details.php"><?php echo $sanpham['ten']; ?></a></h3>
                                            <span class="current_price"><?php echo number_format ($sanpham['gia']); ?>
                                                đ</span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach;?>
                            </div>
                        </div>
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
    <div class="modal fade" id="modal_box" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="modal_body">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-5 col-md-5 col-sm-12">
                                <div class="modal_tab">
                                    <div class="tab-content product-details-large">
                                        <div class="tab-pane fade show active" id="tab1" role="tabpanel">
                                            <div class="modal_tab_img">
                                                <a href="#"><img src="<?= $anhSP['anh'] ?>" alt=""></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal_tab_button">
                                        <ul class="nav product_navactive owl-carousel" role="tablist">
                                            <li>
                                                <a class="nav-link active" data-toggle="tab" href="#tab1" role="tab"
                                                    aria-controls="tab1" aria-selected="false"><img
                                                        src="assets/img/s-product/product3.jpg" alt=""></a>
                                            </li>





                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7 col-md-7 col-sm-12">
                                <div class="modal_right">
                                    <div class="modal_title mb-10">
                                        <h1><?php echo $sanPham['ten']; ?></h1>
                                    </div>
                                    <div class="modal_price mb-10">
                                        <span
                                            class="current_price"><?php echo number_format ($sanPham['gia']); ?>đ</span>
                                    </div>
                                    <div class="modal_description mb-15">
                                        <p><?php echo $sanPham['moTa']; ?></p>
                                    </div>
                                    <div class="variants_selects">
                                        <div class="variants_size">
                                            <h2>size</h2>
                                            <select class="select_option">
                                                <option value="">Chọn size</option>
                                                <?php foreach($kichco as $kc): ?>

                                                <option value="<?php $kc['id']?>"><?= $kc['size'] ?></option>

                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                        <div class="modal_add_to_cart">
                                            <form action="#">
                                                <input min="0" max="100" step="2" value="1" type="number">
                                                <button type="submit">Thêm vào giỏ hàng</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- modal area start-->


    <!-- JS
============================================ -->

    <!-- Plugins JS -->
    <script src="assets/js/plugins.js"></script>

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>



</body>

</html>