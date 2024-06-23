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
  class="light-style layout-menu-fixed"
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

    <title>Pengaturan Akun! - <?php echo $s0['nama_toko']; ?> </title>

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
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <?php include 'system/admin/component/sidebar.php'?>

        <!-- Layout container -->
        <div class="layout-page">

        <?php include 'system/admin/component/topbar.php'?>

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Akun Saya /</span> Akun</h4>
              <div class="row">
                <div class="col-md-12">
                  <!-- Menu Tambahan -->
                <ul class="nav nav-pills flex-column flex-md-row mb-3">
                    <li class="nav-item">
                      <a class="nav-link active" href="javascript:void(0);"><i class="bx bx-user me-1"></i> Akun</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="password"
                        ><i class="bx bx-link-alt me-1"></i>Password</a
                      >
                    </li>
                  </ul>
                  <div class="card mb-4">
                    <h5 class="card-header">Profile Details</h5>
                    <!-- Account -->
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
                                    message: 'Gagal Update Data!',
                                    position: 'topRight'
                                });
                            ";
                        }
                        if ($_GET['notif'] == 4) {
                            echo "
                                iziToast.warning({
                                    title: 'Perhatian!',
                                    message: 'Gagal Upload Gambar!',
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
                        if ($_GET['notif'] == 7) {
                          echo "
                              iziToast.warning({
                                  title: 'Perhatian!',
                                  message: 'Ukuran Gambar Terlalu Besar!',
                                  position: 'topRight'
                              });
                          ";
                      }
                        echo '</script>';
                    }
                    ?>
                    <div class="card-body">
                    <form id="formAccountSettings" method="POST" action="http://localhost/go-app/updateakun" enctype="multipart/form-data">
                      <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img
                          src="data:image/png;base64,<?php echo $s1['image']; ?>"
                          alt="user-avatar"
                          class="d-block rounded mb-4"
                          height="100"
                          width="100"
                          id="uploadedAkun"
                        />
                        <div class="button-wrapper">
                          <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                            <span class="d-none d-sm-block">Upload Foto Baru</span>
                            <i class="bx bx-upload d-block d-sm-none"></i>
                            <input
                              type="file"
                              id="upload"
                              class="akun-file-input"
                              hidden
                              accept="image/png, image/jpeg"
                              name="image"
                            />
                          </label>
                          <button type="button" class="btn btn-outline-secondary akun-image-reset mb-4">
                            <i class="bx bx-reset d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Reset</span>
                          </button>
                          <p class="text-muted mb-0">Format di Terima JPG, GIF atau PNG. Maksimal 2MB !</p>
                        </div>
                      </div>
                      <hr class="my-0" />
                      <div class="row">
                        <!-- Get id_user -->
                        <input
                          class="form-control"
                          type="hidden"
                          id="id_user"
                          name="id_user"
                          value="<?php echo $s1['id_user']; ?>"
                        />
                        <!-- Tanggal update -->
                        <input
                          class="form-control"
                          type="hidden"
                          id="update_at"
                          name="update_at"
                          value="<?php echo date('Y-m-d H:i:s'); ?>"
                        />
                        <div class="mb-3 col-md-6">
                          <label for="username" class="form-label">Username</label>
                          <input
                            class="form-control"
                            type="text"
                            id="username"
                            name="username"
                            value="<?php echo $s1['username']; ?>"
                            autofocus
                            disabled
                          />
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="name" class="form-label">Nama Lengkap</label>
                          <input
                            class="form-control"
                            type="text"
                            id="name"
                            name="name"
                            value="<?php echo $s1['name']; ?>"
                            autofocus
                          />
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="email" class="form-label">E-mail</label>
                          <input
                            class="form-control"
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo $s1['email']; ?>"
                            placeholder="baysatriow@gmail.com"
                          />
                        </div>
                        <div class="mb-3 col-md-6">
                        <?php
                        $phoneNumber = $s1['nohp'];
                        if (substr($phoneNumber, 0, 1) === '0') {
                            $phoneNumber = substr($phoneNumber, 1);
                        }
                        ?>
                          <label class="form-label" for="phoneNumber">Phone Number</label>
                          <div class="input-group input-group-merge">
                            <span class="input-group-text">ID (+62)</span>
                            <input
                              type="text"
                              id="phoneNumber"
                              name="nohp"
                              class="form-control"
                              placeholder="82111249430"
                              value="<?php echo $phoneNumber; ?>"
                            />
                          </div>
                        </div>
                      </div>
                      <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">Save changes</button>
                        <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                      </div>
                    </form>

                    </div>
                    <!-- /Account -->
                  </div>
                </div>
              </div>
            </div>
            <!-- / Content -->

            <!-- Footer -->
                <?php include 'system/admin/component/footer.php' ?>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
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
    <script src="<?php echo $urlweb; ?>/assets/admin/js/image_replace.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
