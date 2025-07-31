<?php
session_start();
require_once "db_utils.php";
$db = new DB_UTILS();
$id = $_SESSION['user']['id'];
$dstinh = $db->getAll("SELECT * FROM tinh");


$idTinh = $idHuyen = $idXa = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["tinh"])) {
        $idTinh = $_POST["tinh"];
        $dshuyen = $db->getAll("SELECT * FROM huyen WHERE idTinh = ?", [$idTinh]);
    }
    if (isset($_POST["huyen"])) {
        $idHuyen = $_POST["huyen"];
        $dsxa = $db->getAll("SELECT * FROM xa WHERE idHuyen = ?", [$idHuyen]);
    }
    if (isset($_POST["xa"])) {
        $idXa = $_POST["xa"];
    }

    if (isset($_POST["them"])) {
        $tinh = $_POST["tinh"] ?? "";
        $huyen = $_POST["huyen"] ?? "";
        $xa = $_POST["xa"] ?? "";
        $chiTiet = $_POST["chiTiet"] ?? "";

        if (empty($chiTiet) || empty($tinh) || empty($huyen) || empty($xa)) {
            $_SESSION["thongBao"] = "Vui lòng nhập đầy đủ thông tin";
        } else {
            $db->execute("INSERT INTO diachi(idNguoiDung,idXa,chiTiet) VALUES (?,?,?)", [$id, $xa, $chiTiet]);
            $_SESSION["thongBao"] = "Thêm địa chỉ thành công!";
            header("Location: diachi.php");
            exit;
        }
    }


    if (isset($_POST["macDinh"])) {
        $db->execute("UPDATE diachi SET macDinh = 0 WHERE idNguoiDung = ?", [$_POST["idNguoiDung"]]);
        $db->execute("UPDATE diachi SET macDinh = 1 WHERE idNguoiDung = ? AND id = ?", [$_POST["idNguoiDung"], $_POST["idDiaChi"]]);
        $_SESSION["thongBao"] = "Cập nhật địa chỉ thành công!";
        header("Location: diachi.php?");
        exit;
    }
    if (isset($_POST["xoa"])) {
        $db->execute("DELETE FROM diachi WHERE id = ?", [$_POST["idDiaChi"]]);
        $_SESSION["thongBao"] = "Xóa địa chỉ thành công!";
        header("Location: diachi.php?");
        exit;
    }
}


