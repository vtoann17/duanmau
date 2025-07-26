<?php
session_start();
require_once "db_utils.php";

$db = new Database();
$connect = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
    $danhMucID = intval($_GET['id']);

    $stmt = $connect->prepare("DELETE FROM danhmuc WHERE id = :id");
    $stmt->bindParam(':id', $danhMucID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Đã xóa danh mục thành công!";
    } else {
        $_SESSION['message'] = "Không thể xóa danh mục!";
    }

    header("Location: quanly_danhmuc.php");
    exit();
} else {
    echo "ID không hợp lệ!";
}
?>
