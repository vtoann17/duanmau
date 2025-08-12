<?php
session_start();
require_once "db_utils.php";
$db_util = new DB_UTILS();

// Lấy dữ liệu sản phẩm và danh mục

$limit =  $_GET["limit"] ?? 5;
$page = $_GET["page"] ??  1;
$offset = ($page - 1) * $limit;

$tongdong = $db_util->getValue("SELECT COUNT(*) FROM sanpham");
$sotrang = ceil($tongdong / $limit);
$userId = $_SESSION['user']['id'];
$statusFilter = $_GET['status'] ?? '';
$params = [$userId];

// Nếu có lọc trạng thái
$sqlFilter = '';
if (!empty($statusFilter)) {
    $sqlFilter = " AND dh.trangThai = ? ";
    $params[] = $statusFilter;
}

$trangThaiDonHang = [
    'tat_ca'           => $db_util->getValue("SELECT COUNT(*) FROM donhang"),
    'cho_xac_nhan'     => $db_util->getValue("SELECT COUNT(*) FROM donhang WHERE trangThai = 'cho_xac_nhan'"),
    'da_xac_nhan'      => $db_util->getValue("SELECT COUNT(*) FROM donhang WHERE trangThai = 'da_xac_nhan'"),
    'dang_giao'        => $db_util->getValue("SELECT COUNT(*) FROM donhang WHERE trangThai = 'dang_giao'"),
    'giao_thanh_cong'  => $db_util->getValue("SELECT COUNT(*) FROM donhang WHERE trangThai = 'giao_thanh_cong'"),
    'tra_hang'         => $db_util->getValue("SELECT COUNT(*) FROM donhang WHERE trangThai = 'tra_hang'"),
    'hoan_thanh'       => $db_util->getValue("SELECT COUNT(*) FROM donhang WHERE trangThai = 'hoan_thanh'"),
    'da_huy'           => $db_util->getValue("SELECT COUNT(*) FROM donhang WHERE trangThai = 'da_huy'"),
];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["trangThai"])) {
    $db_util->execute("UPDATE donhang SET trangThai = ? WHERE id = ?", [
        $_POST["trangThai"],
        $_POST["id"]
    ]);
    $_SESSION['message'] = "Cập nhật trạng thái đơn hàng thành công.";
    header("Location: quanly_donhang.php");
    exit;
}

