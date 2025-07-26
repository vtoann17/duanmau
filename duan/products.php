<?php
session_start();
  require_once "./db_utils.php";
  $db_utils = new DB_UTILS();
  $dsSanPham = $db_utils->getAll("
    SELECT sp.id, sp.ten, sp.gia, sp.moTa, asp.anh
    FROM sanpham sp
    LEFT JOIN anhsanpham asp ON sp.id = asp.sanPhamID
    GROUP BY sp.id
");
//   $dsSanPham = $db_utils->getAll('select * from sanpham  sp left join danhmuc dm on sp.maloai = dm.maloai');
//  echo "<pre>";
//   var_dump($dsSanPham);
//   echo "<pre>";
//   die;


?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Shop category</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    
    <!-- CSS 
    ========================= -->


    <!-- Plugins CSS -->
    <link rel="stylesheet" href="assets/css/plugins.css">
    
    <!-- Main Style CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

    <!-- Main Wrapper Start -->
    <!--Offcanvas menu area start-->
    <div class="off_canvars_overlay">
                
    </div>
     <div class="offcanvas_menu">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="canvas_open">
                        <a href="javascript:void(0)"><i class="ion-navicon"></i></a>
                    </div>
                    <div class="offcanvas_menu_wrapper">
                        <div class="canvas_close">
                            <a href="javascript:void(0)"><i class="ion-android-close"></i></a>
                        </div>
                        <div class="top_right">
                            <ul>
                                <li class="top_links"><a href="#">Tài khoản <i class="ion-chevron-down"></i></a>
                                    <ul class="dropdown_links">
                                        <li><a href="wishlist.php">Danh sách yêu thích</a></li>
                                        <li><a href="my-account.php">Tài khoản của tôi</a></li>
                                        <li><a href="#">Đăng nhập</a></li>
                                        <li><a href="dangxuat.php">Đăng xuất</a></li>
                                    </ul>
                                </li>
                                <li class="language"><a href="#"><img src="assets/img/logo/language.png" alt=""> Ngôn
                                        ngữ <i class="ion-chevron-down"></i></a>
                                    <ul class="dropdown_language">
                                        <li><a href="#"><img src="assets/img/logo/cigar.jpg" alt=""> Tiếng Pháp</a></li>
                                        <li><a href="#"><img src="assets/img/logo/language2.png" alt="">Tiếng Đức</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="currency"><a href="#">Tiền tệ <i class="ion-chevron-down"></i></a>
                                    <ul class="dropdown_currency">
                                        <li><a href="#">EUR</a></li>
                                        <li><a href="#">BRL</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="search_bar">
                            <form action="#">
                                <select class="select_option" name="select" id="categori">
                                    <option selected value="1">Tất cả danh mục</option>
                                    <option value="2">Phụ kiện</option>
                                    <option value="3">Bridge</option>
                                    <option value="4">Hub</option>
                                    <option value="5">Repeater</option>
                                    <option value="6">Switch</option>
                                    <option value="7">Trò chơi điện tử</option>
                                    <option value="8">PlayStation 3</option>
                                    <option value="9">PlayStation 4</option>
                                    <option value="10">Xbox 360</option>
                                    <option value="11">Xbox One</option>
                                </select>
                                <input placeholder="Tìm kiếm sản phẩm..." type="text">
                                <button type="submit"><i class="ion-ios-search-strong"></i></button>
                            </form>
                        </div>
                        <div class="cart_area">
                            <div class="middel_links">
                                <ul>
                                    <li><a href="login.html">Đăng nhập</a></li>
                                    <li>/</li>
                                    <li><a href="login.html">Đăng ký</a></li>
                                </ul>
                            </div>
                            <div class="cart_link">
                                <a href="#"><i class="fa fa-shopping-basket"></i>2 sản phẩm</a>
                                <!-- mini cart -->
                                <div class="mini_cart">
                                    <div class="cart_item top">
                                        <div class="cart_img">
                                            <a href="#"><img src="assets/img/s-product/product.jpg" alt=""></a>
                                        </div>
                                        <div class="cart_info">
                                            <a href="#">Apple iPhone SE 16GB</a>
                                            <span>1x $60.00</span>
                                        </div>
                                        <div class="cart_remove">
                                            <a href="#"><i class="ion-android-close"></i></a>
                                        </div>
                                    </div>
                                    <div class="cart_item bottom">
                                        <div class="cart_img">
                                            <a href="#"><img src="assets/img/s-product/product2.jpg" alt=""></a>
                                        </div>
                                        <div class="cart_info">
                                            <a href="#">Loa Bluetooth Marshall</a>
                                            <span>1x $160.00</span>
                                        </div>
                                        <div class="cart_remove">
                                            <a href="#"><i class="ion-android-close"></i></a>
                                        </div>
                                    </div>
                                    <div class="cart__table">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td class="text-left">Tạm tính :</td>
                                                    <td class="text-right">$150.00</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left">Tổng cộng :</td>
                                                    <td class="text-right">$184.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="cart_button view_cart">
                                        <a href="cart.html">Xem giỏ hàng</a>
                                    </div>
                                    <div class="cart_button checkout">
                                        <a href="checkout.html">Thanh toán</a>
                                    </div>
                                </div>
                                <!-- mini cart end -->
                            </div>
                        </div>
                        <div class="contact_phone">
                            <p>Gọi hỗ trợ miễn phí: <a href="tel:+(012)800456789">(+012) 800 456 789</a></p>
                        </div>
                        <div id="menu" class="text-left">
                            <ul class="offcanvas_main_menu">
                                <li class="menu-item-has-children active">
                                    <a href="#">Trang chủ</a>
                                    <ul class="sub-menu">
                                        <li><a href="index.php">Trang chủ 1</a></li>
                                        <li><a href="index-2.php">Trang chủ 2</a></li>
                                        <li><a href="index-3.php">Trang chủ 3</a></li>
                                        <li><a href="index-4.php">Trang chủ 4</a></li>
                                        <li><a href="index-5.php">Trang chủ 5</a></li>
                                        <li><a href="index-6.php">Trang chủ 6</a></li>
                                        <li><a href="index-7.php">Trang chủ 7</a></li>
                                        <li><a href="index-8.php">Trang chủ 8</a></li>
                                        <li><a href="index-9.php">Trang chủ 9</a></li>
                                    </ul>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="#">Cửa hàng</a>
                                    <ul class="sub-menu">
                                        <li class="menu-item-has-children">
                                            <a href="#">Bố cục cửa hàng</a>
                                            <ul class="sub-menu">
                                                <li><a href="products.php">Cửa hàng</a></li>
                                                <li><a href="shop-fullwidth.php">Toàn màn hình</a></li>
                                                <li><a href="shop-fullwidth-list.php">Toàn màn hình dạng danh sách</a>
                                                </li>
                                                <li><a href="shop-right-sidebar.php">Thanh bên phải</a></li>
                                                <li><a href="shop-right-sidebar-list.php">Danh sách bên phải</a></li>
                                                <li><a href="shop-list.php">Dạng danh sách</a></li>
                                            </ul>
                                        </li>
                                        <li class="menu-item-has-children">
                                            <a href="#">Trang khác</a>
                                            <ul class="sub-menu">
                                                <li><a href="portfolio.php">Danh mục</a></li>
                                                <li><a href="portfolio-details.php">Chi tiết danh mục</a></li>
                                                <li><a href="cart.php">Giỏ hàng</a></li>
                                                <li><a href="checkout.php">Thanh toán</a></li>
                                                <li><a href="my-account.php">Tài khoản của tôi</a></li>
                                            </ul>
                                        </li>
                                        <li class="menu-item-has-children">
                                            <a href="#">Loại sản phẩm</a>
                                            <ul class="sub-menu">
                                                <li><a href="product-details.php">Chi tiết sản phẩm</a></li>
                                                <li><a href="product-sidebar.php">Sản phẩm có sidebar</a></li>
                                                <li><a href="product-grouped.php">Sản phẩm nhóm</a></li>
                                                <li><a href="variable-product.php">Sản phẩm biến thể</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="#">Blog</a>
                                    <ul class="sub-menu">
                                        <li><a href="blog.php">Blog</a></li>
                                        <li><a href="blog-details.php">Chi tiết blog</a></li>
                                        <li><a href="blog-sidebar.php">Blog có sidebar</a></li>
                                        <li><a href="blog-fullwidth.php">Blog toàn màn hình</a></li>
                                    </ul>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="#">Trang</a>
                                    <ul class="sub-menu">
                                        <li><a href="about.php">Về chúng tôi</a></li>
                                        <li><a href="contact.php">Liên hệ</a></li>
                                        <li><a href="login.php">Đăng nhập</a></li>
                                    </ul>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="my-account.php">Tài khoản</a>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="about.php">Về chúng tôi</a>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="contact.php">Liên hệ</a>
                                </li>
                            </ul>
                        </div>
                        <div class="offcanvas_footer">
                            <span><a href="#"><i class="fa fa-envelope-o"></i> info@yourdomain.com</a></span>
                            <ul>
                                <li class="facebook"><a href="#"><i class="fa fa-facebook"></i></a></li>
                                <li class="twitter"><a href="#"><i class="fa fa-twitter"></i></a></li>
                                <li class="pinterest"><a href="#"><i class="fa fa-pinterest-p"></i></a></li>
                                <li class="google-plus"><a href="#"><i class="fa fa-google-plus"></i></a></li>
                                <li class="linkedin"><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Offcanvas menu area end-->
     
    <!--header area start-->
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
            <li><a href="wishlist.html">Danh mục yêu thích</a></li>
            <?php if ($_SESSION['user']['vaiTro'] == 'admin'): ?>
                <li><a href="quanly.php">Quản lý cửa hàng</a></li>
    
            <?php endif; ?>
            <li><a href="logout.php">Đăng xuất</a></li>
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
                                    <a href="#"><i class="fa fa-shopping-basket"></i>2 sản phẩm</a>
                                    <!--mini cart-->
                                     <div class="mini_cart">
                                        <div class="cart_item top">
                                       <div class="cart_img">
                                           <a href="#"><img src="assets/img/s-product/product.jpg" alt=""></a>
                                       </div>
                                        <div class="cart_info">
                                            <a href="#">Apple iPhone SE 16GB</a>

                                            <span>1x $60.00</span>
    
                                        </div>
                                        <div class="cart_remove">
                                            <a href="#"><i class="ion-android-close"></i></a>
                                        </div>
                                    </div>
                                    <div class="cart_item bottom">
                                       <div class="cart_img">
                                           <a href="#"><img src="assets/img/s-product/product2.jpg" alt=""></a>
                                       </div>
                                        <div class="cart_info">
                                            <a href="#">Marshall Portable  Bluetooth</a>
                                                <span> 1x $160.00</span>
                                        </div>
                                        <div class="cart_remove">
                                            <a href="#"><i class="ion-android-close"></i></a>
                                        </div>
                                    </div>
                                    <div class="cart__table">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td class="text-left">Sub-Total :</td>
                                                    <td class="text-right">$150.00</td>
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
                                    <li class="active"><a href="index.php">Trang chủ <i class="fa fa-angle-down"></i></a>
                                    </li>
                                    <li class="mega_items"><a href="products.php">Sản phẩm <i class="fa fa-angle-down"></i></a>
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

    <!--breadcrumbs area start-->
    <div class="breadcrumbs_area">
        <div class="container">   
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb_content">
                        <ul>
                            <li><a href="index.php">home</a></li>
                            <li>/</li>
                            <li>shop</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>         
    </div>
    <!--breadcrumbs area end-->
    
    <!--shop  area start-->
   <div class="shop_area shop_reverse">
    <div class="container">
        <div class="shop_inner_area">
            <div class="row">
                <div class="col-lg-3 col-md-12">
                    <!-- Bắt đầu thanh bên -->
                    <div class="sidebar_widget">
                        <!-- Bộ lọc giá -->
                        <div class="widget_list widget_filter">
                            <h2>Lọc theo giá</h2>
                            <form action="#"> 
                                <div id="slider-range"></div>   
                                <button type="submit">Lọc</button>
                                <input type="text" name="text" id="amount" />   
                            </form> 
                        </div>
                        <!-- Danh mục sản phẩm -->
                        <div class="widget_list widget_categories">
                            <h2>Danh mục sản phẩm</h2>
                            <ul>
                                <li><a href="#">Danh mục 1 <span>6</span></a></li>
                                <li><a href="#">Danh mục 2 <span>10</span></a></li>
                                <li><a href="#">Danh mục 3 <span>4</span></a></li>
                                <li><a href="#">Danh mục 4 <span>4</span></a></li>
                                <li><a href="#">Danh mục 5 <span>3</span></a></li>
                            </ul>
                        </div>
                        <!-- Nhà sản xuất -->
                        <div class="widget_list widget_categories">
                            <h2>Thương hiệu</h2>
                            <ul>
                                <li><a href="#">Calvin Klein <span>6</span></a></li>
                                <li><a href="#">Chanel <span>10</span></a></li>
                                <li><a href="#">Christian Dior <span>4</span></a></li>
                                <li><a href="#">Ferragamo <span>4</span></a></li>
                                <li><a href="#">Hermes <span>10</span></a></li>
                                <li><a href="#">Louis Vuitton <span>8</span></a></li>
                                <li><a href="#">Tommy Hilfiger <span>7</span></a></li>
                                <li><a href="#">Versace <span>6</span></a></li>
                            </ul>
                        </div>
                        <!-- Màu sắc -->
                        <div class="widget_list widget_categories">
                            <h2>Chọn theo màu</h2>
                            <ul>
                                <li><a href="#">Đen <span>6</span></a></li>
                                <li><a href="#">Xanh dương <span>10</span></a></li>
                                <li><a href="#">Nâu <span>4</span></a></li>
                                <li><a href="#">Xanh lá <span>4</span></a></li>
                                <li><a href="#">Hồng <span>7</span></a></li>
                                <li><a href="#">Trắng <span>8</span></a></li>
                                <li><a href="#">Vàng <span>5</span></a></li>
                            </ul>
                        </div>
                        <!-- Thẻ phổ biến -->
                        <div class="widget_list tag-cloud">
                            <h2>Thẻ phổ biến</h2>
                            <div class="tag_widget">
                                <ul>
                                    <li><a href="#">Kem dưỡng</a></li>
                                    <li><a href="#">Chì kẻ mày</a></li>
                                    <li><a href="#">Kẻ mắt</a></li>
                                    <li><a href="#">Phấn mắt</a></li>
                                    <li><a href="#">Sữa dưỡng</a></li>
                                    <li><a href="#">Mascara</a></li>
                                    <li><a href="#">Dầu dưỡng</a></li>
                                    <li><a href="#">Phấn phủ</a></li>
                                    <li><a href="#">Dầu gội</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- Kết thúc thanh bên -->
                </div>

                <div class="col-lg-9 col-md-12">
                    <!-- Bắt đầu phần sản phẩm -->
                    <div class="shop_title">
                        <h1>Cửa hàng</h1>
                    </div>

                    <div class="shop_toolbar_wrapper">
                        <div class="shop_toolbar_btn">
                            <button data-role="grid_3" type="button" class="active btn-grid-3" data-toggle="tooltip" title="3 sản phẩm / dòng"></button>
                            <button data-role="grid_4" type="button" class="btn-grid-4" data-toggle="tooltip" title="4 sản phẩm / dòng"></button>
                            <button data-role="grid_5" type="button" class="btn-grid-5" data-toggle="tooltip" title="5 sản phẩm / dòng"></button>
                            <button data-role="grid_list" type="button" class="btn-list" data-toggle="tooltip" title="Hiển thị dạng danh sách"></button>
                        </div>

                        <div class="niceselect_option">
                            <form class="select_option" action="#">
                                <select name="orderby" id="short">
                                    <option selected value="1">Sắp xếp theo đánh giá</option>
                                    <option value="2">Sắp xếp theo phổ biến</option>
                                    <option value="3">Sắp xếp theo sản phẩm mới</option>
                                    <option value="4">Giá: thấp đến cao</option>
                                    <option value="5">Giá: cao đến thấp</option>
                                    <option value="6">Tên sản phẩm: Z - A</option>
                                </select>
                            </form>
                        </div>

                        <div class="page_amount">
                            <p>Hiển thị 1–9 của 21 sản phẩm</p>
                        </div>
                    </div>

                    <div class="row shop_wrapper">
                        <?php foreach($dsSanPham as $sanpham): ?>
                        <div class="col-lg-4 col-md-4 col-12 ">
                            <div class="single_product">
                                <div class="product_thumb">
                                    <a class="primary_img" href="product-details.php"><img src="<?php echo $sanpham['anh']; ?>" alt=""></a>
                                    <a class="secondary_img" href="product-details.php"><img src="<?php echo $sanpham['anh']; ?>" alt=""></a>
                                    <div class="quick_button">
                                        <a href="product-details.php?id=<?= $sanpham['id'] ?>" title="Xem nhanh">Xem sản phẩm</a>
                                    </div>
                                    <!-- <div class="double_base">
                                        <div class="product_sale">
                                            <span>-7%</span>
                                        </div>
                                        <div class="label_product">
                                            <span>Mới</span>
                                        </div>
                                    </div> -->
                                </div>

                                <div class="product_content grid_content">
                                    <h3><a href="product-details.php"><?php echo $sanpham['ten']; ?></a></h3>
                                    <span class="current_price"><?php echo number_format( $sanpham['gia']); ?></span>
                                </div>

                                <div class="product_content list_content">
                                    <h3><a href="product-details.html"><?php echo $sanpham['ten']; ?></a></h3>
                                    <div class="product_ratting">
                                        <ul>
                                            <li><a href="#"><i class="fa fa-star"></i></a></li>
                                            <li><a href="#"><i class="fa fa-star"></i></a></li>
                                            <li><a href="#"><i class="fa fa-star"></i></a></li>
                                            <li><a href="#"><i class="fa fa-star"></i></a></li>
                                            <li><a href="#"><i class="fa fa-star"></i></a></li>
                                        </ul>
                                    </div>
                                    <div class="product_price">
                                        <span class="current_price"><?php echo $sanpham['gia']; ?></span>
                                    </div>
                                    <div class="product_desc">
                                        <p><?php echo $sanpham['moTa']?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach;?>
                    </div>

                    <!-- Phân trang -->
                    <div class="shop_toolbar t_bottom">
                        <div class="pagination">
                            <ul>
                                <li class="current">1</li>
                                <li><a href="#">2</a></li>
                                <li><a href="#">3</a></li>
                                <li class="next"><a href="#">Tiếp</a></li>
                                <li><a href="#">>></a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- Kết thúc phần sản phẩm -->
                </div>
            </div>
        </div>
    </div>
</div>

    <!--shop  area end-->
    
    <!--footer area start-->
       <footer class="footer_widgets">
        <div class="footer_top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-2 col-md-6 col-sm-6 col-6">
                        <div class="widgets_container">
                            <h3>Thông tin</h3>
                            <div class="footer_menu">
                                <ul>
                                    <li><a href="about.php">Giới thiệu</a></li>
                                    <li><a href="#">Thông tin giao hàng</a></li>
                                    <li><a href="privacy-policy.html">Chính sách bảo mật</a></li>
                                    <li><a href="#">Điều khoản</a></li>
                                    <li><a href="contact.html">Liên hệ với chúng tôi</a></li>
                                    <li><a href="#">Quay lại</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-6 col-6">
                        <div class="widgets_container">
                            <h3>Thêm vào</h3>
                            <div class="footer_menu">
                                <ul>
                                    <li><a href="#">Thương hiệu</a></li>
                                    <li><a href="#">Phiếu quà tặng</a></li>
                                    <li><a href="#">Liên kết</a></li>
                                    <li><a href="#">Đặc biệt</a></li>
                                    <li><a href="contact.html">Sơ đồ web</a></li>
                                    <li><a href="my-account.html">Tài khoản của tôi</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="widgets_container contact_us">
                            <h3>Contact Us</h3>
                            <div class="footer_contact">
                                <p>Địa chỉ: 6688 Princess Road, London, Greater London BAS 23JK, UK</p>
                                <p>Số điện thoại: <a href="tel:+(+012)800456789-987">(+012) 800 456 789 - 987</a> </p>
                                <p>Email: demo@example.com</p>
                                <ul>
                                    <li><a href="#" title="Twitter"><i class="fa fa-twitter"></i></a></li>
                                    <li><a href="#" title="google-plus"><i class="fa fa-google-plus"></i></a></li>
                                    <li><a href="#" title="facebook"><i class="fa fa-facebook"></i></a></li>
                                    <li><a href="#" title="youtube"><i class="fa fa-youtube"></i></a></li>
                                </ul>
                              
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="widgets_container newsletter">
                            <h3>Tham gia bản tin của chúng tôi ngay bây giờ</h3>
                            <div class="newleter-content">
                                <p>Chất lượng vượt trội. Nhà máy có đạo đức. Đăng ký để được miễn phí vận chuyển và trả hàng tại Hoa Kỳ cho đơn hàng đầu tiên của bạn.</p>
                                 <div class="subscribe_form">
                                    <form id="mc-form" class="mc-form footer-newsletter" >
                                        <input id="mc-email" type="email" autocomplete="off" placeholder="Nhập địa chỉ email của bạn..." />
                                        <button id="mc-submit">Đặt mua!</button>
                                    </form>
                                    <!-- mailchimp-alerts Start -->
                                    <div class="mailchimp-alerts text-centre">
                                        <div class="mailchimp-submitting"></div><!-- mailchimp-submitting end -->
                                        <div class="mailchimp-success"></div><!-- mailchimp-success end -->
                                        <div class="mailchimp-error"></div><!-- mailchimp-error end -->
                                    </div><!-- mailchimp-alerts end -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer_bottom">
            <div class="container">
               <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="copyright_area">
                            <p> &copy; 2021 <strong> </strong> Mede with ❤️ by <a href="https://hasthemes.com/" target="_blank"><strong>HasThemes</strong></a></p>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="footer_custom_links">
                            <ul>
                                <li><a href="#">Lịch sử đơn hàng</a></li>
                                <li><a href="wishlist.html">Danh sách yêu thích</a></li>
                                <li><a href="#">Bản tin</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!--footer area end-->
    
    <!-- modal area start-->
    <div class="modal fade" id="modal_box" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <div class="modal_body">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-5 col-md-5 col-sm-12">
                                <div class="modal_tab">  
                                    <div class="tab-content product-details-large">
                                        <div class="tab-pane fade show active" id="tab1" role="tabpanel" >
                                            <div class="modal_tab_img">
                                                <a href="#"><img src="assets/img/product/product4.jpg" alt=""></a>    
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="tab2" role="tabpanel">
                                            <div class="modal_tab_img">
                                                <a href="#"><img src="assets/img/product/product6.jpg" alt=""></a>    
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="tab3" role="tabpanel">
                                            <div class="modal_tab_img">
                                                <a href="#"><img src="assets/img/product/product8.jpg" alt=""></a>    
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="tab4" role="tabpanel">
                                            <div class="modal_tab_img">
                                                <a href="#"><img src="assets/img/product/product2.jpg" alt=""></a>    
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="tab5" role="tabpanel">
                                            <div class="modal_tab_img">
                                                <a href="#"><img src="assets/img/product/product12.jpg" alt=""></a>    
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal_tab_button">    
                                        <ul class="nav product_navactive owl-carousel" role="tablist">
                                            <li >
                                                <a class="nav-link active" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="false"><img src="assets/img/s-product/product3.jpg" alt=""></a>
                                            </li>
                                            <li>
                                                 <a class="nav-link" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false"><img src="assets/img/s-product/product.jpg" alt=""></a>
                                            </li>
                                            <li>
                                               <a class="nav-link button_three" data-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="false"><img src="assets/img/s-product/product2.jpg" alt=""></a>
                                            </li>
                                            <li>
                                               <a class="nav-link" data-toggle="tab" href="#tab4" role="tab" aria-controls="tab4" aria-selected="false"><img src="assets/img/s-product/product4.jpg" alt=""></a>
                                            </li>
                                            <li>
                                               <a class="nav-link" data-toggle="tab" href="#tab5" role="tab" aria-controls="tab5" aria-selected="false"><img src="assets/img/s-product/product5.jpg" alt=""></a>
                                            </li>

                                        </ul>
                                    </div>    
                                </div>  
                            </div> 
                            <div class="col-lg-7 col-md-7 col-sm-12">
                                <div class="modal_right">
                                    <div class="modal_title mb-10">
                                        <h2>Handbag feugiat</h2> 
                                    </div>
                                    <div class="modal_price mb-10">
                                        <span class="new_price">$64.99</span>    
                                        <span class="old_price" >$78.99</span>    
                                    </div>
                                    <div class="modal_description mb-15">
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Mollitia iste laborum ad impedit pariatur esse optio tempora sint ullam autem deleniti nam in quos qui nemo ipsum numquam, reiciendis maiores quidem aperiam, rerum vel recusandae </p>    
                                    </div> 
                                    <div class="variants_selects">
                                        <div class="variants_size">
                                           <h2>size</h2>
                                           <select class="select_option">
                                               <option selected value="1">s</option>
                                               <option value="1">m</option>
                                               <option value="1">l</option>
                                               <option value="1">xl</option>
                                               <option value="1">xxl</option>
                                           </select>
                                        </div>
                                        <div class="variants_color">
                                           <h2>color</h2>
                                           <select class="select_option">
                                               <option selected value="1">purple</option>
                                               <option value="1">violet</option>
                                               <option value="1">black</option>
                                               <option value="1">pink</option>
                                               <option value="1">orange</option>
                                           </select>
                                        </div>
                                        <div class="modal_add_to_cart">
                                            <form action="#">
                                                <input min="0" max="100" step="2" value="1" type="number">
                                                <button type="submit">add to cart</button>
                                            </form>
                                        </div>   
                                    </div>
                                    <div class="modal_social">
                                        <h2>Share this product</h2>
                                        <ul>
                                            <li class="facebook"><a href="#"><i class="fa fa-facebook"></i></a></li>
                                            <li class="twitter"><a href="#"><i class="fa fa-twitter"></i></a></li>
                                            <li class="pinterest"><a href="#"><i class="fa fa-pinterest"></i></a></li>
                                            <li class="google-plus"><a href="#"><i class="fa fa-google-plus"></i></a></li>
                                            <li class="linkedin"><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                        </ul>    
                                    </div>      
                                </div>    
                            </div>    
                        </div>     
                    </div>
                </div>    
            </div>
        </div>
    </div> 
    <!-- modal area start-->
    

<!-- JS
============================================ -->

<!-- Plugins JS -->
<script src="assets/js/plugins.js"></script>

<!-- Main JS -->
<script src="assets/js/main.js"></script>



</body>

</html>