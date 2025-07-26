<?php
function generateOTP($length = 6) {
    return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

session_start();
require_once "db_utils.php";
require_once __DIR__ . "/mailservice.php";
$db_util = new DB_UTILS();
$checkotp = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';

    // Gửi mã OTP
    if (isset($_POST['gui'])) {
        $check = $db_util->getOne("SELECT * FROM nguoidung WHERE email = ?", [$email]);
        if ($check) {
            $otpgui = generateOTP();
            $otptime = date("Y-m-d H:i:s");
            $db_util->execute("UPDATE nguoidung SET otp = ?, otp_time = ? WHERE email = ?", [$otpgui, $otptime, $email]);

            $subject = 'Đặt lại mật khẩu';
            $content = "Mã OTP là $otpgui. Hiệu lực trong 2 phút.";
            MailService::send($email, USERNAME_EMAIL, $subject, $content);
        } else {
            echo "Email không tồn tại";
        }
    }

    // Xác minh OTP
    if (isset($_POST['otpgtui'])) {
        $otpngoidung = $_POST['otp'];
        $check = $db_util->getOne("SELECT * FROM nguoidung WHERE email = ?", [$email]);

        if ($check) {
            $otpdb = $check['otp'];
            $otptime = strtotime($check['otp_time']);
            $otptimehientai = time();

            if ($otpngoidung != $otpdb) {
                $checkotp = false;
                echo "Mã OTP không đúng";
            } elseif ($otptimehientai - $otptime > 120) {
                $checkotp = false;
                echo "Mã OTP quá hạn";
                $db_util->execute("UPDATE nguoidung SET otp = NULL, otp_time = NULL WHERE email = ?", [$email]);
            }
        } else {
            $checkotp = false;
            echo "Email không hợp lệ";
        }
    }

    // Đổi mật khẩu
    if (isset($_POST['doi']) && $checkotp) {
        $matkhau = $_POST['matKhau'];
        $hashed = password_hash($matkhau, PASSWORD_DEFAULT);
        $db_util->execute("UPDATE nguoidung SET matKhau = ?, otp = NULL, otp_time = NULL WHERE email = ?", [$hashed, $email]);

        $subject = 'Đổi mật khẩu thành công';
        $content = "Cảm ơn bạn đã sử dụng dịch vụ";
        MailService::send($email, USERNAME_EMAIL, $subject, $content);

        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

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
                                        <li><a href="compare.html">Đăng xuất </a></li>
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
                                    <li class="mega_items"><a href="shop.html">Sản phẩm <i
                                                class="fa fa-angle-down"></i></a>
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
                            <li>Quên mật khẩu</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="customer_login">
        <div class="container">
            <div class="row">
                <!--login area start-->
                <div class="col-lg-6 col-md-6">
                    <div class="account_form">
                        <h2>Quên mật khẩu</h2>
                        <form action="" method="post">
                            <p>
                                <label for="">Nhập email</label>
                                <input type="email" name="email" required>
                            </p>
                            <button type="submit" name="gui">Gửi mã OTP</button>
                        </form> <br>

                    </div>
                </div>
                <!--login area start-->

                <!--register area start-->
                <div class="col-lg-6 col-md-6">
                    <div class="account_form register">
                        <?php if (isset($_POST['gui']) && isset($check)) { ?>
                        <h2>Nhập mã otp</h2>
                        <form action="" method="post">
                            <p>
                                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                                <label for="">Nhập mã OTP</label>
                                <input type="text" name="otp" required>
                            </p>
                            <button type="submit" name="otpgtui">Xác nhận</button>
                        </form>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="account_form register">
                        <?php if (isset($_POST['otpgtui']) && $checkotp) { ?>
                        <h2>Đổi mật khẩu mới</h2>
                        <form action="" method="post">
                            <p>
                                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                                <label for="">Nhập mật khẩu mới</label>
                                <input type="text" name="matKhau" required>
                            </p>
                            <button type="submit" name="doi">Đổi</button>
                        </form>
                        <?php } ?>
                    </div>
                </div>
                <!--register area end-->
            </div>
        </div>
    </div>

</body>

</html>