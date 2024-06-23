<?php
ob_start();
session_start();
require('config/database.php');
require('config/session.php');

// Memanggil nilai dari sesi
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$level = isset($_SESSION['level']) ? $_SESSION['level'] : '';

// Mendapatkan nama produk dari URL
$product_name = isset($_GET['product_name']) ? $_GET['product_name'] : '';

// Fetch produk berdasarkan nama
$product_query = pg_query($conn, "SELECT * FROM tb_produk WHERE nama_produk = '".pg_escape_string($product_name)."'");
$product = pg_fetch_assoc($product_query);

// Fetch category name
$category_query = pg_query($conn, "SELECT nama_kategori FROM tb_kategori_produk WHERE id_kategori = '".$product['kategori']."'");
$category = pg_fetch_assoc($category_query);

// Fetch product images
$product_images_query = pg_query($conn, "SELECT * FROM tb_produk_gambar WHERE id_produk = '".$product['id_produk']."'");
$product_images = pg_fetch_all($product_images_query);

$product_images_by_id = [];
foreach ($product_images as $image) {
    $product_images_by_id[] = 'data:image/jpeg;base64,' . $image['gambar'];
}

if (!$product) {
    die('Produk tidak ditemukan.');
}
function formatRupiah($number){
    return 'Rp ' . number_format($number, 2, ',', '.');
}

$sid = session_id();
$result = pg_query($conn, "SELECT * FROM tb_settings WHERE id_setting = 1");
$s0 = pg_fetch_array($result);
$urlweb = $uri;
$pengguna = 1;
$ip = $_SERVER['REMOTE_ADDR'];
$date = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Produk <?= $product['nama_produk']; ?> | <?= $s0['nama_toko']  ?></title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="description" content="Detail Produk">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="<?php echo $urlweb; ?>/assets/styles/bootstrap4/bootstrap.min.css">
<link href="<?php echo $urlweb; ?>/assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="<?php echo $urlweb; ?>/assets/plugins/OwlCarousel2-2.2.1/owl.carousel.css">
<link rel="stylesheet" type="text/css" href="<?php echo $urlweb; ?>/assets/plugins/OwlCarousel2-2.2.1/owl.theme.default.css">
<link rel="stylesheet" type="text/css" href="<?php echo $urlweb; ?>/assets/plugins/OwlCarousel2-2.2.1/animate.css">
<link rel="stylesheet" href="<?php echo $urlweb; ?>/assets/plugins/themify-icons/themify-icons.css">
<link rel="stylesheet" type="text/css" href="<?php echo $urlweb; ?>/assets/plugins/jquery-ui-1.12.1.custom/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="<?php echo $urlweb; ?>/assets/styles/single_styles.css">
<link rel="stylesheet" type="text/css" href="<?php echo $urlweb; ?>/assets/styles/single_responsive.css">
</head>
<body>
<div class="super_container">
    <!-- Header -->
    <header class="header trans_300">

<!-- Top Navigation -->



<!-- Main Navigation -->

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
                    <li><a href="categories.html"><i class="fa fa-angle-right" aria-hidden="true"></i>Shop</a></li>
                    <li><a href="index.html"><i class="fa fa-angle-right" aria-hidden="true"></i><?= $category['nama_kategori'] ?></a></li>
                    <li class="active"><a href="#"><i class="fa fa-angle-right" aria-hidden="true"></i><?= $product['nama_produk']; ?></a></li>
                </ul>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="single_product_pics">
                <div class="row">
                    <div class="col-lg-3 thumbnails_col order-lg-1 order-2">
                        <div class="single_product_thumbnails">
                            <ul>
                                <?php foreach ($product_images_by_id as $index => $image): ?>
                                    <li class="<?= $index === 0 ? 'active' : '' ?>"><img src="<?= $image ?>" alt="Product Image" data-image="<?= $image ?>"></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-9 image_col order-lg-2 order-1">
                        <div class="single_product_image">
                            <div class="single_product_image_background" id="main_product_image" style="background-image:url(<?= $product_images_by_id[0] ?>)"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="product_details">
                <div class="product_details_title">
                    <h2><?= $product['nama_produk']; ?></h2>
                    <p><?= substr($product['deskripsi_produk'], 0, 150); ?>...</p>
                </div>
                <div class="free_delivery d-flex flex-row align-items-center justify-content-center">
                    <span class="ti-truck"></span><span>Gratis Ongkir Wilayah Bandung!</span>
                </div>
                <div class="original_price"><?= formatRupiah($product['harga']); ?></div>
                <div class="product_price"><?= formatRupiah($product['harga']); ?></div>
                <ul class="star_rating">
                    <li><i class="fa fa-star" aria-hidden="true"></i></li>
                    <li><i class="fa fa-star" aria-hidden="true"></i></li>
                    <li><i class="fa fa-star" aria-hidden="true"></i></li>
                    <li><i class="fa fa-star" aria-hidden="true"></i></li>
                    <li><i class="fa fa-star-o" aria-hidden="true"></i></li>
                </ul>
                <div class="product_color">
                    <span>Opsi Pilihan:</span>
                    <ul>
                    <?php
                    $options_query = pg_query($conn, "SELECT * FROM tb_products_options WHERE id_produk = '".$product['id_produk']."'");
                    $options = pg_fetch_all($options_query);
                    if ($options) {
                        foreach ($options as $option): ?>
                            <li><?= $option['option_name'] ?>: <?= $option['option_value'] ?></li>
                        <?php endforeach;
                    } else {
                        echo '<li>N/A</li>';
                    }
                    ?>
                    </ul>
                </div>
                <div class="quantity d-flex flex-column flex-sm-row align-items-sm-center">
                    <span>Jumlah:</span>
                    <div class="quantity_selector">
                        <span class="minus"><i class="fa fa-minus" aria-hidden="true"></i></span>
                        <span id="quantity_value">1</span>
                        <span class="plus"><i class="fa fa-plus" aria-hidden="true"></i></span>
                    </div>
                    <div class="red_button add_to_cart_button"><a href="#"> + Keranjang</a></div>
                    <div class="product_favorite d-flex flex-column align-items-center justify-content-center"></div>
                </div>
            </div>
        </div>
    </div>

