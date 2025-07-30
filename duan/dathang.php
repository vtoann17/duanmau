<?php 
session_start();
require_once "db_utils.php";
$db = new DB_UTILS();
$idNguoiDung = $_SESSION['user']['id'];

$cart = $db->getAll('SELECT c.ten, b.size, a.kichCoID, a.id, a.soLuong, a.gia FROM giohang a join kichco b on a.kichCoID = b.id join sanpham c on b.idSanPham = c.id where nguoiDungID = ?',[$idNguoiDung]);
$tong = $db->getValue("SELECT SUM(gia * soLuong) FROM giohang WHERE nguoiDungID = ?", [$idNguoiDung]) ?? 0;
$diaChi = $db->getAll("SELECT a.id,b.maGHN,b.ten as tenXa,c.ten as tenHuyen,d.ten as tenTinh,a.macDinh,a.chiTiet 
FROM diachi a 
JOIN xa b ON a.idXa = b.maGHN 
JOIN huyen c ON b.idHuyen = c.maGHN 
JOIN tinh d ON c.idTinh = d.maGHN 
WHERE idNguoiDung = ?", [$idNguoiDung]);

// Phí ship mặc định
$fromDistrict = 1552;
$phiShip = 30000;
$ghiChuShip = "Phí mặc định";
$loaiGHN = $_POST["loaiGHN"] ?? 2;
$thongBaoGHN = "";

// Xử lý địa chỉ được chọn
$idXa = null;
if (!empty($_POST["diaChi"])) {
    $dc = $db->getOne("SELECT * FROM diachi WHERE id = ?", [$_POST["diaChi"]]);
    $idXa = $dc["idXa"] ?? null;
} else {
    $macDinh = $db->getOne("SELECT idXa FROM diachi WHERE idNguoiDung = ? AND macDinh = 1", [$idNguoiDung]);
    $idXa = $macDinh["idXa"] ?? null;
}

// Gọi API GHN nếu có xã
if ($idXa) {
    $xa = $db->getOne("SELECT b.maGHN AS wardCode, c.maGHN AS districtId 
        FROM xa b 
        JOIN huyen c ON b.idHuyen = c.maGHN 
        WHERE b.maGHN = ?", [$idXa]);

    if ($xa && $xa["wardCode"] && $xa["districtId"]) {
        $postData = [
            "service_type_id"   => (int)$loaiGHN,
            "insurance_value"   => 1000000,
            "coupon"            => null,
            "from_district_id"  => $fromDistrict,
            "to_district_id"    => (int)$xa["districtId"],
            "to_ward_code"      => (string)$xa["wardCode"],
            "weight"            => 500,
            "length"            => 20,
            "width"             => 20,
            "height"            => 10
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Token: " . GHN_TOKEN,
                "ShopId: " . GHN_SHOP_ID
            ]
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);

        if (!empty($result["data"]["total"])) {
            $phiShip = $result["data"]["total"];
            $ghiChuShip = "Phí GHN tính toán";
        } else {
            $thongBaoGHN = "<div class='alert alert-warning'>⚠ Không thể tính phí giao hàng cho địa chỉ này. Hệ thống sẽ dùng phí mặc định.</div>";
        }
    } else {
        $thongBaoGHN = "<div class='alert alert-danger'>❌ Khu vực của bạn chưa được GHN hỗ trợ. Vui lòng chọn địa chỉ khác.</div>";
    }
} else {
    $thongBaoGHN = "<div class='alert alert-info'>💬 Bạn chưa chọn địa chỉ giao hàng.</div>";
}

// echo "<pre>";
// var_dump($phiShip);
// var_dump($ghiChuShip);
// var_dump($thongBaoGHN);
// exit;
// echo "<pre>";

if($_SERVER["REQUEST_METHOD"] == 'POST'){
    if(isset($_POST['dat'])){
        $ten = $_POST["ten"];
        $sdt = $_POST["sdt"];
        $diaChi = $_POST["diaChi"];
        $phuongTT = $_POST["pttt"];
        $db->execute("INSERT INTO donhang(ten,sdt,idDc,phuongTT,nguoiDungID,tongTien,phiShip) VALUES(?,?,?,?,?,?,?)", [$ten, $sdt, $diaChi, $phuongTT,$idNguoiDung,$tong+$phiShip,$phiShip]);
        $idDH =  $db -> getLastInsertId();
        foreach($cart as $c){
            $tongCart = $c["soLuong"]*$c["gia"];
             $db->execute("INSERT INTO chitietdonhang(donHangID, bienTheID, soLuong, gia, tong) VALUES(?,?,?,?,?)", [$idDH, $c["kichCoID"],$c["soLuong"],$c["gia"],$tongCart]);
            $db->execute("DELETE FROM giohang WHERE id = ?", [$c["id"]]);
        }

         $chiTiet = $db->getAll("SELECT bienTheID, soLuong FROM chitietdonhang WHERE donHangID = ?", [$idDH]);

        // Cập nhật lại số lượng tồn kho cho từng idSize
        foreach ($chiTiet as $item) {
            $idSize = $item['bienTheID'];
            $soLuong = $item['soLuong'];

            $db->execute("UPDATE kichco SET soLuong = soLuong - ? WHERE id = ?", [$soLuong, $idSize]);
        }
        

    }
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container my-5">
        <h2 class="mb-4">Thông tin đặt hàng</h2>
        <form method="POST" action="">
            <!-- Thông tin khách hàng -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="hoTen" class="form-label">Tên</label>
                    <input type="text" name="ten" class="form-control" id="ten" placeholder="Nhập tên..">
                </div>
                <div class="col-md-6">
                    <label for="soDienThoai" class="form-label">Số điện thoại</label>
                    <input type="tel" name="sdt" class="form-control" id="soDienThoai"
                        placeholder="Nhập số điện thoại...">
                </div>
            </div>

            <!-- Địa chỉ -->
            <div class="mb-3">
                <label for="diaChi" class="form-label">Tỉnh/Thành phố</label>
                <select onchange="this.form.submit()" name="diaChi" class="form-select" required>
                    <option value="">Chọn địa chỉ</option>
                    <?php foreach ($diaChi as $item) {
                                        $selected = '';
                                        if (isset($_POST["diaChi"]) && $_POST["diaChi"] == $item["id"]) {
                                            $selected = "selected";
                                        } elseif (!isset($_POST["diaChi"]) && $item["macDinh"] == "1") {
                                            $selected = "selected";
                                        }
                                    ?>
                    <option <?= $selected ?> value="<?= $item["id"] ?>">
                        <?= "{$item["chiTiet"]}, {$item["tenXa"]}, {$item["tenHuyen"]}, {$item["tenTinh"]}" ?>
                    </option>
                    <?php } ?>
                </select>
            </div>

            <!-- Phương thức thanh toán -->
            <div class="mb-3">
                <label class="form-label">Phương thức thanh toán</label>
                <div class="form-check">
                    <input class="form-check-input" value="cod" type="radio" name="pttt" id="cod" checked>
                    <label class="form-check-label" for="cod">Thanh toán khi nhận hàng (COD)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" value="bank" type="radio" name="pttt" id="bank">
                    <label class="form-check-label" for="bank">Chuyển khoản ngân hàng</label>
                </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <h4 class="mt-4">Sản phẩm</h4>
            <ul class="list-group mb-3">
                <?php foreach($cart as $ct):?>
                <li class="list-group-item d-flex justify-content-between">
                    <div>
                        <h6 class="my-0"><?= $ct['ten']?></h6>
                        <small class="text-muted"><?= $ct['soLuong']?></small>
                    </div>
                    <span class="text-muted"><?= number_format($ct['gia'])?>đ</span>
                </li>
            </ul>
            <?php endforeach ?>

            <!-- Tính tiền -->
            <ul class="list-group mb-4">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Tạm tính</span>
                    <strong><?= number_format($tong) ?>₫</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Phí vận chuyển</span>
                    <span><?= number_format($phiShip) ?>₫</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Tổng cộng</span>
                    <strong><?= number_format($tong + $phiShip) ?>₫</strong>
                </li>
            </ul>

            <!-- Nút đặt hàng -->
            <button type="submit" name="dat" class="btn btn-primary w-100">Đặt hàng</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>