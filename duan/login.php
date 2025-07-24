<?php
// Đăng ký
session_start();
require_once "db_utils.php";
$db_util = new DB_UTILS();

$login_error = "";
$register_error = "";
$register_success = "";

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['register'])) {
    $ten = trim($_POST['ten']);
    $email = trim($_POST['email']);
    $matkhau = $_POST['matkhau'] ?? '';
    $sdt = trim($_POST['sdt']);
    $diachi = trim($_POST['diachi']);
    $ngaytao = date('Y-m-d H:i:s');

    if (empty($ten) || empty($email) || empty($matkhau) || empty($sdt) || empty($diachi)) {
        $register_error = "Vui lòng điền đầy đủ thông tin.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $register_error = "Email không hợp lệ.";
    } elseif (strlen($matkhau) < 6) {
        $register_error = "Mật khẩu ít nhất 6 ký tự.";
    } else {
        $tontai = $db_util->getOne("SELECT * FROM nguoidung WHERE email = ?", [$email]);
        if ($tontai) {
            $register_error = "Email đã được đăng ký.";
        } else {
            $hashed = password_hash($matkhau, PASSWORD_DEFAULT);
            $vaitro = 'admin';

            $db_util->execute(
                "INSERT INTO nguoidung (ten, email, matkhau, vaitro, soDienThoai, diachi, ngaytao)
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                [$ten, $email, $hashed, $vaitro, $sdt, $diachi, $ngaytao]
            );
            $register_success = "Đăng ký thành công. Vui lòng đăng nhập!";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']) ?? '';
    $matkhau = $_POST["matkhau"] ?? '';

    if (empty($email) || empty($matkhau)) {
        $login_error = "Vui lòng nhập đầy đủ email và mật khẩu.";
    } else {
        $user = $db_util->getOne("SELECT * FROM nguoidung WHERE email = ?", [$email]);
        if ($user && password_verify($matkhau, $user['matKhau'])) {
            $_SESSION['user'] = $user;
            header("Location: index.php");
            exit;
        } else {
            $login_error = "Email hoặc mật khẩu không đúng.";
        }
    }
}

?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Login page</title>
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
                        <div class="welcome_text">
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-12">
                        <div class="top_right text-right">
                            <ul>
                               <li class="top_links"><a href="#">Tài khoản của tôi <i class="ion-chevron-down"></i></a>
                                    <ul class="dropdown_links">
                                        <li><a href="wishlist.html">Danh mục yêu thích </a></li>
                                        <li><a href="my-account.html">My Account </a></li>
                                        <li><a href="login.php">Đăng nhập</a></li>
                                        <li><a href="compare.html">Đăng xuất  </a></li>
                                    </ul>
                                </li> 
                            </ul>
                        </div>   
                    </div>
                </div>
            </div>
        </div>
        <!--header top start-->

        <!--header middel start-->
        <div class="header_middel">
            <div class="container-fluid">
                <div class="middel_inner">
                    <div class="row align-items-center">
                        <div class="col-lg-4">
                            <div class="search_bar">
                                <form action="#">                          
                                    <input placeholder="Tìm trong cửa hàng..." type="text">
                                    <button type="submit"><i class="ion-ios-search-strong"></i></button>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="logo">
                                <a href="index.html"><img src="assets/img/logo/logo.png" alt=""></a>
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
                                            <a href="#">Marshall Portable  Bluetooth</a>
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
                                    <li class="active"><a href="index.php">Trang chủ <i class="fa fa-angle-down"></i></a>
                                    </li>
                                    <li class="mega_items"><a href="shop.html">Sản phẩm <i class="fa fa-angle-down"></i></a>
                                    </li>
                                    <li><a href="blog.php">blog <i class="fa fa-angle-down"></i></a>
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
                        <a href="index.html"><img src="assets/img/logo/logo.png" alt=""></a>
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
                                        <li><a href="shop_category.php">Sản phẩm </a></li>
                                        <li><a href="about.html">Giới thiệu </a></li>
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
                            <li><a href="index.html">Trang chủ</a></li>
                            <li>/</li>
                            <li>Đăng nhập</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>         
    </div>
    <!--breadcrumbs area end-->
    
    <!-- customer login start -->
    <div class="customer_login">
        <div class="container">
            <div class="row">
               <!--login area start-->
                <div class="col-lg-6 col-md-6">
                    <div class="account_form">
                        <h2>Đăng nhập</h2>
                        <form action="" method="post">
    <?php if (!empty($login_error)) echo "<p style='color:red;'>$login_error</p>"; ?>

<p>
    <label>Email</label>
    <input type="email" name="email" required />
</p>

<p>
    <label>Mật khẩu</label>
    <input type="password" name="matkhau" required />
</p>

    <div class="login_submit">
        <a href="#">Quên mật khẩu?</a>
        <label for="remember">
            <input id="remember" type="checkbox">
            Nhớ mật khẩu
        </label>
        <button type="submit" name="login">Đăng nhập</button>
    </div>
</form>

                     </div>    
                </div>
                <!--login area start-->

                <!--register area start-->
                <div class="col-lg-6 col-md-6">
                    <div class="account_form register">
                        <h2>Đăng kí</h2>
                        <form action="" method="post">
    <?php if (!empty($register_error)) echo "<p style='color:red;'>$register_error</p>"; ?>
    <?php if (!empty($register_success)) echo "<p style='color:green;'>$register_success</p>"; ?>

   <p>
       <label>Họ và tên</label>
       <input type="text" name="ten" required />
   </p>

   <p>
       <label>Email</label>
       <input type="email" name="email" required />
   </p>

   <p>
       <label>Mật khẩu</label>
       <input type="password" name="matkhau" required />
       </p>
<p>
    <label>Số điện thoại</label>
    <input type="text" name="sdt" required />
</p>
<p>
    <label>Địa chỉ</label>
    <input type="text" name="diachi" required />
</p>

    <button type="submit" name="register">Đăng ký</button>
</form>

                    </div>    
                </div>
                <!--register area end-->
            </div>
        </div>    
    </div>
    <!-- customer login end -->
    
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
                                <p>Chất lượng vượt trội. Nhà máy có đạo đức. Đăng ký để được miễn phí vận chuyển và trả hàng tại Hoa Kỳ cho đơn hàng đầu tiên của bạn.</p>
                                 <div class="subscribe_form">
                                    <form id="mc-form" class="mc-form footer-newsletter" >
                                        <input id="mc-email" type="email" autocomplete="off" placeholder="Nhập địa chỉ email của bạn..." />
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
                            <p> &copy; 2021 <strong> </strong> Mede with ❤️ by <a href="https://hasthemes.com/" target="_blank"><strong>HasThemes</strong></a></p>
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
<script  src="https://www.google.com/jsapi"></script>
<script src="assets/js/map.js"></script>


<!-- Plugins JS -->
<script src="assets/js/plugins.js"></script>

<!-- Main JS -->
<script src="assets/js/main.js"></script>



</body>

</html>