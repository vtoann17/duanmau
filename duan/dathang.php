<?php 
session_start();
require_once "db_utils.php";
$db = new DB_UTILS();
$idNguoiDung = $_SESSION['user']['id'];

$cart = $db->getAll('SELECT c.ten, b.size, a.kichCoID, a.id, a.soLuong, a.gia FROM giohang a join kichco b on a.kichCoID = b.id join sanpham c on b.idSanPham = c.id where nguoiDungID = ?',[$idNguoiDung]);
$tong = $db->getValue("SELECT SUM(gia * soLuong) FROM giohang WHERE nguoiDungID = ?", [$idNguoiDung]) ?? 0;
$diaChi = $db->getAll("SELECT a.id,b.maGHN,b.ten as tenXa,c.ten as tenHuyen,d.ten as tenTinh,a.macDinh,a.chiTiet 
FROM diachi a 
JOIN xa b ON a.idXa = b.maGHN 
JOIN huyen c ON b.idHuyen = c.maGHN 
JOIN tinh d ON c.idTinh = d.maGHN 
WHERE idNguoiDung = ?", [$idNguoiDung]);

// Phí ship mặc định
$fromDistrict = 1552;
$phiShip = 30000;
$ghiChuShip = "Phí mặc định";
$loaiGHN = $_POST["loaiGHN"] ?? 2;
$thongBaoGHN = "";

// Xử lý địa chỉ được chọn
$idXa = null;
if (!empty($_POST["diaChi"])) {
    $dc = $db->getOne("SELECT * FROM diachi WHERE id = ?", [$_POST["diaChi"]]);
    $idXa = $dc["idXa"] ?? null;
} else {
    $macDinh = $db->getOne("SELECT idXa FROM diachi WHERE idNguoiDung = ? AND macDinh = 1", [$idNguoiDung]);
    $idXa = $macDinh["idXa"] ?? null;
}

// Gọi API GHN nếu có xã
if ($idXa) {
    $xa = $db->getOne("SELECT b.maGHN AS wardCode, c.maGHN AS districtId 
        FROM xa b 
        JOIN huyen c ON b.idHuyen = c.maGHN 
        WHERE b.maGHN = ?", [$idXa]);

    if ($xa && $xa["wardCode"] && $xa["districtId"]) {
        $postData = [
            "service_type_id"   => (int)$loaiGHN,
            "insurance_value"   => 1000000,
            "coupon"            => null,
            "from_district_id"  => $fromDistrict,
            "to_district_id"    => (int)$xa["districtId"],
            "to_ward_code"      => (string)$xa["wardCode"],
            "weight"            => 500,
            "length"            => 20,
            "width"             => 20,
            "height"            => 10
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Token: " . GHN_TOKEN,
                "ShopId: " . GHN_SHOP_ID
            ]
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);

        if (!empty($result["data"]["total"])) {
            $phiShip = $result["data"]["total"];
            $ghiChuShip = "Phí GHN tính toán";
        } else {
            $thongBaoGHN = "<div class='alert alert-warning'>Không thể tính phí giao hàng cho địa chỉ này. Hệ thống sẽ dùng phí mặc định.</div>";
        }
    } else {
        $thongBaoGHN = "<div class='alert alert-danger'> Khu vực của bạn chưa được GHN hỗ trợ. Vui lòng chọn địa chỉ khác.</div>";
    }
} else {
    $thongBaoGHN = "<div class='alert alert-info'>Bạn chưa chọn địa chỉ giao hàng.</div>";
}

// echo "<pre>";
// var_dump($phiShip);
// var_dump($ghiChuShip);
// var_dump($thongBaoGHN);
// exit;
// echo "<pre>";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    if (isset($_POST['dat'])) {
        $ten = trim($_POST["ten"] ?? '');
        $sdt = trim($_POST["sdt"] ?? '');
        $diaChi = $_POST["diaChi"] ?? '';
        $phuongTT = $_POST["pttt"] ?? '';
        if ($ten === '') {
            $errors['ten'] = "Vui lòng nhập tên người nhận.";
        }

        if ($sdt === '') {
            $errors['sdt'] = "Vui lòng nhập số điện thoại.";
        }

        if ($diaChi === '') {
            $errors['diaChi'] = "Vui lòng chọn địa chỉ giao hàng.";
        }

        if ($phuongTT === '') {
            $errors['pttt'] = "Vui lòng chọn phương thức thanh toán.";
        }
        if (empty($errors)) {
            $db->execute("INSERT INTO donhang(ten,sdt,idDc,phuongTT,nguoiDungID,tongTien,phiShip) VALUES(?,?,?,?,?,?,?)", [$ten, $sdt, $diaChi, $phuongTT, $idNguoiDung, $tong + $phiShip, $phiShip]);
            $idDH =  $db->getLastInsertId();

            foreach ($cart as $c) {
                $tongCart = $c["soLuong"] * $c["gia"];
                $db->execute("INSERT INTO chitietdonhang(donHangID, bienTheID, soLuong, gia, tong) VALUES(?,?,?,?,?)", [$idDH, $c["kichCoID"], $c["soLuong"], $c["gia"], $tongCart]);
                $db->execute("DELETE FROM giohang WHERE id = ?", [$c["id"]]);
            }

            $chiTiet = $db->getAll("SELECT bienTheID, soLuong FROM chitietdonhang WHERE donHangID = ?", [$idDH]);
            foreach ($chiTiet as $item) {
                $idSize = $item['bienTheID'];
                $soLuong = $item['soLuong'];
                $db->execute("UPDATE kichco SET soLuong = soLuong - ? WHERE id = ?", [$soLuong, $idSize]);
            }

            $_SESSION["thongBao"] = "Đặt hàng thành công!";
            header("Location: dathang.php");
            exit;
        }
    }
}


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
    <div class="container my-5">
        <h2 class="mb-4">Thông tin đặt hàng</h2>
        <?php if (isset($_SESSION['thongBao'])): ?>
        <div class="alert alert-success text-center">
            <?= $_SESSION['thongBao']; unset($_SESSION['thongBao']); ?>
        </div>
        <?php endif; ?>
        <form method="POST" action="">
            <!-- Thông tin khách hàng -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="hoTen" class="form-label">Tên</label>
                    <input type="text" name="ten" class="form-control" id="ten" placeholder="Nhập tên..">
                     <?php if (!empty($errors['ten'])): ?>
            <div style="color: red;"><?= $errors['ten'] ?></div>
        <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="soDienThoai" class="form-label">Số điện thoại</label>
                    <input type="tel" name="sdt" class="form-control" id="soDienThoai"
                        placeholder="Nhập số điện thoại...">
                         <?php if (!empty($errors['sdt'])): ?>
            <div style="color: red;"><?= $errors['sdt'] ?></div>
        <?php endif; ?>
                </div>
            </div>

            <!-- Địa chỉ -->
            <div class="mb-3">
                <label for="diaChi" class="form-label">Tỉnh/Thành phố</label>
                <select onchange="this.form.submit()" name="diaChi" class="form-select" required>
                    <option value="">Chọn địa chỉ</option>
                    <?php foreach ($diaChi as $item) {
                                        $selected = '';
                                        if (isset($_POST["diaChi"]) && $_POST["diaChi"] == $item["id"]) {
                                            $selected = "selected";
                                        } elseif (!isset($_POST["diaChi"]) && $item["macDinh"] == "1") {
                                            $selected = "selected";
                                        }
                                    ?>
                    <option <?= $selected ?> value="<?= $item["id"] ?>">
                        <?= "{$item["chiTiet"]}, {$item["tenXa"]}, {$item["tenHuyen"]}, {$item["tenTinh"]}" ?>
                    </option>
                    <?php } ?>
                </select>
                 <?php if (!empty($errors['diaChi'])): ?>
            <div style="color: red;"><?= $errors['diaChi'] ?></div>
        <?php endif; ?>
            </div>

            <!-- Phương thức thanh toán -->
            <div class="mb-3">
                <label class="form-label">Phương thức thanh toán</label>
                <div class="form-check">
                    <input class="form-check-input" value="cod" type="radio" name="pttt" id="cod" checked>
                    <label class="form-check-label" for="cod">Thanh toán khi nhận hàng (COD)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" value="bank" type="radio" name="pttt" id="bank">
                    <label class="form-check-label" for="bank">Chuyển khoản ngân hàng</label>
                </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <h4 class="mt-4">Sản phẩm</h4>
            <ul class="list-group mb-3">
                <?php foreach($cart as $ct):?>
                <li class="list-group-item d-flex justify-content-between">
                    <div>
                        <h6 class="my-0"><?= $ct['ten']?></h6>
                        <small class="text-muted"><?= $ct['soLuong']?></small>
                    </div>
                    <span class="text-muted"><?= number_format($ct['gia'])?>đ</span>
                </li>
            </ul>
            <?php endforeach ?>

            <!-- Tính tiền -->
            <ul class="list-group mb-4">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Tạm tính</span>
                    <strong><?= number_format($tong) ?>₫</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Phí vận chuyển</span>
                    <span><?= number_format($phiShip) ?>₫</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Tổng cộng</span>
                    <strong><?= number_format($tong + $phiShip) ?>₫</strong>
                </li>
            </ul>

            <!-- Nút đặt hàng -->
            <button type="submit" name="dat" class="btn btn-primary w-100">Đặt hàng</button>
        </form>
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