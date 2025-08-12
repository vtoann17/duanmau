<?php
session_start();
require_once "db_utils.php";
$db_utils = new DB_UTILS();
$limit =  $_GET["limit"] ?? 3;
$page = $_GET["page"] ??  1;
$offset = ($page - 1) * $limit;

$tongdong = $db_utils->getValue("SELECT COUNT(*) FROM donhang");
$sotrang = ceil($tongdong / $limit);
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user']['id'];
// $gioHang = $db_utils->getAll("
//     SELECT gh.id, gh.soLuong, 
//            sp.ten AS tensp, 
//            kc.size, 
//            sp.id AS sanPhamID,
//            sp.gia AS gia,
//            (sp.gia * gh.soLuong) AS thanhTien
//     FROM giohang gh
//     JOIN sanpham sp ON gh.sanPhamID = sp.id
//     JOIN kichco kc ON gh.kichCoID = kc.id
//     WHERE gh.nguoiDungID = ?
// ", [$userId]);
if (isset($_GET['action']) && $_GET['action'] === 'huy' && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $db_utils->execute("
        UPDATE donhang 
        SET trangThai = 'da_huy' 
        WHERE id = ? 
          AND nguoiDungID = ? 
          AND trangThai IN ('cho_xac_nhan', 'da_xac_nhan')
    ", [$id, $userId]);

    header("Location: donhang.php");
    exit;
}
// Xử lý Mua lại
if (isset($_GET['action']) && $_GET['action'] === 'mualai' && !empty($_GET['id'])) {
    $donhangId = (int)$_GET['id'];

    // Lấy danh sách sản phẩm từ đơn hàng + join bảng kichco để lấy idSanPham & size
    $sanphams = $db_utils->getAll("
        SELECT kc.idSanPham AS sanPhamID, kc.size, ct.soLuong
        FROM chitietdonhang ct
        JOIN donhang dh ON ct.donHangID = dh.id
        JOIN kichco kc ON ct.bienTheID = kc.id
        WHERE dh.id = ? AND dh.nguoiDungID = ?
    ", [$donhangId, $userId]);

    foreach ($sanphams as $sp) {
        // Kiểm tra sản phẩm đã có trong giỏ chưa (theo sanPhamID + size)
        $gioHang = $db_utils->getOne("
            SELECT id FROM giohang 
            WHERE nguoiDungID = ? AND sanPhamID = ? AND kichCoID = (
                SELECT id FROM kichco WHERE idSanPham = ? AND size = ?
            )
        ", [$userId, $sp['sanPhamID'], $sp['sanPhamID'], $sp['size']]);

        if ($gioHang) {
            // Nếu có thì cộng thêm số lượng
            $db_utils->execute("
                UPDATE giohang 
                SET soLuong = soLuong + ? 
                WHERE id = ?
            ", [$sp['soLuong'], $gioHang['id']]);
        } else {
            // Nếu chưa có thì thêm mới
            $kichCoID = $db_utils->getValue("
                SELECT id FROM kichco WHERE idSanPham = ? AND size = ?
            ", [$sp['sanPhamID'], $sp['size']]);

            if ($kichCoID) {
                // Lấy giá sản phẩm từ bảng sanpham
                $giaSP = $db_utils->getValue("
        SELECT gia FROM sanpham WHERE id = ?
    ", [$sp['sanPhamID']]);

                $db_utils->execute("
        INSERT INTO giohang (nguoiDungID, sanPhamID, kichCoID, soLuong, gia) 
        VALUES (?, ?, ?, ?, ?)
    ", [$userId, $sp['sanPhamID'], $kichCoID, $sp['soLuong'], $giaSP]);
            }
        }
    }

    header("Location: cart.php");
    exit;
}

$statusFilter = $_GET['status'] ?? '';
$params = [$userId];

// Nếu có lọc trạng thái
$sqlFilter = '';
if (!empty($statusFilter)) {
    $sqlFilter = " AND dh.trangThai = ? ";
    $params[] = $statusFilter;
}

$dsDonHang = $db_utils->getAll("
    SELECT dh.*, 
           CONCAT(dc.chiTiet, ', ', xa.ten, ', ', h.ten, ', ', t.ten) AS diaChiDayDu
    FROM donhang dh
    LEFT JOIN diachi dc ON dh.idDc = dc.id
    LEFT JOIN xa ON dc.idXa = xa.maGHN
    LEFT JOIN huyen h ON xa.idHuyen = h.maGHN
    LEFT JOIN tinh t ON h.idTinh = t.maGHN
    WHERE dh.nguoiDungID = ?
    $sqlFilter
    ORDER BY dh.ngayDat DESC
    LIMIT $limit OFFSET $offset
", $params);
$trangThaiDonHang = [
    'tat_ca'           => $db_utils->getValue("SELECT COUNT(*) FROM donhang WHERE nguoiDungID = ?", [$userId]),
    'cho_xac_nhan'     => $db_utils->getValue("SELECT COUNT(*) FROM donhang WHERE nguoiDungID = ? AND trangThai = 'cho_xac_nhan'", [$userId]),
    'da_xac_nhan'      => $db_utils->getValue("SELECT COUNT(*) FROM donhang WHERE nguoiDungID = ? AND trangThai = 'da_xac_nhan'", [$userId]),
    'dang_giao'        => $db_utils->getValue("SELECT COUNT(*) FROM donhang WHERE nguoiDungID = ? AND trangThai = 'dang_giao'", [$userId]),
    'giao_thanh_cong'  => $db_utils->getValue("SELECT COUNT(*) FROM donhang WHERE nguoiDungID = ? AND trangThai = 'giao_thanh_cong'", [$userId]),
    'tra_hang'         => $db_utils->getValue("SELECT COUNT(*) FROM donhang WHERE nguoiDungID = ? AND trangThai = 'tra_hang'", [$userId]),
    'hoan_thanh'       => $db_utils->getValue("SELECT COUNT(*) FROM donhang WHERE nguoiDungID = ? AND trangThai = 'hoan_thanh'", [$userId]),
    'da_huy'           => $db_utils->getValue("SELECT COUNT(*) FROM donhang WHERE nguoiDungID = ? AND trangThai = 'da_huy'", [$userId]),
];
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <title>Quản lý sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <style>
        .product-img {
            width: 200px;
            height: 200px;
            height: auto;
            object-fit: cover;
            margin-top: 10px;
        }

        .product-info {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            /* Tên - Size - SL - Giá chia đều */
            align-items: center;
            text-align: left;
            width: 100%;
        }

        .col-name {
            font-weight: bold;
        }

        .col-size,
        .col-qty,
        .col-price {
            white-space: nowrap;
            /* không xuống dòng */
        }
    </style>

    <!-- CSS 
    ========================= -->


    <!-- Plugins CSS -->
    <link rel="stylesheet" href="assets/css/plugins.css">

    <!-- Main Style CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
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
                                            echo $_SESSION['user']['vaiTro'] == 'admin' ? 'Admin' : $_SESSION['user']['ten'];
                                        } else {
                                            echo 'Tài khoản của tôi';
                                        }
                                        ?>
                                        <i class="ion-chevron-down"></i></a>
                                    <ul class="dropdown_links">
                                        <?php if (isset($_SESSION['user'])): ?>
                                            <?php if ($_SESSION['user']['vaiTro'] == 'admin'): ?>
                                                <li><a href="quanly.php">Quản lý cửa hàng</a></li>
                                                <li><a href="logout.php">Đăng xuất</a></li>
                                            <?php else: ?>
                                                <li><a href="taikhoan.php">Thông tin tài khoản</a></li>
                                                <li><a href="logout.php">Đăng xuất</a></li>
                                            <?php endif; ?>
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
                                    <a href="#"><i class="fa fa-shopping-basket"></i></a>
                                    <!--mini cart-->
                                    <div class="mini_cart">
                                        <?php $tongTien = 0;
                                        foreach ($gioHang as $gh):
                                            $tongTien += $gh['thanhTien'];
                                            $anh = $db_utils->getOne("SELECT anh FROM anhsanpham WHERE sanPhamID = ? AND anhChinh = 1", [$gh['sanPhamID']]); ?>
                                            <div class="cart_item top">

                                                <div class="cart_img">
                                                    <a href="#"><img src="<?= $anh['anh'] ?>" alt=""></a>
                                                </div>
                                                <div class="cart_info">
                                                    <a href="#"><?= $gh['tensp'] ?></a>

                                                    <span><?= $gh['soLuong'] ?> </span>
                                                    <span><?= number_format($gh['gia']) ?>đ</span>

                                                </div>
                                                <div class="cart_remove">
                                                    <a href="cart.php?delete_id=<?= $gh['id'] ?>"><i
                                                            class="ion-android-close"></i></a>
                                                </div>
                                            </div><?php endforeach; ?>
                                        <div class="cart__table">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-left">Tổng phụ</td>
                                                        <td class="text-right"><?= number_format($tongTien) ?>đ</td>
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
                                    <li class="active"><a href="index.php">Trang chủ <i
                                                class="fa fa-angle-down"></i></a>
                                    </li>
                                    <li class="mega_items"><a href="products.php">Sản phẩm <i
                                                class="fa fa-angle-down"></i></a>
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
                                        <li><a href="products.php">Sản phẩm </a></li>
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
    <div class="d-flex">
        <!-- Sidebar -->
        <div
            style="background-color: white; color: black; padding: 1rem; height: 100vh; width: 250px; border: 1px solid #ccc; border-radius: 8px;">
            <h2 class="text-center">Menu tài khoản</h2>
            <ul class="nav flex-column mt-4">
                <li class="nav-item"><a href="taikhoan.php" class="nav-link text-black"><i class="fas fa-user"></i>
                        Thông tin tài khoản</a></li>
                <li class="nav-item"><a href="dsyeuthich.php" class="nav-link text-black"><i class="fa fa-heart-o"></i>
                        Danh sách yêu thích</a></li>
                <li class="nav-item"><a href="diachi.php" class="nav-link text-black"><i class="fa fa-map-marker"></i>
                        Địa chỉ</a></li>
                <li class="nav-item"><a href="donhang.php" class="nav-link text-black"><i
                            class="fas fa-clipboard-list"></i> Đơn hàng</a></li>
                <li class="nav-item"><a href="magiamgiauser.php" class="nav-link text-black"><i class="fa fa-gift"></i>
                        Mã giảm giá</a></li>
                <li class="nav-item"><a href="index.php" class="nav-link text-black"><i class="fas fa-home"></i>
                        Trang chủ</a></li>
            </ul>
        </div>

        <!-- Main content -->
        <div class="p-4 flex-grow-1">
            <h1 class="nav-link text-black">Đơn hàng của tôi</h1>
            <form action="" method="get">
                <select name="limit" onchange="this.form.submit()" id="">
                    <option <?= isset($_GET["limit"]) && $_GET["limit"] == 2 ? "selected" : "" ?> value="2">2</option>
                    <option <?= isset($_GET["limit"]) && $_GET["limit"] == 20 ? "selected" : "" ?> value="20">20</option>
                    <option <?= isset($_GET["limit"]) && $_GET["limit"] == 50 ? "selected" : "" ?> value="50">50</option>
                    <option <?= isset($_GET["limit"]) && $_GET["limit"] == 100 ? "selected" : "" ?> value="100">100</option>
                </select>
            </form>
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link <?= empty($_GET['status']) ? 'active' : '' ?>" href="?">Tất cả (<?= $trangThaiDonHang['tat_ca'] ?>)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['status'] ?? '') == 'cho_xac_nhan' ? 'active' : '' ?>" href="?status=cho_xac_nhan">Chờ xác nhận (<?= $trangThaiDonHang['cho_xac_nhan'] ?>)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['status'] ?? '') == 'da_xac_nhan' ? 'active' : '' ?>" href="?status=da_xac_nhan">Đã xác nhận (<?= $trangThaiDonHang['da_xac_nhan'] ?>)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['status'] ?? '') == 'dang_giao' ? 'active' : '' ?>" href="?status=dang_giao">Đang giao hàng (<?= $trangThaiDonHang['dang_giao'] ?>)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['status'] ?? '') == 'giao_thanh_cong' ? 'active' : '' ?>" href="?status=giao_thanh_cong">Giao hàng thành công (<?= $trangThaiDonHang['giao_thanh_cong'] ?>)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['status'] ?? '') == 'tra_hang' ? 'active' : '' ?>" href="?status=tra_hang">Trả hàng (<?= $trangThaiDonHang['tra_hang'] ?>)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['status'] ?? '') == 'hoan_thanh' ? 'active' : '' ?>" href="?status=hoan_thanh">Hoàn thành (<?= $trangThaiDonHang['hoan_thanh'] ?>)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['status'] ?? '') == 'da_huy' ? 'active' : '' ?>" href="?status=da_huy">Đã hủy (<?= $trangThaiDonHang['da_huy'] ?>)</a>
                </li>
            </ul>


            <?php foreach ($dsDonHang as $donHang): ?>
                <?php
                $chiTiet = $db_utils->getAll("
        SELECT ct.*, sp.ten AS tenSP, asp.anh, kc.size AS tenSize
        FROM chitietdonhang ct
        JOIN kichco kc ON ct.bienTheID = kc.id
        JOIN sanpham sp ON kc.idSanPham = sp.id
        LEFT JOIN anhsanpham asp 
            ON sp.id = asp.sanPhamID AND asp.anhChinh = 1
        WHERE ct.donHangID = ?
    ", [$donHang['id']]);
                ?>

                <div class="border rounded mb-4 p-3 bg-white">
                    <!-- Tiêu đề đơn hàng -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Đơn hàng #<?= $donHang['id'] ?></strong> - <?= date('d/m/Y H:i', strtotime($donHang['ngayDat'])) ?>
                        </div>
                        <div>
                            <?php if ($donHang['trangThai'] == 'giao_thanh_cong'): ?>
                                <span class="badge bg-info">Giao hàng thành công</span>
                            <?php elseif ($donHang['trangThai'] == 'cho_xac_nhan'): ?>
                                <span class="badge bg-warning">Chờ xác nhận</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Phương thức & địa chỉ -->
                    <div class="mt-2 text-muted small">
                        <strong>Phương thức:</strong> <?= strtoupper($donHang['phuongTT'] ?? 'COD') ?> |
                        <strong>Địa chỉ giao hàng:</strong> <?= $donHang['diaChiDayDu'] ?? 'Chưa có địa chỉ' ?>
                    </div>

                    <!-- Bảng sản phẩm -->
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Size</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Tổng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($chiTiet as $ct): ?>
                                    <tr>
                                        <td><img src="<?= $ct['anh'] ?>" style="width:60px;height:60px;object-fit:cover" class="rounded"></td>
                                        <td class="text-start"><?= $ct['tenSP'] ?></td>
                                        <td><?= $ct['tenSize'] ?></td>
                                        <td><?= number_format($ct['gia']) ?> đ</td>
                                        <td><?= $ct['soLuong'] ?></td>
                                        <td><?= number_format($ct['gia'] * $ct['soLuong']) ?> đ</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tính tiền -->
                    <div class="text-end">
                        <div>Giảm giá: <strong><?= number_format($donHang['giamGia'] ?? 0) ?> đ</strong></div>
                        <div>Phí vận chuyển: <strong><?= number_format($donHang['phiShip'] ?? 0) ?> đ</strong></div>
                        <div class="fs-5 text-danger">Tổng tiền: <strong><?= number_format($donHang['tongTien']) ?> đ</strong></div>
                    </div>

                    <!-- Nút hành động -->
                    <?php if (in_array($donHang['trangThai'], ['cho_xac_nhan', 'da_xac_nhan'])): ?>
                        <a href="?action=huy&id=<?= $donHang['id'] ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Bạn có chắc muốn hủy đơn này không?')">Hủy đơn</a>

                    <?php elseif ($donHang['trangThai'] == 'giao_thanh_cong'): ?>
                        <a href="?action=hoanthanh&id=<?= $donHang['id'] ?>" class="btn btn-success btn-sm">Hoàn thành</a>
                        <a href="?action=trahang&id=<?= $donHang['id'] ?>" class="btn btn-outline-secondary btn-sm">Trả hàng</a>

                    <?php elseif ($donHang['trangThai'] == 'hoan_thanh'): ?>
                        <a href="danhgia.php?donhang=<?= $donHang['id'] ?>" class="btn btn-primary btn-sm">Đánh giá</a>
                        <a href="?action=mualai&id=<?= $donHang['id'] ?>" class="btn btn-warning btn-sm">Mua lại</a>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>


            <!-- Phân trang -->
            <nav>
                <ul class="pagination">
                    <?php for ($index = 1; $index <= $sotrang; $index++) { ?>
                        <li class="page-item <?= $index == $page ? "active" : "" ?>">
                            <a class="page-link"
                                href="?<?= http_build_query(array_merge($_GET, ["page" => $index])) ?>"><?= $index ?></a>
                        </li>
                    <?php } ?>
                </ul>

            </nav>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/plugins.js"></script>

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>
</body>

</html>