$dsDiaChi = $db->getAll("
    SELECT dc.id, dc.chiTiet, dc.macDinh, 
           tinh.ten AS tenTinh, 
           huyen.ten AS tenHuyen, 
           xa.ten AS tenXa
    FROM diachi dc
    JOIN xa ON dc.idXa = xa.maGHN
    JOIN huyen ON xa.idHuyen = huyen.maGHN
    JOIN tinh ON huyen.idTinh = tinh.maGHN
    WHERE dc.idNguoiDung = ?
    ORDER BY macDinh DESC
", [$id]);

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý người dùng</title>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Fashion eCommerce HTML Template</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
    <style>
    form.card.p-10 {
        padding: 1.5rem !important;
        border-radius: 12px;
        border: 1px solid #dee2e6;
        background-color: #fff;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .form-select,
    .form-control {
        border-radius: 8px;
        font-size: 14px;
    }

    .btn-primary {
        border-radius: 8px;
        font-weight: 500;
    }

    .row.align-items-end>div {
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .row.align-items-end>div {
            margin-bottom: 1rem;
        }
    }
    </style>
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

        <div class="d-flex flex-grow-1">

            <div class="flex-grow-1 d-flex flex-column">

                <main class="p-4 flex-grow-1">
                    <div class="container my-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-geo-alt-fill fs-4"></i>
                            <span class="fs-5 fw-semibold align-text-bottom">Sổ địa chỉ</span>
                        </div>

                        <?php if (!empty($_SESSION["thongBao"])): ?>
                        <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                            <?= $_SESSION["thongBao"] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php $_SESSION["thongBao"] = ""; ?>
                        <?php endif; ?>
                        <form action="" method="post" class="card p-3 shadow-sm">
                            <div class="d-flex flex-wrap align-items-end gap-3">

                                <!-- Tỉnh -->
                                <div style="flex: 1; min-width: 150px;">
                                    <label for="tinh" class="form-label">
                                        <i class="bi bi-geo-alt-fill me-1"></i>Tỉnh
                                    </label>
                                    <select name="tinh" id="tinh" class="form-select" onchange="this.form.submit()">
                                        <option value="">Chọn Tỉnh</option>
                                        <?php foreach ($dstinh as $tinh) { ?>
                                        <option
                                            <?= isset($_POST["tinh"]) && $idTinh == $tinh["maGHN"] ? "selected" : "" ?>
                                            value="<?= $tinh["maGHN"] ?>">
                                            <?= $tinh["ten"] ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- Huyện -->
                                <div style="flex: 1; min-width: 150px;">
                                    <label for="huyen" class="form-label">
                                        <i class="bi bi-signpost-2-fill me-1"></i>TP/Quận/Huyện
                                    </label>
                                    <select name="huyen" id="huyen" class="form-select" onchange="this.form.submit()"
                                        <?= !isset($_POST["tinh"]) ? "disabled" : "" ?>>
                                        <option value="">Chọn Quận / Huyện</option>
                                        <?php if (isset($_POST["tinh"])) {
                                        foreach ($dshuyen as $huyen) { ?>
                                        <option
                                            <?= isset($_POST["huyen"]) && $idHuyen == $huyen["maGHN"] ? "selected" : "" ?>
                                            value="<?= $huyen["maGHN"] ?>">
                                            <?= $huyen["ten"] ?>
                                        </option>
                                        <?php }
                                    } ?>
                                    </select>
                                </div>

                                <!-- Xã -->
                                <div style="flex: 1; min-width: 150px;">
                                    <label for="xa" class="form-label">
                                        <i class="bi bi-house-door-fill me-1"></i>Xã
                                    </label>
                                    <select name="xa" id="xa" class="form-select" onchange="this.form.submit()"
                                        <?= !isset($_POST["huyen"]) ? "disabled" : "" ?>>
                                        <option value="">Chọn Xã</option>
                                        <?php if (isset($_POST["huyen"])) {
                                        foreach ($dsxa as $xa) { ?>
                                        <option <?= isset($_POST["xa"]) && $idXa == $xa["maGHN"] ? "selected" : "" ?>
                                            value="<?= $xa["maGHN"] ?>">
                                            <?= $xa["ten"] ?>
                                        </option>
                                        <?php }
                                    } ?>
                                    </select>
                                </div>

                                <!-- Chi tiết -->
                                <div style="flex: 2; min-width: 200px;">
                                    <label for="chiTiet" class="form-label">
                                        <i class="bi bi-pencil-square me-1"></i>Chi tiết
                                    </label>
                                    <input type="text" name="chiTiet" id="chiTiet" class="form-control"
                                        placeholder="Thôn, Tên đường, Số nhà"
                                        value="<?= isset($_POST["chiTiet"]) ? htmlspecialchars($_POST["chiTiet"]) : "" ?>"
                                        <?= !isset($_POST["xa"]) || empty($_POST["xa"]) ? "disabled" : "" ?> required>
                                    <?php if (!empty($err["chiTiet"])): ?>
                                    <div class="text-danger mt-1 small"><?= $err["chiTiet"] ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- Nút Thêm -->
                                <div style="min-width: 120px;">
                                    <label class="form-label d-block invisible">Nút</label>
                                    <button type="submit" name="them"
                                        class="btn btn-success w-100 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-plus-circle me-2"></i> Thêm
                                    </button>
                                </div>

                            </div>
                        </form>

                        <div class="table-responsive">
                            <?php if (!empty($dsDiaChi)) { ?>
                            <table class="table table-bordered table-hover text-center align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Tỉnh</th>
                                        <th>TP/Quận/Huyện</th>
                                        <th>Phường/Thị trấn/Xã</th>
                                        <th>Chi tiết</th>
                                        <th>Mặc định</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dsDiaChi as $dc): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($dc["tenTinh"]) ?></td>
                                        <td><?= htmlspecialchars($dc["tenHuyen"]) ?></td>
                                        <td><?= htmlspecialchars($dc["tenXa"]) ?></td>
                                        <td><?= htmlspecialchars($dc["chiTiet"]) ?></td>
                                        <td>
                                            <?php if ($dc["macDinh"]): ?>
                                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <form method="post" action="">
                                                    <input type="hidden" name="idDiaChi" value="<?= $dc["id"] ?>">
                                                    <input type="hidden" name="idNguoiDung" value="<?= $id ?>">

                                                    <?php if (!$dc["macDinh"]): ?>
                                                    <button name="macDinh" class="btn btn-outline-primary btn-sm"
                                                        title="Đặt làm mặc định">
                                                        <i class="bi bi-star"></i>
                                                    </button>
                                                    <?php endif; ?>

                                                    <button type="submit" name="xoa" class="btn btn-danger btn-sm"
                                                        title="Xóa địa chỉ"
                                                        onclick="return confirm('Bạn có chắc muốn xóa địa chỉ này?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php } else { ?>
                            <div class="alert alert-warning text-center mt-4" role="alert">
                                <i class="bi bi-geo-alt fs-4 me-2"></i>Hiện tại chưa có địa chỉ nào.
                            </div>
                            <?php } ?>
                        </div>

                    </div>


                </main>

            </div>
        </div>
    </div>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/plugins.js"></script>

<!-- Main JS -->
<script src="assets/js/main.js"></script>
</body>

</html>