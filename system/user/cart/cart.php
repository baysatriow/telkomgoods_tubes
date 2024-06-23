<?php
ob_start();
session_start();
require('config/database.php');

// Memanggil nilai dari sesi
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$level = isset($_SESSION['level']) ? $_SESSION['level'] : '';

// Fetch cart items count
$cart_count = 0;

if ($level != '') {
    $username = $_SESSION['username'];

    // Fetch the user_id based on username
    $user_query = pg_query($conn, "SELECT id_user FROM tb_user WHERE username = '".pg_escape_string($username)."'");
    if ($user_query) {
        $user_result = pg_fetch_assoc($user_query);
        $user_id = $user_result['id_user'];

        // Fetch the total quantity of items in the cart for the user
        $cart_query = pg_query($conn, "SELECT SUM(quantity) AS total_items FROM tb_keranjang WHERE user_id = '".pg_escape_string($user_id)."'");
        if ($cart_query) {
            $cart_result = pg_fetch_assoc($cart_query);
            $cart_count = $cart_result['total_items'] ? $cart_result['total_items'] : 0;
        }
    }
}

// Fetch categories
$categories_query = pg_query($conn, "SELECT * FROM tb_kategori_produk");
$categories = pg_fetch_all($categories_query);

// Fetch products
$products_query = pg_query($conn, "SELECT * FROM tb_produk");
$products = pg_fetch_all($products_query);

// Fetch top 10 products based on 'terjual'
$products_query1 = pg_query($conn, "SELECT * FROM tb_produk ORDER BY terjual DESC LIMIT 5");
$terjual = pg_fetch_all($products_query1);

// Fetch product images
$product_images_query = pg_query($conn, "SELECT * FROM tb_produk_gambar");
$product_images = pg_fetch_all($product_images_query);

$product_images_by_id = [];
foreach ($product_images as $image) {
    $product_images_by_id[$image['id_produk']][] = $image['gambar'];
}

$sid = session_id();
$result = pg_query($conn, "SELECT * FROM tb_settings WHERE id_setting = 1");
$result1 = pg_query($conn, "SELECT * FROM tb_user WHERE id_user = 1");
if (!$result) {
    die('Query gagal: ' . pg_last_error());
}
if (!$result1) {
    die('Query gagal: ' . pg_last_error());
}
$s0 = pg_fetch_array($result);
$s1 = pg_fetch_array($result1);
$urlweb = $uri;
$pengguna = 1;
$ip = $_SERVER['REMOTE_ADDR'];
$date = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Keranjang | <?= $s0['nama_toko'] ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Detail Produk">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="<?php echo $urlweb; ?>/assets/styles/bootstrap4/bootstrap.min.css">
    <link href="<?php echo $urlweb; ?>/assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?php echo $urlweb; ?>/assets/styles/cart_styles.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $urlweb; ?>/assets/styles/cart_responsive.css">
