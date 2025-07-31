<?php
session_start();
require_once "db_utils.php";
$db_util = new DB_UTILS();
$nguoiDungID = $_SESSION['user']['id'];
if (isset($_GET['delete_id'])) {
    $idCanXoa = intval($_GET['delete_id']);
    // Xóa sản phẩm khỏi giỏ nếu đúng ID và thuộc về user
    $db_util->execute("DELETE FROM giohang WHERE id = ? AND nguoiDungID = ?", [$idCanXoa, $nguoiDungID]);
    header("Location: cart.php"); // reload lại để tránh xóa lặp
    exit();
}
$nguoidung = $db_util->getAll("SELECT * FROM giohang WHERE nguoiDungID = ?", [$nguoiDungID]);
$gioHang = $db_util->getAll("
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
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Cart page</title>
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

    <!--Offcanvas menu area start-->
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
                                        <li><a href="wishlist.html">Danh mục yêu thích</a></li>
                                        <?php if ($_SESSION['user']['vaiTro'] == 'admin'): ?>
                                        <li><a href="quanly.php">Quản lý cửa hàng</a></li>

                                        <?php endif; ?>
                                        <li><a href="logout.php">Đăng xuất</a></li>
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
                                    <a href="#"><i class="fa fa-shopping-basket"></i>2 sản phẩm</a>
                                    <!--mini cart-->
                                    <div class="mini_cart">
                                        <div class="cart_item top">
                                            <div class="cart_img">
                                                <a href="#"><img src="assets/img/s-product/product.jpg" alt=""></a>
                                            </div>
                                            <div class="cart_info">
                                                <a href="#">Apple iPhone SE 16GB</a>

                                                <span>1x $60.00</span>

                                            </div>
                                            <div class="cart_remove">
                                                <a href="#"><i class="ion-android-close"></i></a>
                                            </div>
                                        </div>
                                        <div class="cart_item bottom">
                                            <div class="cart_img">
                                                <a href="#"><img src="assets/img/s-product/product2.jpg" alt=""></a>
                                            </div>
                                            <div class="cart_info">
                                                <a href="#">Marshall Portable Bluetooth</a>
                                                <span> 1x $160.00</span>
                                            </div>
                                            <div class="cart_remove">
                                                <a href="#"><i class="ion-android-close"></i></a>
                                            </div>
                                        </div>
                                        <div class="cart__table">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-left">Sub-Total :</td>
                                                        <td class="text-right">$150.00</td>
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
    <!--header area end-->

    <!--breadcrumbs area start-->
    <div class="breadcrumbs_area other_bread">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb_content">
                        <ul>
                            <li><a href="index.php">Trang chủ</a></li>
                            <li>/</li>
                            <li>Giỏ hàng</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--breadcrumbs area end-->

    <!-- shopping cart area start -->
    <div class="shopping_cart_area">
        <div class="container">
            <form action="#">
                <div class="row">
                    <div class="col-12">
                        <div class="table_desc">
                            <div class="cart_page table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="product_remove">Xóa</th>
                                            <th class="product_thumb">Hình ảnh</th>
                                            <th class="product_name">Sản phẩm</th>
                                            <th class="product-price">Đơn giá</th>
                                            <th class="product_quantity">Số lượng</th>
                                            <th class="product_total">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <?php $tongTien = 0;
                                     foreach($gioHang as $gh):
                                     $tongTien += $gh['thanhTien'];
                                     $anh = $db_util->getOne("SELECT anh FROM anhsanpham WHERE sanPhamID = ? AND anhChinh = 1", [$gh['sanPhamID']]); ?>
                                    <tbody>
                                        <tr>
                                            <td class="product_remove">
                                                <a href="cart.php?delete_id=<?= $gh['id'] ?>"><i
                                                        class="fa fa-trash-o"></i></a>
                                            </td>
                                            <td class="product_thumb">
                                                <a href="#"><img src="<?= $anh['anh'] ?>" alt=""
                                                        style="width:80px;"></a>
                                            </td>
                                            <td class="product_name"><?= $gh['tensp'] ?> (size
                                                <?= $gh['size'] ?>)</td>
                                            <td class="product-price"><?= number_format($gh['gia']) ?>đ</td>
                                            <td class="product_quantity">
                                                <input min="1" max="100" value="<?= $gh['soLuong'] ?>" type="number"
                                                    readonly>
                                            </td>
                                            <td class="product_total"><?= number_format($gh['thanhTien']) ?>đ</td>
                                        </tr>
                                    </tbody>
                                    <?php endforeach;?>
                                </table>
                            </div>
                            <div class="cart_submit">
                                <button type="submit">Cập nhật giỏ hàng</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bắt đầu khu vực mã giảm giá -->
                <div class="coupon_area">
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="coupon_code left">
                                <h3>Mã giảm giá</h3>
                                <div class="coupon_inner">
                                    <p>Nhập mã giảm giá nếu bạn có.</p>
                                    <input placeholder="Nhập mã giảm giá" type="text">
                                    <button type="submit">Áp dụng mã</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="coupon_code right">
                                <h3>Tổng giỏ hàng</h3>
                                <div class="coupon_inner">
                                    <div class="cart_subtotal">
                                        <p>Tạm tính</p>
                                        <p class="cart_amount"><?= number_format($tongTien) ?>đ</p>
                                    </div>
                                    <div class="cart_subtotal">
                                        <p>Phí vận chuyển</p>
                                        <p class="cart_amount"><span>Phí cố định:</span> 0đ</p>
                                    </div>
                                    <div class="cart_subtotal">
                                        <p>Tổng cộng</p>
                                        <p class="cart_amount"><?= number_format($tongTien) ?>đ</p>
                                    </div>
                                    <div class="checkout_btn">
                                        <a href="dathang.php">Tiến hành thanh toán</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Kết thúc khu vực mã giảm giá -->
            </form>
        </div>
    </div>

    <!-- shopping cart area end -->

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

    <!-- JS
============================================ -->


    <!--map js code here-->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAdWLY_Y6FL7QGW5vcO3zajUEsrKfQPNzI"></script>
    <script src="https://www.google.com/jsapi"></script>
    <script src="assets/js/map.js"></script>


    <!-- Plugins JS -->
    <script src="assets/js/plugins.js"></script>

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>



</body>

</html>