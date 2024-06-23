<?php
function loadPage($page) {
        $file = __DIR__ . '/' . $page . '.php';
        // echo $file . "<br>"; // Debugging
        if (file_exists($file)) {
            // echo "Loading file: " . $file . "<br>"; // Debugging
            include $file;
        } else {
            // echo "File not found: " . $file . "<br>"; // Debugging
            include '404.php';
        }
}

// Get the requested URL
$request = $_SERVER['REQUEST_URI'];

// Remove leading slash and any query string
$request = trim(parse_url($request, PHP_URL_PATH), '/');
$request = preg_replace('/^telkomgoods\//', '', $request);

// Debugging output
// echo "Requested URL: " . $request . "<br>";

// Route the request
switch ($request) {
    case '':
        loadPage('index');
        break;
    case 'detail':
        loadPage('detail_produk');
        break;
    case 'keranjang':
        loadPage('system/user/cart/cart');
        break;
    case 'pengiriman':
        loadPage('system/user/shipping/shipping');
        break;
    case 'registrasi':
        loadPage('home/register_proses/register');
        break;
    case 'otpreg':
        loadPage('home/register_proses/confirm');
        break;
    case 'login':
        loadPage('home/login_proses/login');
        break;
    case 'otp':
        loadPage('home/login_proses/confirm');
        break;
    case 'forgot':
        loadPage('home/forgot_password_proses/index');
        break;
    case 'otpfp':
        loadPage('home/forgot_password_proses/confirm');
        break;
    case 'respw':
        loadPage('home/forgot_password_proses/password');
        break;
    case 'dashboard':
        loadPage('dashboard');
        break;
    case 'profile':
        loadPage('profile');
        break;
    case 'admin':
        loadPage('system/admin/index');
        break;
    case 'admin/setting':
        loadPage('system/admin/mod_setting/settings');
        break;
    case 'admin/myproduk':
        loadPage('system/admin/mod_produk/allproduk');
        break;
    case 'admin/addproduk':
        loadPage('system/admin/mod_produk/addproduk');
        break;
    case 'admin/editproduk':
        loadPage('system/admin/mod_produk/editproduk');
        break;
    case 'admin/addkategori':
        loadPage('system/admin/mod_produk/addkategori');
        break;
    case 'admin/userdata':
        loadPage('system/admin/mod_user/user');
        break;
    case 'admin/akun':
        loadPage('system/admin/mod_akun/akun');
        break;
    case 'admin/password':
        loadPage('system/admin/mod_akun/password');
        break;
    case 'admin/bannerpromo':
        loadPage('system/admin/mod_banner_promo/banner_promo');
        break;
    case 'user':
        loadPage('system/user/index');
        break;
    case 'user/akun':
        loadPage('system/user/mod_akun/akun');
        break;
    case 'user/password':
        loadPage('system/user/mod_akun/password');
        break;
    case 'user/alamat':
        loadPage('system/user/mod_alamat/alamat');
        break;
    case 'session':
        loadPage('system/login_proses/php_session');
        break;
    default:
        loadPage('404'); // Tampilkan 404 jika halaman tidak ada
        break;
}
?>
