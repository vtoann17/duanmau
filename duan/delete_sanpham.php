<?php
session_start();
require_once "db_utils.php";

$db = new Database();
$connect = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (!empty($_GET['id'])) {
        $query = "DELETE FROM products WHERE id = :id";
        $stmt = $connect->prepare($query);
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['message'] = 'Đã xóa sản phẩm thành công!';
        } else {
            $_SESSION['message'] = 'Lỗi khi xóa sản phẩm!';
        }

        header("Location: quanly.php");
        exit();
    } else {
        echo "ID không hợp lệ!";
    }
}
?>