</head>
<body>
<div class="super_container">
    <!-- Header -->
    <header class="header trans_300">
        <div class="main_nav_container">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 text-right">
                        <div class="logo_container">
                            <a href="."><?php echo $s0['nama_toko']; ?></a>
                        </div>
                        <!-- Start Navbar -->
                        <?php include 'home/component/navbar.php' ?>
                        <!-- End Navbar -->
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="fs_menu_overlay"></div>

    <!-- Hamburger Menu -->

    <div class="hamburger_menu">
        <div class="hamburger_close"><i class="fa fa-times" aria-hidden="true"></i></div>
        <div class="hamburger_menu_content text-right">
            <ul class="menu_top_nav">
                <li class="menu_item has-children">
                    <a href="#">
                        usd
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="menu_selection">
                        <li><a href="#">cad</a></li>
                        <li><a href="#">aud</a></li>
                        <li><a href="#">eur</a></li>
                        <li><a href="#">gbp</a></li>
                    </ul>
                </li>
                <li class="menu_item has-children">
                    <a href="#">
                        English
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="menu_selection">
                        <li><a href="#">French</a></li>
                        <li><a href="#">Italian</a></li>
                        <li><a href="#">German</a></li>
                        <li><a href="#">Spanish</a></li>
                    </ul>
                </li>
                <li class="menu_item has-children">
                    <a href="#">
                        My Account
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="menu_selection">
                        <li><a href="#"><i class="fa fa-sign-in" aria-hidden="true"></i>Sign In</a></li>
                        <li><a href="#"><i class="fa fa-user-plus" aria-hidden="true"></i>Register</a></li>
                    </ul>
                </li>
                <li class="menu_item"><a href="#">home</a></li>
                <li class="menu_item"><a href="#">shop</a></li>
                <li class="menu_item"><a href="#">promotion</a></li>
                <li class="menu_item"><a href="#">pages</a></li>
                <li class="menu_item"><a href="#">blog</a></li>
                <li class="menu_item"><a href="#">contact</a></li>
            </ul>
        </div>
    </div>

    <div class="container single_product_container">
        <div class="row">
            <div class="col">

                <!-- Breadcrumbs -->
                <div class="breadcrumbs d-flex flex-row align-items-center">
                    <ul>
                        <li><a href="index.html">Home</a></li>
                        <li><a href="shop.html"><i class="fa fa-angle-right" aria-hidden="true"></i>Shop</a></li>
                        <li class="active"><a href="#"><i class="fa fa-angle-right" aria-hidden="true"></i>Keranjang</a></li>
                    </ul>
                </div>

            </div>
        </div>

        <!-- Cart -->
        <div class="card">
            <div class="row">
                <div class="col-md-8 cart">
                    <div class="title">
                        <div class="row">
                            <div class="col"><h4><b>Keranjang</b></h4></div>
                            <div class="col align-self-center text-right text-muted"><?= $cart_count ?> items</div>
                        </div>
                    </div>
                    <?php
                    $cart_query = pg_query($conn, "SELECT * FROM tb_keranjang WHERE user_id = '$user_id'");
                    while ($item = pg_fetch_assoc($cart_query)) {
                        $product_query = pg_query($conn, "SELECT * FROM tb_produk WHERE id_produk = '".$item['product_id']."'");
                        $product = pg_fetch_assoc($product_query);
                        $product_image_query = pg_query($conn, "SELECT * FROM tb_produk_gambar WHERE id_produk = '".$item['product_id']."' LIMIT 1");
                        $product_image = pg_fetch_assoc($product_image_query)['gambar'];
                    ?>
                    <div class="row border-top border-bottom">
                        <div class="row main align-items-center">
                            <div class="col-2"><img class="img-fluid" src="data:image/jpeg;base64,<?= $product_image ?>"></div>
                            <div class="col">
                                <div class="row text-muted"><?= $product['nama_produk'] ?></div>
                            </div>
                            <div class="col">
                                <a href="#">-</a><a href="#" class="border"><?= $item['quantity'] ?></a><a href="#">+</a>
                            </div>
                            <div class="col">Rp <?= number_format($product['harga'], 0, ',', '.') ?> <span class="close">&#10005;</span></div>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="back-to-shop"><a href="#">&leftarrow;</a><span class="text-muted">Kembali ke Shop</span></div>
                </div>
                <div class="col-md-4 summary">
                    <div><h5><b>Ringkasan</b></h5></div>
                    <hr>
                    <div class="row">
                        <div class="col" style="padding-left:0;">ITEMS <?= $cart_count ?></div>
                        <div class="col text-right">Rp <?= number_format($total_price, 0, ',', '.') ?></div>
                    </div>
                    <form>
                        <p>KODE VOUCHER</p>
                        <input id="code" placeholder="Enter your code">
                    </form>
                    <div class="row" style="border-top: 1px solid rgba(0,0,0,.1); padding: 2vh 0;">
                        <div class="col">TOTAL HARGA</div>
                        <div class="col text-right">Rp <?= number_format($total_price + 5000, 0, ',', '.') ?></div>
                    </div>
                    <button class="btn">CHECKOUT</button>
                </div>
            </div>
        </div>

    </div>


    <!-- Start Footer -->
    <?php include 'home/component/footer.php' ?>
    <!-- End Footer -->
</div>

<script src="<?php echo $urlweb; ?>/assets/js/jquery-3.2.1.min.js"></script>
<script src="<?php echo $urlweb; ?>/assets/styles/bootstrap4/popper.js"></script>
<script src="<?php echo $urlweb; ?>/assets/styles/bootstrap4/bootstrap.min.js"></script>
<script src="<?php echo $urlweb; ?>/assets/plugins/Isotope/isotope.pkgd.min.js"></script>
<script src="<?php echo $urlweb; ?>/assets/plugins/OwlCarousel2-2.2.1/owl.carousel.js"></script>
<script src="<?php echo $urlweb; ?>/assets/plugins/easing/easing.js"></script>
<script src="<?php echo $urlweb; ?>/assets/js/single_custom.js"></script>
</body>
</html>