</div>

 <!-- Tabs -->
 <div class="tabs_section_container">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="tabs_container">
                        <ul class="tabs d-flex flex-sm-row flex-column align-items-left align-items-md-center justify-content-center">
                            <li class="tab active" data-active-tab="tab_1"><span>Detail</span></li>
                            <li class="tab" data-active-tab="tab_2"><span>Deskripsi</span></li>
                            <li class="tab" data-active-tab="tab_3"><span>Informasi Produk</span></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <!-- Tab Detail -->
                    <div id="tab_1" class="tab_container active">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="tab_title additional_info_title">
                                    <h4>Detail Produk</h4>
                                </div>
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <th>Kategori</th>
                                            <td><?= $category['nama_kategori'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Stok</th>
                                            <td><?= $product['stok'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Berat</th>
                                            <td><?= $product['berat'] ?> gram</td>
                                        </tr>
                                        <tr>
                                            <th>SKU</th>
                                            <td><?= $product['kode_sku'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Variant</th>
                                            <td>
                                                <?php
                                                if ($options) {
                                                    foreach ($options as $option) {
                                                        echo $option['option_name'] . ': ' . $option['option_value'] . '<br>';
                                                    }
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-12 desc_col">
                                <div class="tab_title" style="margin-bottom: 10px;">
                                    <h4>Deskripsi</h4>
                                </div>
                                <div class="tab_text_block">
                                    <p class="lead" style="font-size: 1rem; line-height: 1.2; margin-top: 0;"><?= nl2br(htmlspecialchars($product['deskripsi_produk'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Description -->
                    <div id="tab_2" class="tab_container">
                        <div class="row">
                        <div class="col-lg-12 desc_col">
                            <div class="tab_title" style="margin-bottom: 10px;">
                                <h4>Deskripsi</h4>
                            </div>
                            <div class="tab_text_block">
                                <p class="lead" style="font-size: 1rem; line-height: 1.2; margin-top: 0;"><?= nl2br(htmlspecialchars($product['deskripsi_produk'])); ?></p>
                            </div>
                        </div>
                        </div>
                    </div>

                    <!-- Tab Additional Info -->
                    <div id="tab_3" class="tab_container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="tab_title additional_info_title">
                                    <h4>Informasi Produk</h4>
                                </div>
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <th>Kategori</th>
                                            <td><?= $category['nama_kategori'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Stok</th>
                                            <td><?= $product['stok'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Berat</th>
                                            <td><?= $product['berat'] ?> gram</td>
                                        </tr>
                                        <tr>
                                            <th>SKU</th>
                                            <td><?= $product['kode_sku'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Variant</th>
                                            <td>
                                                <?php
                                                if ($options) {
                                                    foreach ($options as $option) {
                                                        echo $option['option_name'] . ': ' . $option['option_value'] . '<br>';
                                                    }
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Start Footer -->
        <?php include 'home/component/footer.php' ?>
    <!-- End Footer -->
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const thumbnails = document.querySelectorAll('.single_product_thumbnails ul li img');
        const mainImage = document.getElementById('main_product_image');

        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                const imageUrl = thumbnail.getAttribute('data-image');
                mainImage.style.backgroundImage = `url(${imageUrl})`;
            });
        });
    });
</script>

<script src="<?php echo $urlweb; ?>/assets/js/jquery-3.2.1.min.js"></script>
<script src="<?php echo $urlweb; ?>/assets/styles/bootstrap4/popper.js"></script>
<script src="<?php echo $urlweb; ?>/assets/styles/bootstrap4/bootstrap.min.js"></script>
<script src="<?php echo $urlweb; ?>/assets/plugins/Isotope/isotope.pkgd.min.js"></script>
<script src="<?php echo $urlweb; ?>/assets/plugins/OwlCarousel2-2.2.1/owl.carousel.js"></script>
<script src="<?php echo $urlweb; ?>/assets/plugins/easing/easing.js"></script>
<script src="<?php echo $urlweb; ?>/assets/js/single_custom.js"></script>
</body>
</html>
