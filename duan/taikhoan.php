<?php
session_start();
require_once "db_utils.php";
$db_util = new DB_UTILS();



$nguoiDungID = $_SESSION['user']['id'];
$nguoidungs = $db_util->getOne("SELECT * FROM nguoidung WHERE id = ?", [$nguoiDungID]);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = $_POST['ten'];
    $email = $_POST['email'];
    $sdt = $_POST['sdt'];
    $gioiTinh = $_POST['gioitinh'];
    $ngaySinh = $_POST['ngaySinh'];

    // Upload avatar nếu có
    $avatar = $nguoidungs['avatar'];
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $uploadDir = 'uploads/avatars/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = uniqid() . "_" . basename($_FILES['avatar']['name']);
        $targetPath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
            $avatar = $targetPath;
        }
    }

    // Cập nhật dữ liệu vào bảng nguoidung
    $sql = "UPDATE nguoidung SET ten = ?, email = ?, soDienThoai = ?, gioiTinh = ?, ngaySinh = ?, avatar = ? WHERE id = ?";
    $params = [$ten, $email, $sdt, $gioiTinh, $ngaySinh, $avatar, $nguoiDungID];
    $db_util->execute($sql, $params);

    // Reload lại dữ liệu sau khi lưu
    $nguoidungs = $db_util->getOne("SELECT * FROM nguoidung WHERE id = ?", [$nguoiDungID]);

    echo "<script>alert('Cập nhật thành công!');</script>";
}

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
    <style>
    <style>body {
        background-color: #f8f9fa;
        padding: 20px;
    }

    .profile-card {
        background: white;
        border-radius: 10px;
        max-width: 900px;
        margin: auto;
        flex: 1;
        height: 600px;
        overflow-y: auto;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-left: auto;
        margin-top: 0;
    }

    .sidebar {
        background-color: white;
        color: black;
        padding: 1rem;
        height: 100vh;
        width: 250px;
        border: 1px solid #ccc;
        border-radius: 8px;
    }

    .d-flex {
        display: flex;
        align-items: stretch;
    }


    .avatar-preview {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ccc;
    }

    .form-section label {
        font-weight: bold;
    }
    </style>
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
                                        <?php  ?>
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
        <div class="profile-card">
            <h3><i class="fas fa-user-circle me-2 text-danger"></i>Hồ Sơ Của Tôi</h3>
            <p class="text-muted">Quản lý thông tin hồ sơ để bảo mật tài khoản</p>

            <form method="POST" action="#" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="ten" class="form-label">Tên</label>
                        <input type="text" id="ten" name="ten" class="form-control" value="<?= $nguoidungs['ten'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control"
                            value="<?= $nguoidungs['email'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="sdt" class="form-label">Số điện thoại</label>
                        <input type="text" id="sdt" name="sdt" class="form-control"
                            value="<?= $nguoidungs['soDienThoai'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Giới tính</label>
                        <div class="d-flex gap-3 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gioitinh" value="Nam"
                                    <?= $nguoidungs['gioiTinh'] == 'Nam' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="nam">Nam</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gioitinh" value="Nữ"
                                    <?= $nguoidungs['gioiTinh'] == 'Nữ' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="nu">Nữ</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gioitinh" value="Khác"
                                    <?= $nguoidungs['gioiTinh'] == 'Khác' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="khac">Khác</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="ngaysinh" class="form-label">Ngày sinh</label>
                        <input type="date" id="ngaySinh" name="ngaySinh" class="form-control"
                            value="<?= $nguoidungs['ngaySinh'] ?>">
                    </div>
                    <div class="col-md-6 text-center">
                        <label class="form-label">Ảnh đại diện</label>
                        <div>
                            <img src="<?= $nguoidungs['avatar'] ?: 'img/icon/pngtree-male-account-profile-worker-vector-picture-image_10398976.png' ?>"
                                alt="Ảnh đại diện" class="avatar-preview mb-2">
                        </div>
                        <input class="form-control" type="file" name="avatar" accept=".jpg,.jpeg,.png">
                        <small class="text-muted">Dung lượng tối đa 1MB<br>Định dạng: JPG, PNG</small>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-danger px-4"><i class="fas fa-save me-2"></i>Lưu</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Plugins JS -->
    <script src="assets/js/plugins.js"></script>

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>
</body>

</html>