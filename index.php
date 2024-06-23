<?php
ob_start();
session_start();
require('config/database.php');
require('config/session.php');

// Ambil Nilai Session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$level = isset($_SESSION['level']) ? $_SESSION['level'] : '';

// Inisiasi Keranjang
$cart_count = 0;

if ($level != '') {
    $username = $_SESSION['username'];

    // Fetch the user_id based on username
    $user_query = pg_query($conn, "SELECT id_user FROM tb_user WHERE username = '".($username)."'");
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

// Fetch categories with 'terbaru = 1'
$categories_query = pg_query($conn, "SELECT * FROM tb_kategori_produk WHERE terbaru = 1");
$categories = pg_fetch_all($categories_query);

// Initialize an array to store products by category
$products_by_category = [];

// Loop through each category to fetch products
if ($categories) {
    foreach ($categories as $category) {
        // Fetch products for the current category, limited to 5
        $category_id = $category['id_kategori']; // Assuming the primary key is 'id'
        $products_query = pg_query($conn, "SELECT * FROM tb_produk WHERE kategori = $category_id LIMIT 5");
        $products = pg_fetch_all($products_query);
        
        // Store products in the array by category id
        if ($products) {
            $products_by_category[$category_id] = $products;
        } else {
            $products_by_category[$category_id] = []; // If no products found, store an empty array
        }
    }
}

// Fetch top 15 latest products across all latest categories
$latest_products_query = pg_query($conn, "SELECT * FROM tb_produk WHERE kategori IN (SELECT id_kategori FROM tb_kategori_produk WHERE terbaru = 1) LIMIT 15");
$latest_products = pg_fetch_all($latest_products_query);

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

// Fetch deals of the week from tb_banner_promo
$deals_query = pg_query($conn, "SELECT * FROM tb_banner_promo WHERE status = 1");
$deals = pg_fetch_all($deals_query);

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
<title><?php echo $s0['nama_toko']; ?></title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="description" content="Colo Shop Template">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="<?php echo $urlweb; ?>/assets/styles/bootstrap4/bootstrap.min.css">
<link href="<?php echo $urlweb; ?>/assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="<?php echo $urlweb; ?>/assets/plugins/OwlCarousel2-2.2.1/owl.carousel.css">
<link rel="stylesheet" type="text/css" href="<?php echo $urlweb; ?>/assets/plugins/OwlCarousel2-2.2.1/owl.theme.default.css">
<link rel="stylesheet" type="text/css" href="<?php echo $urlweb; ?>/assets/plugins/OwlCarousel2-2.2.1/animate.css">
<script src="<?php echo $urlweb; ?>/assets/admin/js/config.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $urlweb; ?>/assets/styles/main_styles.css">
<link rel="stylesheet" type="text/css" href="<?php echo $urlweb; ?>/assets/styles/responsive.css">
</head>

<body>
<div class="super_container">
    <header class="header trans_300">
        <div class="main_nav_container">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 text-right">
                        <div class="logo_container">
                            <a href="."><?php echo $s0['nama_toko']; ?></a>
                        </div>
                        <?php include 'home/component/navbar.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="fs_menu_overlay"></div>
    <!-- Start Menu Smartphone -->
    <?php include 'home/component/humberger.php'?>
    <!-- End Menu Smartphone -->

    <!-- Main Slider -->
    <div class="main_slider" style="background-image:url(<?php echo $urlweb; ?>/assets/images/rgb.jpg)">
        <div class="container fill_height">
            <div class="row align-items-center fill_height">
                <div class="col">
                    <div class="main_slider_content">
                        <h6>PC GAMING SPEK DEWA!</h6>
                        <h1>Diskon Hingga 30%</h1>
                        <div class="red_button shop_now_button"><a href="#">belanja sekarang</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Banner -->
    <div class="banner">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="banner_item align-items-center" style="background-image:url(<?php echo $urlweb; ?>/assets/images/banner30.jpg)">
                        <div class="banner_category">
                            <a href="categories.html">Komputer</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="banner_item align-items-center" style="background-image:url(<?php echo $urlweb; ?>/assets/images/oip.jpg)">
                        <div class="banner_category">
                            <a href="categories.html">aksesoris</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="banner_item align-items-center" style="background-image:url(<?php echo $urlweb; ?>/assets/images/iop.jpg)">
                        <div class="banner_category">
                            <a href="categories.html">komponen</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Arrivals -->
    <div class="new_arrivals">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <div class="section_title new_arrivals_title">
                        <ul id="products-list"></ul>
                        <h2>Terbaru</h2>
                    </div>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col text-center">
                    <div class="new_arrivals_sorting">
                        <ul class="arrivals_grid_sorting clearfix button-group filters-button-group">
                            <li class="grid_sorting_button button d-flex flex-column justify-content-center align-items-center active is-checked" data-filter="*">Semua</li>
                            <?php foreach ($categories as $category) { ?>
                                <li class="grid_sorting_button button d-flex flex-column justify-content-center align-items-center" data-filter=".category-<?= $category['id_kategori'] ?>"><?= $category['nama_kategori'] ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="product-grid" data-isotope='{ "itemSelector": ".product-item", "layoutMode": "fitRows" }'>
                        <?php foreach ($latest_products as $product) {
                            $product_image = isset($product_images_by_id[$product['id_produk']][0]) ? 'data:image/jpeg;base64,' . $product_images_by_id[$product['id_produk']][0] : $urlweb . '/assets/images/default.png';
                            $category_class = 'category-' . $product['kategori'];
                        ?>
                            <div class="product-item <?= $category_class ?>" data-product-id="<?= $product['id_produk'] ?>">
                                <div class="product product_filter">
                                    <div class="product_image">
                                        <img src="<?= $product_image ?>" alt="" class="product-image">
                                    </div>
                                    <div class="product_info">
                                        <h6 class="product_name"><a href="detail?product_name=<?= urlencode($product['nama_produk']) ?>"><?= $product['nama_produk'] ?></a></h6>
                                        <div class="product_price">Rp <?= number_format($product['harga'], 0, ',', '.') ?></div>
                                    </div>
                                </div>
                                <?php if ($username != 'guest') { ?>
                                <div class="red_button add_to_cart_button"><a href="#">+ keranjang</a></div>
                                <?php } else if ($level != '') { ?>
                                <div class="red_button add_to_cart_button"><a href="#">+ keranjang</a></div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deal of the Week Carousel -->
    <div class="deal_ofthe_week">
        <div class="container">
            <div class="row align-items-center">
                <div class="col text-center">
                    <div class="section_title new_arrivals_title">
                        <h2>Hanya Minggu Ini</h2>
                    </div>
                </div>
            </div>
            <div class="row deal_ofthe_week_row">
                <?php foreach ($deals as $deal) { 
                    $end_date = new DateTime($deal['tgl_selesai']);
                    $now = new DateTime();
                    $interval = $now->diff($end_date);
                ?>
                <div class="col-lg-6">
                    <div class="deal_ofthe_week_img">
                        <img src="data:image/jpeg;base64,<?php echo $deal['image']; ?>" alt="<?php echo $deal['nama_promo']; ?>">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="deal_ofthe_week_content d-flex flex-column align-items-center text-center">
                        <div class="section_title">
                            <h2><?php echo $deal['nama_promo']; ?></h2>
                        </div>
                        <ul class="timer" data-end-date="<?php echo $deal['tgl_selesai']; ?>">
                            <li class="d-inline-flex flex-column justify-content-center align-items-center">
                                <div class="timer_num" id="day"></div>
                                <div class="timer_unit">Hari</div>
                            </li>
                            <li class="d-inline-flex flex-column justify-content-center align-items-center">
                                <div class="timer_num" id="hour">=</div>
                                <div class="timer_unit">Jam</div>
                            </li>
                            <li class="d-inline-flex flex-column justify-content-center align-items-center">
                                <div class="timer_num" id="minute"></div>
                                <div class="timer_unit">Menit</div>
                            </li>
                            <li class="d-inline-flex flex-column justify-content-center align-items-center">
                                <div class="timer_num" id="second"></div>
                                <div class="timer_unit">Detik</div>
                            </li>
                        </ul>
                        <div class="product_price_banner">Rp <?= number_format($deal['harga'] - ($deal['harga'] * $deal['diskon'] / 100), 0, ',', '.') ?> <span>Rp <?= number_format($deal['harga'], 0, ',', '.') ?></span></div>
                        <div class="red_button deal_ofthe_week_button"><a href="#">Belanja Sekarang</a></div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <!-- Best Sellers -->
    <div class="best_sellers">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <div class="section_title new_arrivals_title">
                        <h2>Terlaris</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="product_slider_container">
                        <div class="owl-carousel owl-theme product_slider">
                            <?php foreach ($terjual as $product) {
                                $product_image = isset($product_images_by_id[$product['id_produk']][0]) ? 'data:image/jpeg;base64,' . $product_images_by_id[$product['id_produk']][0] : $urlweb . '/assets/images/default.png';
                            ?>
                            <div class="owl-item product_slider_item">
                                <div class="product-item">
                                    <div class="product discount">
                                        <div class="product_image">
                                            <img src="<?= $product_image ?>" alt="" class="product-image">
                                        </div>
                                        <div class="favorite favorite_left"></div>
                                        <div class="product_bubble product_bubble_right product_bubble_red d-flex flex-column align-items-center"><span>-$20</span></div>
                                        <div class="product_info">
                                            <h6 class="product_name"><a href="detail?product_name=<?= urlencode($product['nama_produk']) ?>"><?= $product['nama_produk'] ?></a></h6>
                                            <div class="product_price">Rp <?= number_format($product['harga'], 0, ',', '.') ?></div>
                                            <div class="product_terjual">Terjual: <?= $product['terjual'] ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>

                        <!-- Slider Navigation -->
                        <div class="product_slider_nav_left product_slider_nav d-flex align-items-center justify-content-center flex-column">
                            <i class="fa fa-chevron-left" aria-hidden="true"></i>
                        </div>
                        <div class="product_slider_nav_right product_slider_nav d-flex align-items-center justify-content-center flex-column">
                            <i class="fa fa-chevron-right" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Benefits -->
    <div class="benefit">
        <div class="container">
            <div class="row benefit_row">
                <div class="col-lg-4 benefit_col">
                    <div class="benefit_item d-flex flex-row align-items-center">
                        <div class="benefit_icon"><i class="fa fa-truck" aria-hidden="true"></i></div>
                        <div class="benefit_content">
                            <h6>gratis ongkir</h6>
                            <p>Syarat dan Ketentuan berlaku</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 benefit_col">
                    <div class="benefit_item d-flex flex-row align-items-center">
                        <div class="benefit_icon"><i class="fa fa-money" aria-hidden="true"></i></div>
                        <div class="benefit_content">
                            <h6>cash on delivery</h6>
                            <p>Syarat dan Ketentuan Berlaku</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 benefit_col">
                    <div class="benefit_item d-flex flex-row align-items-center">
                        <div class="benefit_icon"><i class="fa fa-undo" aria-hidden="true"></i></div>
                        <div class="benefit_content">
                            <h6>Jaminan Uang Kembali</h6>
                            <p>Syarat dan Ketentuan Berlaku</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Newsletter -->
    <div class="newsletter">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="newsletter_text d-flex flex-column justify-content-center align-items-lg-start align-items-md-center text-center">
                        <h4>Buletin</h4>
                        <p>Berlangganan dan dapatkan diskon 20% untuk pembelian pertama Anda</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <form action="post">
                        <div class="newsletter_form d-flex flex-md-row flex-column flex-xs-column align-items-center justify-content-lg-end justify-content-center">
                            <input id="newsletter_email" type="email" placeholder="Your email" required="required" data-error="Valid email is required.">
                            <button id="newsletter_submit" type="submit" class="newsletter_submit_btn trans_300" value="Submit">Berlangganan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="footer_nav_container d-flex flex-sm-row flex-column align-items-center justify-content-lg-start justify-content-center text-center">
                        <ul class="footer_nav">
                            <li><a href="#">Blog</a></li>
                            <li><a href="#">FAQs</a></li>
                            <li><a href="contact.html">Hubungi Kami</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="footer_social d-flex flex-row align-items-center justify-content-lg-end justify-content-center">
                        <ul>
                            <li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                            <li><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                            <li><a href="#"><i class="fa fa-skype" aria-hidden="true"></i></a></li>
                            <li><a href="#"><i class="fa fa-pinterest" aria-hidden="true"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="footer_nav_container">
                        <div class="cr">©2024 All Rights Reserverd. This Website is made with <i class="fa fa-heart-o" aria-hidden="true"></i> by <a href="#">Baysatriow & PALLL</a></div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>
<!-- jQuery -->
<script src="<?php echo $urlweb; ?>/assets/js/jquery-3.2.1.min.js"></script>
<!-- Bootstrap -->
<script src="<?php echo $urlweb; ?>/assets/styles/bootstrap4/popper.js"></script>
<script src="<?php echo $urlweb; ?>/assets/styles/bootstrap4/bootstrap.min.js"></script>
<!-- Isotope -->
<script src="<?php echo $urlweb; ?>/assets/plugins/Isotope/isotope.pkgd.min.js"></script>
<!-- Owl Carousel -->
<script src="<?php echo $urlweb; ?>/assets/plugins/OwlCarousel2-2.2.1/owl.carousel.js"></script>
<!-- Easing -->
<script src="<?php echo $urlweb; ?>/assets/plugins/easing/easing.js"></script>
<!-- Custom Script -->
<script src="<?php echo $urlweb; ?>/assets/js/custom.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add_to_cart_button a').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            <?php if ($level != '') { ?>
            let productId = this.closest('.product-item').dataset.productId;
            addToCart(productId);
            <?php } else { ?>
            alert('Anda harus login untuk menambahkan produk ke keranjang');
            <?php } ?>
        });
    });

     // Initialize the timer for each deal of the week
     $('.deal_ofthe_week .timer').each(function() {
        var timer = $(this);
        var endDate = new Date(timer.data('end-date')).getTime();
        setInterval(function() {
            var currentDate = new Date().getTime();
            var secondsLeft = (endDate - currentDate) / 1000;

            var days = parseInt(secondsLeft / 86400);
            secondsLeft = secondsLeft % 86400;

            var hours = parseInt(secondsLeft / 3600);
            secondsLeft = secondsLeft % 3600;

            var minutes = parseInt(secondsLeft / 60);
            var seconds = parseInt(secondsLeft % 60);

            timer.find('#day').text(days);
            timer.find('#hour').text(hours);
            timer.find('#minute').text(minutes);
            timer.find('#second').text(seconds);
        }, 1000);
    });
});

function addToCart(productId) {
    fetch('http://localhost/go-app/addcart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ productId: parseInt(productId) })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Produk berhasil ditambahkan ke keranjang');
            updateCartCount(data.cartCount);
        } else {
            alert('Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function updateCartCount(count) {
    document.getElementById('checkout_items').innerText = count;
}

</script>
</body>
</html>
