<?php
session_start();
require_once "db_utils.php";

$db = new Database();
$connect = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
    $maDanhMuc = intval($_GET['id']);
    $laySP = $connect->prepare("SELECT id FROM sanpham WHERE danhMucID = :dmID");
    $laySP->bindParam(':dmID', $maDanhMuc, PDO::PARAM_INT);
    $laySP->execute();
    $danhSachSP = $laySP->fetchAll(PDO::FETCH_COLUMN);

    foreach ($danhSachSP as $maSP) {
        $connect->prepare("DELETE FROM anhsanpham WHERE sanPhamID = :id")
                ->execute([':id' => $maSP]);

        $connect->prepare("DELETE FROM kichco WHERE idSanPham = :id")
                ->execute([':id' => $maSP]);

        $connect->prepare("DELETE FROM giohang WHERE sanPhamID = :id")
                ->execute([':id' => $maSP]);

        $connect->prepare("DELETE FROM dsyeuthich WHERE sanPhamID = :id")
                ->execute([':id' => $maSP]);

        $connect->prepare("DELETE FROM sanpham WHERE id = :id")
                ->execute([':id' => $maSP]);
    }
    $stmt = $connect->prepare("DELETE FROM danhmuc WHERE id = :id");
    $stmt->bindParam(':id', $maDanhMuc, PDO::PARAM_INT);

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
