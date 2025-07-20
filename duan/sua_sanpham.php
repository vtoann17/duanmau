<?php
session_start();
require_once "db_utils.php";
$db_util = new DB_UTILS();

// Danh sách danh mục để chọn
$danhmucs = $db_util->getAll("SELECT * FROM categories");

$sanpham = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sanpham = $db_util->getOne("SELECT * FROM products WHERE id = ?", [$id]);
}

// Khi submit form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image_url = $_POST['image_url'];
    $category_id = $_POST['category_id'];


    // Thêm mới hoặc cập nhật
    if ($id) {
        // Cập nhật
        $sql = "UPDATE products SET name=?, description=?, price=?, stock=?, image_url=?, category_id=? WHERE id=?";
        $db_util->execute($sql, [$name, $description, $price, $stock, $image_url, $category_id, $id]);
    } else {
        // Thêm mới
        $sql = "INSERT INTO products (name, description, price, stock, image_url, category_id) VALUES (?, ?, ?, ?, ?, ?)";
        $db_util->execute($sql, [$name, $description, $price, $stock, $image_url, $category_id]);
    }

    header("Location: quanly.php");
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
            echo $_SESSION['user']['role'] == 'admin' ? 'Admin' : $_SESSION['user']['name'];
        } else {
            echo 'Tài khoản của tôi';
        }
    ?>
    <i class="ion-chevron-down"></i></a>
    <ul class="dropdown_links">
        <?php if (isset($_SESSION['user'])): ?>
            <li><a href="wishlist.html">Danh mục yêu thích</a></li>
            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
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
                                    <li class="mega_items"><a href="products.php">Sản phẩm <i class="fa fa-angle-down"></i></a>
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
                                        <li><a href="shop_category.php">Sản phẩm </a></li>
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
            <input type="hidden" name="id" value="<?= $sanpham['id'] ?>" readonly>
            <input type="hidden" name="old_image" value="<?= $sanpham['image_url'] ?>">
        <?php endif; ?>

        <label>Tên:</label><br>
        <input type="text" name="name" value="<?= $sanpham['name'] ?? '' ?>" required><br><br>

        <label>Miêu tả:</label><br>
        <textarea name="description" required><?= $sanpham['description'] ?? '' ?></textarea><br><br>

        <label>Giá:</label><br>
        <input type="number" name="price" value="<?= $sanpham['price'] ?? '' ?>" required><br><br>

        <label>Tồn kho:</label><br>
        <input type="number" name="stock" value="<?= $sanpham['stock'] ?? '' ?>" required><br><br>

        <label>Danh mục:</label><br>
        <select name="category_id" required>
            <?php foreach ($danhmucs as $dm): ?>
                <option value="<?= $dm['id'] ?>" <?= isset($sanpham) && $sanpham['category_id'] == $dm['id'] ? 'selected' : '' ?>>
                    <?= $dm['name'] ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

         <label for="image_url">Đường dẫn ảnh</label>
            <input type="text" id="image_url" name="image_url" required>

        <button type="submit"><?= $sanpham ? "Cập nhật" : "Thêm mới" ?></button>
        <a href="quanly.php">Quay lại</a>
    </form>
        <a href="quanly.php" class="back-link">← Quay lại quản lý</a>
    </div>
      </div>
    <!-- Plugins JS -->
<script src="assets/js/plugins.js"></script>

<!-- Main JS -->
<script src="assets/js/main.js"></script>
</body>
</html>
