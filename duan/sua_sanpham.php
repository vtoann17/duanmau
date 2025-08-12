<?php
session_start();
require_once "db_utils.php";
$db_util = new DB_UTILS();

// Lấy danh sách danh mục
$danhmucs = $db_util->getAll("SELECT * FROM danhmuc");

$sanpham = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sanpham = $db_util->getOne("SELECT * FROM sanpham WHERE id = ?", [$id]);
    $anhs = $db_util->getAll("SELECT * FROM anhsanpham WHERE sanPhamID = ?", [$id]);
}

// Khi submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'] ?? null;
    $ten = $_POST['name'];
    $moTa = $_POST['description'];
    $gia = $_POST['price'];
    $danhMucID = $_POST['category_id'];

    if ($id) {
        // Cập nhật sản phẩm
        $sql = "UPDATE sanpham SET ten=?, moTa=?, gia=?, danhMucID=? WHERE id=?";
        $db_util->execute($sql, [$ten, $moTa, $gia, $danhMucID, $id]);
    } else {
        // Thêm mới
        $sql = "INSERT INTO sanpham (ten, moTa, gia, danhMucID) VALUES (?, ?, ?, ?)";
        $db_util->execute($sql, [$ten, $moTa, $gia, $danhMucID]);
        $id = $db_util->getLastInsertId();
    }

    // Cập nhật ảnh nếu có
    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) mkdir($upload_dir, 0755, true);

    // Xóa ảnh cũ
    $db_util->execute("DELETE FROM anhsanpham WHERE sanPhamID = ?", [$id]);

    // Ảnh chính (image[])
    if (!empty($_FILES['image']['name'][0])) {
        foreach ($_FILES['image']['tmp_name'] as $index => $tmpName) {
            $fileName = basename($_FILES['image']['name'][$index]);
            $targetPath = $upload_dir . time() . '_' . $fileName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                $isMain = ($index === 0) ? 1 : 0; // Ảnh đầu tiên làm ảnh chính
                $db_util->execute(
                    "INSERT INTO anhsanpham (sanPhamID, anh, anhChinh) VALUES (?, ?, ?)",
                    [$id, $targetPath, $isMain]
                );
            }
        }
    }

    // Ảnh phụ (anh[])
    if (!empty($_FILES['anh']['name'][0])) {
        foreach ($_FILES['anh']['tmp_name'] as $index => $tmpName) {
            $fileName = basename($_FILES['anh']['name'][$index]);
            $targetPath = $upload_dir . time() . '_' . $fileName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                $db_util->execute(
                    "INSERT INTO anhsanpham (sanPhamID, anh) VALUES (?, ?)",
                    [$id, $targetPath]
                );
            }
        }
    }

    $_SESSION['message'] = "Đã lưu sản phẩm thành công!";
    header("Location: quanly_sanpham.php");
    exit;
}
?>



<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm sản phẩm</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    
    <!-- CSS 
    ========================= -->


    <!-- Plugins CSS -->
    <link rel="stylesheet" href="assets/css/plugins.css">
    
    <!-- Main Style CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        label {
            font-weight: 500;
            display: block;
            margin-top: 12px;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            margin-top: 20px;
            padding: 10px 18px;
            background-color: #ff6600;
            border: none;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #ff6600;
        }
        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
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
                                                <a href="cart.php?delete_id=<?= $gh['id'] ?>"><i class="ion-android-close"></i></a>
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

    <div class="form-container">
        <h2>Sửa sản phẩm</h2>
         <form method="POST" enctype="multipart/form-data">
        <?php if ($sanpham): ?>
            <input type="hidden" name="id" value="<?= $sanpham['id'] ?>">
        <?php endif; ?>

        <label>Tên sản phẩm:</label>
        <input type="text" name="name" value="<?= $sanpham['ten'] ?? '' ?>" required>

        <label>Mô tả:</label>
        <textarea name="description" required><?= $sanpham['moTa'] ?? '' ?></textarea>

        <label>Giá:</label>
        <input type="number" name="price" value="<?= $sanpham['gia'] ?? '' ?>" required>

        <label>Danh mục:</label>
        <select name="category_id" required>
            <?php foreach ($danhmucs as $dm): ?>
                <option value="<?= $dm['id'] ?>" <?= (isset($sanpham) && $sanpham['danhMucID'] == $dm['id']) ? 'selected' : '' ?>>
                    <?= $dm['tenDanhMuc'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Tải ảnh lên:</label>
            <input type="file" name="image[]" multiple accept="image/*"><br>
            <label>Ảnh phụ:</label>
            <input type="file" name="anh[]" multiple accept="image/*"><br>

        <button type="submit"><?= $sanpham ? 'Cập nhật' : 'Thêm mới' ?></button>
        <a href="quanly_sanpham.php" class="back-link">← Quay lại quản lý</a>
    </form>
    </div>
      </div>
    <!-- Plugins JS -->
<script src="assets/js/plugins.js"></script>

<!-- Main JS -->
<script src="assets/js/main.js"></script>
</body>
</html>
