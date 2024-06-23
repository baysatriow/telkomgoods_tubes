<?php
ob_start();
session_start();
require('config/database.php');

// Memanggil nilai dari sesi
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$level = isset($_SESSION['level']) ? $_SESSION['level'] : '';

// Fetch cart items count
$cart_count = 0;
$user_id = 0;

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

// Fetch addresses for the user
$addresses_query = pg_query($conn, "SELECT * FROM tb_alamat WHERE id_user = '".pg_escape_string($user_id)."'");
$addresses = pg_fetch_all($addresses_query) ?: [];

// Fetch categories
$categories_query = pg_query($conn, "SELECT * FROM tb_kategori_produk");
$categories = pg_fetch_all($categories_query) ?: [];

// Fetch products
$products_query = pg_query($conn, "SELECT * FROM tb_produk");
$products = pg_fetch_all($products_query) ?: [];

// Fetch top 10 products based on 'terjual'
$products_query1 = pg_query($conn, "SELECT * FROM tb_produk ORDER BY terjual DESC LIMIT 5");
$terjual = pg_fetch_all($products_query1) ?: [];

// Fetch product images
$product_images_query = pg_query($conn, "SELECT * FROM tb_produk_gambar");
$product_images = pg_fetch_all($product_images_query) ?: [];

$product_images_by_id = [];
foreach ($product_images as $image) {
    $product_images_by_id[$image['id_produk']][] = $image['gambar'];
}

// Fetch shipping methods
$shipping_methods_query = pg_query($conn, "SELECT * FROM tb_kurir WHERE status = 1");
$shipping_methods = pg_fetch_all($shipping_methods_query) ?: [];

