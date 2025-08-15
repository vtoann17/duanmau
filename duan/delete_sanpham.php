<?php
session_start();
require_once "db_utils.php";

$db = new Database();
$connect = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (!empty($_GET['id'])) {
        $maSanPham = (int) $_GET['id'];
        $danhSachBang = [
            "anhsanpham" => "sanPhamID",
            "danhgia" => "sanPhamID",
            "dsyeuthich" => "sanPhamID",
            "giohang" => "sanPhamID",
            "kichco" => "idSanPham"
        ];

        foreach ($danhSachBang as $tenBang => $cotLienKet) {
            $lenhXoa = $connect->prepare("DELETE FROM {$tenBang} WHERE {$cotLienKet} = :id");
            $lenhXoa->bindParam(':id', $maSanPham, PDO::PARAM_INT);
            $lenhXoa->execute();
        }

        $lenhXoa = $connect->prepare("DELETE FROM sanpham WHERE id = :id");
        $lenhXoa->bindParam(':id', $maSanPham, PDO::PARAM_INT);
        $lenhXoa->execute();

        $_SESSION['message'] = 'Đã xóa sản phẩm thành công!';

        header("Location: quanly_sanpham.php");
        exit();
    } else {
        echo "Mã sản phẩm không hợp lệ!";
    }
}
?>
