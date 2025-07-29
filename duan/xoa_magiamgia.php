<?php
session_start();
require_once "db_utils.php";

$db_ultils = new Database();
$connect = $db_ultils->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
    $magiamID = intval($_GET['id']);

    $stmt = $connect->prepare("DELETE FROM magiamgia WHERE id = :id");
    $stmt->bindParam(':id', $magiamID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Đã xóa mã giảm giá thành công!";
    } else {
        $_SESSION['message'] = "Không thể xóa mã giảm giá!";
    }

    header("Location: quanly_magiamgia.php");
    exit();
} else {
    echo "ID không hợp lệ!";
}
?>