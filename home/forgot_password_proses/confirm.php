<?php
ob_start();
session_start();
// if (session_status() === PHP_SESSION_ACTIVE) {
//   echo "Sesi sedang aktif.";
// } else {
//   echo "Tidak ada sesi yang aktif.";
// }
require('config/database.php');
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
// $sql_1a = pg_query($conn,"SELECT * FROM `tb_social` WHERE user = '$pengguna'") or die(mysqli_error());
// $s1a = mysqli_fetch_array($sql_1a);
// $sql_1b = pg_query($conn,"SELECT * FROM `tb_user` WHERE user = '$pengguna'") or die(mysqli_error());
// $s1b = mysqli_fetch_array($sql_1b);
$ip = $_SERVER['REMOTE_ADDR'];
$date = date('Y-m-d');

// $stat = pg_query($conn,"INSERT INTO `tb_stat` (`ip`, `date`, `hits`, `page`, `user`) VALUES ('$ip', '$date', 1, 'Masuk Akun', '$pengguna')") or die (pg_last_error());
?>
<!DOCTYPE html>


<!-- beautify ignore:start -->
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="<?php echo $urlweb; ?>/assets/admin/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Konfirmasi Akun | <?php echo $s0['nama_toko']; ?></title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $urlweb; ?>/assets/admin/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="<?php echo $urlweb; ?>/assets/admin/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo $urlweb; ?>/assets/admin/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?php echo $urlweb; ?>/assets/admin/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?php echo $urlweb; ?>/assets/admin/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?php echo $urlweb; ?>/assets/admin/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="<?php echo $urlweb; ?>/assets/admin/vendor/css/pages/page-auth.css" />

    <link rel="stylesheet" href="<?php echo $urlweb; ?>/assets/plugins/izitoast/css/iziToast.min.css">
    <!-- izitoast -->
    <!-- JS Libraies -->
    <script src="<?php echo $urlweb; ?>/assets/plugins/izitoast/js/iziToast.min.js"></script>
    <!-- Helpers -->
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?php echo $urlweb; ?>/assets/admin/js/config.js"></script>
  </head>

  <body>
    <!-- Content -->

    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
          <!-- Forgot Password -->
          <div class="card">
            <div class="card-body">
            <?php
              error_reporting(0);
              if (!empty($_GET['notif'])) {
                  echo '<script>';
                  if ($_GET['notif'] == 1) {
                      echo "
                          iziToast.warning({
                              title: 'Perhatian!',
                              message: 'Username dan Password Wajib Diisi!',
                              position: 'topRight'
                          });
                      ";
                  }
                  if ($_GET['notif'] == 2) {
                      echo "
                          iziToast.warning({
                              title: 'Perhatian!',
                              message: 'Username Wajib Diisi!',
                              position: 'topRight'
                          });
                      ";
                  }
                  if ($_GET['notif'] == 3) {
                      echo "
                          iziToast.warning({
                              title: 'Perhatian!',
                              message: 'Kode OTP Salah!',
                              position: 'topRight'
                          });
                      ";
                  }
                  if ($_GET['notif'] == 4) {
                      echo "
                          iziToast.warning({
                              title: 'Perhatian!',
                              message: 'Username atau Password Salah!',
                              position: 'topRight'
                          });
                      ";
                  }
                  if ($_GET['notif'] == 5) {
                      echo "
                          iziToast.success({
                              title: 'Well done!',
                              message: 'Akun berhasil di verifikasi!',
                              position: 'topRight'
                          });
                      ";
                  }
                  if ($_GET['notif'] == 6) {
                      echo "
                          iziToast.warning({
                              title: 'Perhatian!',
                              message: 'Terjadi Kesalahan!',
                              position: 'topRight'
                          });
                      ";
                  }
                  echo '</script>';
              }
              ?>
              <!-- Logo -->
              <div class="app-brand justify-content-center">
                <a href="index.html" class="app-brand-link gap-2">
                  <span class="app-brand-logo demo">
                  <img src="data:image/png;base64,<?php echo $s0['logo']?>" alt="user-avatar" class="d-block rounded" height="50" width="50" style="margin-left: -10px;"/>
                
                  </span>
                  <span class="app-brand-text demo text-body fw-bolder"><?php echo $s0['nama_toko']?></span>
                </a>
              </div>
              <!-- /Logo -->
              <h4 class="mb-2">Verifikasi Akun Anda 🔒</h4>
              <p class="mb-4">Masukkan Kode OTP Yang Telah dikirimkan ke WhatsApp anda!</p>
              <form id="formAuthentication" class="mb-3" action="http://localhost/go-app/confirmotpfp" method="POST">
                <div class="mb-3">
                  <input
                    type="hidden"
                    class="form-control"
                    id="otp"
                    name="token"
                    placeholder="Masukkan OTP!"
                    autofocus
                    value="<?php echo ($_GET['token']) ?>"
                  />
                  <label for="otp" class="form-label">OTP</label>
                  <input
                    type="text"
                    class="form-control"
                    id="otp"
                    name="otp"
                    placeholder="Masukkan OTP!"
                    autofocus
                  />
                </div>
                <button class="btn btn-primary d-grid w-100">Kirim!</button>
              </form>
              <div class="text-center">
              <a href="http://localhost/go-app/resendotpfp?token=<?php echo $_GET['token']; ?>" class="btn btn-danger btn-block">Kirim Ulang OTP</a>
                <!-- <a href="login.php" class="d-flex align-items-center justify-content-center">
                  <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                 Kembali ke Login!
                </a> -->
              </div>
            </div>
          </div>
          <!-- /Forgot Password -->
        </div>
      </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js --> 
    <script src="<?php echo $urlweb; ?>/assets/vendor/js/core.js"></script>
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/libs/jquery/jquery.js"></script>
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/libs/popper/popper.js"></script>
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/js/bootstrap.js"></script>
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="<?php echo $urlweb; ?>/assets/admin/js/main.js"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
