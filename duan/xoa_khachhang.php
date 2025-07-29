<?php
session_start();
require_once "db_utils.php";

$db_ultils = new Database();
$connect = $db_ultils->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
    $khachhangID = intval($_GET['id']);

    $stmt = $connect->prepare("DELETE FROM nguoidung WHERE id = :id");
    $stmt->bindParam(':id', $khachhangID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Đã xóa khách hàng thành công!";
    } else {
        $_SESSION['message'] = "Không thể xóa khách hàng!";
    }

    header("Location: quanly_khachhang.php");
    exit();
} else {
    echo "ID không hợp lệ!";
}
?>