// Lấy tất cả đơn hàng
$donhangs = $db_util->getAll("
    SELECT dh.*, nd.ten as tenNguoiDung, dc.chiTiet as diaChiChiTiet
    FROM donhang dh
    LEFT JOIN nguoidung nd ON dh.nguoiDungID = nd.id
    LEFT JOIN diachi dc ON dh.idDc = dc.id
    ORDER BY dh.ngayDat DESC
    LIMIT $limit OFFSET $offset
");
if (!empty($_GET['status'])) {
    $status = $_GET['status'];
    $donhangs = $db_util->getAll("
        SELECT dh.*, nd.ten as tenNguoiDung, dc.chiTiet as diaChiChiTiet
        FROM donhang dh
        LEFT JOIN nguoidung nd ON dh.nguoiDungID = nd.id
        LEFT JOIN diachi dc ON dh.idDc = dc.id
        WHERE dh.trangThai = ?
        ORDER BY dh.ngayDat DESC
        LIMIT $limit OFFSET $offset
    ", [$status]);
}

if (isset($_GET["sort"])) {
    $sort = $_GET["sort"];
    if ($sort == "asc") {
        $donhangs = $db_util->getAll("
            SELECT dh.*, nd.ten as tenNguoiDung, dc.chiTiet as diaChiChiTiet
            FROM donhang dh
            LEFT JOIN nguoidung nd ON dh.nguoiDungID = nd.id
            LEFT JOIN diachi dc ON dh.idDc = dc.id
            ORDER BY dh.phiShip ASC
            LIMIT $limit OFFSET $offset
        ");
    } elseif ($sort == "desc") {
        $donhangs = $db_util->getAll("
            SELECT dh.*, nd.ten as tenNguoiDung, dc.chiTiet as diaChiChiTiet
            FROM donhang dh
            LEFT JOIN nguoidung nd ON dh.nguoiDungID = nd.id
            LEFT JOIN diachi dc ON dh.idDc = dc.id
            ORDER BY dh.phiShip DESC
            LIMIT $limit OFFSET $offset
        ");
    }
}


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
                                                        <td class="text-left">Tổng phụ:</td>
                                                        <td class="text-right"><?= number_format($tongTien) ?>đ</td>
                                                    </tr>

                                                    <tr>
                                                        <td class="text-left">Tổng cộng :</td>
                                                       <td class="text-right"><?= number_format($tongTien) ?>đ</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="cart_button view_cart">
                                            <a href="cart.php">Giỏ hàng</a>
                                        </div>
                                        <div class="cart_button checkout">
                                            <a href="dathang.php">Thanh toán</a>
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
            <h2 class="text-center">Admin</h2>
            <ul class="nav flex-column mt-4">
                <li class="nav-item"><a href="quanly.php" class="nav-link text-black"><i class="fa fa-chart-line"></i>
                        Dashboard</a></li>
                <li class="nav-item"><a href="quanly_sanpham.php" class="nav-link text-black"><i
                            class="fa fa-shirt"></i> Sản phẩm</a></li>
                <li class="nav-item"><a href="quanly_danhmuc.php" class="nav-link text-black"><i class="fa fa-tags"></i>
                        Danh mục</a></li>
                <li class="nav-item"><a href="quanly_donhang.php" class="nav-link text-black"><i class="fa fa-box"></i>
                        Đơn hàng</a></li>
                <li class="nav-item"><a href="quanly_khachhang.php" class="nav-link text-black"><i
                            class="fa fa-users"></i> Khách hàng</a></li>
                <li class="nav-item"><a href="quanly_magiamgia.php" class="nav-link text-black"><i
                            class="fa fa-gift"></i> Mã giảm giá</a></li>
                <li class="nav-item"><a href="reviews.html" class="nav-link text-black"><i class="fa fa-comments"></i>
                        Đánh giá</a></li>
                <li class="nav-item"><a href="settings.html" class="nav-link text-black"><i class="fa fa-cog"></i> Cài
                        đặt</a></li>
                <li class="nav-item"><a href="login.html" class="nav-link text-black"><i class="fa fa-sign-out"></i>
                        Đăng xuất</a></li>
            </ul>
        </div>

        <!-- Main content -->
        <div class="p-4 flex-grow-1">
            <h1 class="nav-link text-black">Quản lý đơn hàng</h1>
            <?php if (isset($_SESSION['message'])): ?>
                <div
                    style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 5px;">
                    <?= $_SESSION['message'] ?>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            <!-- Tìm kiếm & Sắp xếp -->
            <div class="row mb-3 align-items-center">
                <div class="col-md-4">
                    <form action="" method="get" class="d-flex">

                        <input type="text" class="form-control me-2" name="search"
                            placeholder="Tìm theo tên sản phẩm hoặc danh mục..."
                            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
                <div class="col-md-3">
                    <select name="sort" onchange="this.form.submit()" class="form-select">
                        <option value="">Sắp xếp theo</option>
                        <option value="asc">Đơn hàng cũ nhất</option>
                        <option value="desc">Đơn hàng mới nhất</option>
                    </select>
                    </form>
                </div>
            </div>


            <!-- Bảng -->
            <form action="" method="get">
                <select name="limit" onchange="this.form.submit()" id="">
                    <option <?= isset($_GET["limit"]) && $_GET["limit"] == 5 ? "selected" : "" ?> value="5">5</option>
                    <option <?= isset($_GET["limit"]) && $_GET["limit"] == 20 ? "selected" : "" ?> value="20">20</option>
                    <option <?= isset($_GET["limit"]) && $_GET["limit"] == 50 ? "selected" : "" ?> value="50">50</option>
                    <option <?= isset($_GET["limit"]) && $_GET["limit"] == 100 ? "selected" : "" ?> value="100">100</option>
                </select>
            </form>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success"><?= $_SESSION['message'];
                                                    unset($_SESSION['message']); ?></div>
            <?php endif; ?>
            <!-- Tabs lọc trạng thái -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link <?= !isset($_GET['status']) ? 'active' : '' ?>"
           href="?<?= http_build_query(array_merge($_GET, ['status' => null, 'page' => 1])) ?>">
           Tất cả (<?= $trangThaiDonHang['tat_ca'] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($_GET['status'] ?? '') == 'cho_xac_nhan' ? 'active' : '' ?>"
           href="?<?= http_build_query(array_merge($_GET, ['status' => 'cho_xac_nhan', 'page' => 1])) ?>">
           Chờ xác nhận (<?= $trangThaiDonHang['cho_xac_nhan'] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($_GET['status'] ?? '') == 'da_xac_nhan' ? 'active' : '' ?>"
           href="?<?= http_build_query(array_merge($_GET, ['status' => 'da_xac_nhan', 'page' => 1])) ?>">
           Đã xác nhận (<?= $trangThaiDonHang['da_xac_nhan'] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($_GET['status'] ?? '') == 'dang_giao' ? 'active' : '' ?>"
           href="?<?= http_build_query(array_merge($_GET, ['status' => 'dang_giao', 'page' => 1])) ?>">
           Đang giao hàng (<?= $trangThaiDonHang['dang_giao'] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($_GET['status'] ?? '') == 'giao_thanh_cong' ? 'active' : '' ?>"
           href="?<?= http_build_query(array_merge($_GET, ['status' => 'giao_thanh_cong', 'page' => 1])) ?>">
           Giao thành công (<?= $trangThaiDonHang['giao_thanh_cong'] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($_GET['status'] ?? '') == 'tra_hang' ? 'active' : '' ?>"
           href="?<?= http_build_query(array_merge($_GET, ['status' => 'tra_hang', 'page' => 1])) ?>">
           Trả hàng (<?= $trangThaiDonHang['tra_hang'] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($_GET['status'] ?? '') == 'hoan_thanh' ? 'active' : '' ?>"
           href="?<?= http_build_query(array_merge($_GET, ['status' => 'hoan_thanh', 'page' => 1])) ?>">
           Hoàn thành (<?= $trangThaiDonHang['hoan_thanh'] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($_GET['status'] ?? '') == 'da_huy' ? 'active' : '' ?>"
           href="?<?= http_build_query(array_merge($_GET, ['status' => 'da_huy', 'page' => 1])) ?>">
           Đã hủy (<?= $trangThaiDonHang['da_huy'] ?>)
        </a>
    </li>
</ul>


            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người đặt</th>
                        <th>Địa chỉ</th>
                        <th>Ngày đặt</th>
                        <th>Phương thức</th>
                        <th>Phí ship</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Cập nhật</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donhangs as $dh): ?>
                        <tr>
                            <td><?= $dh['id'] ?></td>
                            <td><?= $dh['ten'] ?> <br><small><?= $dh['sdt'] ?></small></td>
                            <td><?= $dh['diaChiChiTiet'] ?></td>
                            <td><?= $dh['ngayDat'] ?></td>
                            <td><?= $dh['phuongTT'] ?></td>
                            <td><?= number_format($dh['phiShip']) ?>đ</td>
                            <td><?= number_format($dh['tongTien']) ?>đ</td>
                            <td><?= ucfirst(str_replace('_', ' ', $dh['trangThai'])) ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="id" value="<?= $dh['id'] ?>">
                                    <select name="trangThai" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option <?= $dh['trangThai'] == 'cho_xac_nhan' ? 'selected' : '' ?> value="cho_xac_nhan">Chờ xác nhận</option>
                                        <option <?= $dh['trangThai'] == 'da_xac_nhan' ? 'selected' : '' ?> value="da_xac_nhan">Đã xác nhận</option>
                                        <option <?= $dh['trangThai'] == 'dang_giao' ? 'selected' : '' ?> value="dang_giao">Đang giao</option>
                                        <option <?= $dh['trangThai'] == 'giao_thanh_cong' ? 'selected' : '' ?> value="giao_thanh_cong">Giao thành công</option>
                                        <option <?= $dh['trangThai'] == 'tra_hang' ? 'selected' : '' ?> value="tra_hang">Trả hàng</option>
                                        <option <?= $dh['trangThai'] == 'hoan_thanh' ? 'selected' : '' ?> value="hoan_thanh">Hoàn thành</option>
                                        <option <?= $dh['trangThai'] == 'da_huy' ? 'selected' : '' ?> value="da_huy">Đã huỷ</option>
                                    </select>

                                </form>
                            </td>
                            <td><a class="btn btn-sm btn-info" href="chitietdonhang.php?id=<?= $dh['id'] ?>">Xem</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>


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
    <script src="assets/js/plugins.js"></script>

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>
</body>

</html>