$sid = session_id();
$result = pg_query($conn, "SELECT * FROM tb_settings WHERE id_setting = 1");
$result1 = pg_query($conn, "SELECT * FROM tb_user WHERE id_user = 1");
if (!$result || !$result1) {
    die('Query gagal: ' . pg_last_error());
}
$s0 = pg_fetch_array($result);
$s1 = pg_fetch_array($result1);
$urlweb = $uri;
$pengguna = 1;
$ip = $_SERVER['REMOTE_ADDR'];
$date = date('Y-m-d');
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Keranjang | <?= $s0['nama_toko'] ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Detail Produk">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="<?= $urlweb; ?>/assets/styles/bootstrap4/bootstrap.min.css">
    <link href="<?= $urlweb; ?>/assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?= $urlweb; ?>/assets/styles/cart_styles.css">
    <link rel="stylesheet" type="text/css" href="<?= $urlweb; ?>/assets/styles/cart_responsive.css">
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
                            <a href="."><?= $s0['nama_toko']; ?></a>
                        </div>
                        <!-- Start Navbar -->
                        <?php include 'home/component/navbar.php'; ?>
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
        <div class="row">
            <div class="col-md-8">
                <!-- Alamat Pengiriman -->
                <div class="card mb-3">
                    <div class="card-header">
                        Alamat Pengiriman
                    </div>
                    <div class="card-body">
                        <div id="alamat-pengiriman">
                            <?php if (!empty($addresses)): ?>
                                <div id="current-address">
                                    <strong><?= $addresses[0]['label'] ?> • <?= $addresses[0]['nama_penerima'] ?></strong><br>
                                    <?= $addresses[0]['alamat'] ?><br>
                                    <?= $addresses[0]['kota'] ?>, <?= $addresses[0]['provinsi'] ?><br>
                                    <?= $addresses[0]['kode_pos'] ?><br>
                                    <?= $addresses[0]['nohp_penerima'] ?><br>
                                </div>
                                <button type="button" class="btn btn-secondary mt-3" data-toggle="modal" data-target="#addressModal">Ganti Alamat</button>
                            <?php else: ?>
                                <p>Alamat tidak ditemukan.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Daftar Produk di Keranjang -->
                <div class="card">
                    <div class="card-header">
                        Keranjang Belanja
                    </div>
                    <div class="card-body">
                        <?php
                        $cart_query = pg_query($conn, "SELECT * FROM tb_keranjang WHERE user_id = '$user_id'");
                        while ($item = pg_fetch_assoc($cart_query)) {
                            $product_query = pg_query($conn, "SELECT * FROM tb_produk WHERE id_produk = '".pg_escape_string($item['product_id'])."'");
                            $product = pg_fetch_assoc($product_query);
                            $product_image_query = pg_query($conn, "SELECT * FROM tb_produk_gambar WHERE id_produk = '".pg_escape_string($item['product_id'])."' LIMIT 1");
                            $product_image = pg_fetch_assoc($product_image_query)['gambar'];
                            $total_price += $product['harga'] * $item['quantity']; // Menghitung total harga
                        ?>
                        <div class="row border-top pt-3 pb-3">
                            <div class="col-3"><img class="img-fluid" src="data:image/jpeg;base64,<?= $product_image ?>"></div>
                            <div class="col-6">
                                <h5><?= $product['nama_produk'] ?></h5>
                                <p>Putih, FAN REVERSE</p>
                            </div>
                            <div class="col-3 text-right">
                                <span class="price">Rp <?= number_format($product['harga'], 0, ',', '.') ?> x <?= $item['quantity'] ?></span>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Ringkasan Belanja -->
                <div class="card">
                    <div class="card-header">
                        Ringkasan Belanja
                    </div>
                    <div class="card-body">
                        <!-- Voucher Input -->
                        <div class="form-group">
                            <label for="voucher">Masukkan Voucher</label>
                            <input type="text" class="form-control" id="voucher" placeholder="Masukkan kode voucher">
                        </div>
                        <!-- Shipping Method Selection -->
                        <div class="form-group">
                            <label for="shipping-method">Pilih Pengiriman</label>
                            <select class="form-control" id="shipping-method">
                                <?php foreach ($shipping_methods as $method): ?>
                                    <option value="<?= $method['id_kurir'] ?>"><?= $method['nama_kurir'] ?> - Rp<?= number_format($method['harga'], 0, ',', '.') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <h5 class="card-title">Total Harga (<?= $cart_count ?> Barang)</h5>
                        <p class="card-text">Rp <?= number_format($total_price, 0, ',', '.') ?></p>
                        <button class="btn btn-success btn-block">Pilih Pembayaran</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Address Selection Modal -->
    <div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addressModalLabel">Daftar Alamat</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="list-group">
                        <?php foreach ($addresses as $address): ?>
                            <a href="#" class="list-group-item list-group-item-action" onclick="selectAddress(<?= htmlspecialchars(json_encode($address)) ?>)">
                                <div><b><?= $address['label'] ?></b></div>
                                <div><?= $address['alamat'] ?></div>
                                <div><?= $address['kota'] ?>, <?= $address['provinsi'] ?>, <?= $address['kode_pos'] ?></div>
                                <div><?= $address['nama_penerima'] ?>, <?= $address['nohp_penerima'] ?></div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Start Footer -->
    <?php include 'home/component/footer.php'; ?>
    <!-- End Footer -->
</div>

<script src="<?= $urlweb; ?>/assets/js/jquery-3.2.1.min.js"></script>
<script src="<?= $urlweb; ?>/assets/styles/bootstrap4/popper.js"></script>
<script src="<?= $urlweb; ?>/assets/styles/bootstrap4/bootstrap.min.js"></script>
<script src="<?= $urlweb; ?>/assets/plugins/Isotope/isotope.pkgd.min.js"></script>
<script src="<?= $urlweb; ?>/assets/plugins/OwlCarousel2-2.2.1/owl.carousel.js"></script>
<script src="<?= $urlweb; ?>/assets/plugins/easing/easing.js"></script>
<script src="<?= $urlweb; ?>/assets/js/single_custom.js"></script>
<script>
function selectAddress(address) {
    $('#current-address').html(`
        <strong>${address.label} • ${address.nama_penerima}</strong><br>
        ${address.alamat}<br>
        ${address.kota}, ${address.provinsi}<br>
        ${address.kode_pos}<br>
        ${address.nohp_penerima}<br>
    `);
    $('#addressModal').modal('hide');
}
</script>
</body>
</html>
