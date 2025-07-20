<?php
session_start();

require_once "db_utils.php";
$db_util = new DB_UTILS();

// Lấy dữ liệu
$sanphams = $db_util->getAll("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.created_at DESC
");
$donhangs = $db_util->getAll("SELECT * FROM orders ORDER BY created_at DESC");
$nguoidungs = $db_util->getAll("SELECT * FROM users ORDER BY created_at DESC");
$danhmucs = $db_util->getAll("SELECT * FROM categories ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
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
  .admin-section  {
    max-width: 1800px; /* tăng từ 700 lên 1200 */
    margin: 40px auto;
    padding: 30px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}
 .admin-header {
    display: flex;
    flex-direction: column;
    align-items: center; /* căn giữa theo chiều ngang */
    justify-content: center;
    text-align: center;
    margin-top: 40px;
}

.admin-section table {
    width: 100%; /* đảm bảo bảng chiếm toàn bộ chiều ngang */
    border-collapse: collapse;
    margin-top: 15px;
}
.admin-section th, .admin-section td {
    border: 1px solid #ccc;
    padding: 12px 15px;
    text-align: center;
    font-size: 14px;
}
.container {
    max-width: 100%;
    padding: 0 20px;
}

.admin-section h2 {
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 25px;
    color: #333;
}

.admin-buttons {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
}

.admin-buttons a {
    padding: 12px 20px;
    font-size: 15px;
    font-weight: 500;
    background-color: #ff6600;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.admin-buttons a:hover {
    background-color: #e65500;
}
@media (max-width: 576px) {
    .admin-buttons a {
        width: 100%;
        text-align: center;
    }
}
.btn {
    padding: 6px 12px;
    border-radius: 5px;
    text-decoration: none;
    margin-right: 5px;
    font-size: 14px;
}
.btn-add { background: #ff6600; color: #fff; }
.btn-edit { background: #ffc107; color: #000; }
.btn-delete { background: #dc3545; color: #fff; }

.admin-section table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
.admin-section th, .admin-section td {
    border: 1px solid #ccc;
    padding: 10px;
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

<div class="admin-container">
    <div class="admin-header">
        <h1>Quản lý cửa hàng</h1>
        <p>Xin chào, Admin!</p>
    </div>

    <!-- Quản lý sản phẩm -->
    <div class="admin-section">
        <h2>Sản phẩm</h2>
        <?php if (isset($_SESSION['message'])): ?>
    <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 5px;">
        <?= $_SESSION['message'] ?>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>
        <a href="them_sanpham.php" class="btn btn-add"> Thêm sản phẩm</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Tên</th><th>Miêu tả</th><th>Giá</th><th>Tồn kho</th><th>Ảnh</th><th>Loại</th><th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sanphams as $sp): ?>
                <tr>
                    <td><?= $sp['id'] ?></td>
                    <td><?= $sp['name'] ?></td>
                    <td><?= $sp['description'] ?></td>
                    <td><?= $sp['price'] ?> đ</td>
                    <td><?= $sp['stock'] ?></td>
                    <td><img src="<?= $sp['image_url'] ?>" width="60"></td>
                    <td><?= $sp['category_name'] ?></td>
                    <td>
                        <a class="btn btn-edit" href="sua_sanpham.php?id=<?= $sp['id'] ?>">Sửa</a>
                        <a class="btn btn-delete" href="delete_sanpham.php?id=<?= $sp['id'] ?>">Xóa</a>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <!-- Quản lý đơn hàng -->
    <div class="admin-section">
        <h2>Đơn hàng</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Khách hàng</th><th>Tổng</th><th>Ngày</th><th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donhangs as $dh): ?>
                <tr>
                    <td><?= $dh['id'] ?></td>
                    <td><?= $dh['user_id'] ?></td>
                    <td><?= $dh['total'] ?> đ</td>
                    <td><?= $dh['created_at'] ?></td>
                    <td><a class="btn btn-edit" href="#">Xem</a></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <!-- Quản lý người dùng -->
    <div class="admin-section">
        <h2>Người dùng</h2>
        <a href="them_nguoidung.php" class="btn btn-add"> Thêm người dùng</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Tên</th><th>Email</th><th>Vai trò</th><th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($nguoidungs as $nd): ?>
                <tr>
                    <td><?= $nd['id'] ?></td>
                    <td><?= $nd['name'] ?></td>
                    <td><?= $nd['email'] ?></td>
                    <td><?= $nd['role'] ?></td>
                    <td>
                        <a class="btn btn-edit" href="them_nguoidung.php?id=<?= $nd['id'] ?>">Sửa</a>
                        <a class="btn btn-delete" href="?delete_user=<?= $nd['id'] ?>" onclick="return confirm('Xóa người dùng?')">Xóa</a>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <!-- Quản lý danh mục -->
    <div class="admin-section">
        <h2>Danh mục</h2>
        <a href="them_danhmuc.php" class="btn btn-add"> Thêm danh mục</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Tên danh mục</th><th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($danhmucs as $dm): ?>
                <tr>
                    <td><?= $dm['id'] ?></td>
                    <td><?= $dm['name'] ?></td>
                    <td>
                        <a class="btn btn-edit" href="them_danhmuc.php?id=<?= $dm['id'] ?>">Sửa</a>
                        <a class="btn btn-delete" href="?delete_category=<?= $dm['id'] ?>" onclick="return confirm('Xóa danh mục?')">Xóa</a>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

    </div>
    <!-- Plugins JS -->
<script src="assets/js/plugins.js"></script>

<!-- Main JS -->
<script src="assets/js/main.js"></script>
</body>
</html>
