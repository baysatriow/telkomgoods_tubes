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

    <title>Konfirmasi Masuk | <?php echo $s0['nama_toko']; ?></title>

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
                    <!-- <svg
                      width="25"
                      viewBox="0 0 25 42"
                      version="1.1"
                      xmlns="http://www.w3.org/2000/svg"
                      xmlns:xlink="http://www.w3.org/1999/xlink"
                    >
                      <defs>
                        <path
                          d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z"
                          id="path-1"
                        ></path>
                        <path
                          d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z"
                          id="path-3"
                        ></path>
                        <path
                          d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z"
                          id="path-4"
                        ></path>
                        <path
                          d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z"
                          id="path-5"
                        ></path>
                      </defs>
                      <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                          <g id="Icon" transform="translate(27.000000, 15.000000)">
                            <g id="Mask" transform="translate(0.000000, 8.000000)">
                              <mask id="mask-2" fill="white">
                                <use xlink:href="#path-1"></use>
                              </mask>
                              <use fill="#EB1B21" xlink:href="#path-1"></use>
                              <g id="Path-3" mask="url(#mask-2)">
                                <use fill="#EB1B21" xlink:href="#path-3"></use>
                                <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                              </g>
                              <g id="Path-4" mask="url(#mask-2)">
                                <use fill="#EB1B21" xlink:href="#path-4"></use>
                                <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                              </g>
                            </g>
                            <g
                              id="Triangle"
                              transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) "
                            >
                              <use fill="#EB1B21" xlink:href="#path-5"></use>
                              <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                            </g>
                          </g>
                        </g>
                      </g>
                    </svg> -->
                  </span>
                  <span class="app-brand-text demo text-body fw-bolder"><?php echo $s0['nama_toko']?></span>
                </a>
              </div>
              <!-- /Logo -->
              <h4 class="mb-2">Verifikasi Akun Anda 🔒</h4>
              <p class="mb-4">Masukkan Kode OTP Yang Telah dikirimkan ke WhatsApp anda!</p>
              <form id="formAuthentication" class="mb-3" action="http://localhost/go-app/confirmotp" method="POST">
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
                    name="kode"
                    placeholder="Masukkan OTP!"
                    autofocus
                  />
                </div>
                <button class="btn btn-primary d-grid w-100">Konfirmasi!</button>
              </form>
              <div class="text-center">
              <a href="http://localhost/go-app/resendotp?token=<?php echo $_GET['token']; ?>" class="btn btn-danger btn-block">Kirim Ulang OTP</a>
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
