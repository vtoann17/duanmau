<?php
session_start();
require_once "db_utils.php";

$db = new Database();
$connect = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (!empty($_GET['id'])) {
        $sanPhamID = $_GET['id'];

        try {
            $connect->beginTransaction();

            // Xóa ảnh sản phẩm
            $stmt = $connect->prepare("DELETE FROM anhsanpham WHERE sanPhamID = :id");
            $stmt->bindParam(':id', $sanPhamID, PDO::PARAM_INT);
            $stmt->execute();

            // Xóa biến thể (size) sản phẩm
            $stmt = $connect->prepare("DELETE FROM bienthesanpham WHERE sanPhamID = :id");
            $stmt->bindParam(':id', $sanPhamID, PDO::PARAM_INT);
            $stmt->execute();

            // Xóa sản phẩm chính
            $stmt = $connect->prepare("DELETE FROM sanpham WHERE id = :id");
            $stmt->bindParam(':id', $sanPhamID, PDO::PARAM_INT);
            $stmt->execute();

            $connect->commit();
            $_SESSION['message'] = 'Đã xóa sản phẩm thành công!';
        } catch (Exception $e) {
            $connect->rollBack();
            $_SESSION['message'] = 'Lỗi khi xóa sản phẩm: ' . $e->getMessage();
        }

        header("Location: quanly_sanpham.php");
        exit();
    } else {
        echo "ID không hợp lệ!";
    }
}
?>
