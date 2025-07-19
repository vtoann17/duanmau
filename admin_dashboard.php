<?php
session_start();
// Kiểm tra đăng nhập admin (giả sử đã có session)
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Lý Cửa Hàng - Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            padding: 30px;
            background-color: #f4f4f4;
        }
        .admin-container {
            max-width: 1200px;
            margin: auto;
        }
        .admin-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .admin-header h1 {
            color: #333;
        }
        .admin-section {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .admin-section h2 {
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .admin-buttons a {
            display: inline-block;
            background: #ff6a28;
            color: white;
            padding: 10px 20px;
            margin: 10px 10px 0 0;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.3s;
        }
        .admin-buttons a:hover {
            background: #e65a1e;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Quản lý cửa hàng</h1>
            <p>Xin chào, Admin!</p>
        </div>

        <div class="admin-section">
            <h2>Quản lý</h2>
            <div class="admin-buttons">
                <a href="quanly_sanpham.php">Sản phẩm</a>
                <a href="quanly_donhang.php">Đơn hàng</a>
                <a href="quanly_nguoidung.php">Người dùng</a>
                <a href="logout.php">Đăng xuất</a>
            </div>
        </div>
    </div>
</body>
</html>
