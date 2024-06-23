<?php
ob_start();
session_start();
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
$ip = $_SERVER['REMOTE_ADDR'];
$date = date('Y-m-d');
?>
<!DOCTYPE html>
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
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700&display=swap"
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
  <script src="<?php echo $urlweb; ?>/assets/plugins/izitoast/js/iziToast.min.js"></script>

  <!-- Helpers -->
  <script src="<?php echo $urlweb; ?>/assets/admin/vendor/js/helpers.js"></script>
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
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Promosi /</span> Tambah Promo</h4>

            <div class="row">
              <div class="col-xxl">
                <div class="card mb-4">
                  <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Informasi Promo</h5>
                  </div>
                  <div class="card-body">
                    <?php
                    error_reporting(0);
                    if (!empty($_GET['notif'])) {
                        echo '<script>';
                        if ($_GET['notif'] == 1) {
                            echo "
                                iziToast.warning({
                                    title: 'Perhatian!',
                                    message: 'Nama Promo Wajib Diisi!',
                                    position: 'topRight'
                                });
                            ";
                        }
                        if ($_GET['notif'] == 2) {
                            echo "
                                iziToast.warning({
                                    title: 'Perhatian!',
                                    message: 'Gagal Update Data!',
                                    position: 'topRight'
                                });
                            ";
                        }
                        if ($_GET['notif'] == 3) {
                            echo "
                                iziToast.warning({
                                    title: 'Perhatian!',
                                    message: 'Gagal Update Gambar!',
                                    position: 'topRight'
                                });
                            ";
                        }
                        if ($_GET['notif'] == 4) {
                            echo "
                                iziToast.success({
                                    title: 'Well done!',
                                    message: 'Promo Berhasil di Tambahkan!',
                                    position: 'topRight'
                                });
                            ";
                        }
                        if ($_GET['notif'] == 5) {
                            echo "
                                iziToast.warning({
                                    title: 'Perhatian!',
                                    message: 'Terjadi Kesalahan!',
                                    position: 'topRight'
                                });
                            ";
                        }
                        if ($_GET['notif'] == 6) {
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
                    <form id="formAddPromo" method="POST" action="http://localhost/go-app/addbannerpromo" enctype="multipart/form-data">
                      <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="nama_promo">Nama Promo</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="nama_promo" name="nama_promo" placeholder="Masukkan Nama Promo" />
                        </div>
                      </div>
                      <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="harga">Harga</label>
                        <div class="col-sm-10">
                          <input type="number" class="form-control" id="harga" name="harga" placeholder="Masukkan Harga Sebelum Diskon" />
                        </div>
                      </div>
                      <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="diskon">Diskon</label>
                        <div class="col-sm-10">
                          <input type="number" class="form-control" id="diskon" name="diskon" placeholder="Masukkan Besaran Diskon" />
                        </div>
                      </div>
                      <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="tgl_selesai">Tanggal Selesai</label>
                        <div class="col-sm-10">
                          <input type="date" class="form-control" id="tgl_selesai" name="tgl_selesai" />
                        </div>
                      </div>
                      <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="status">Status</label>
                        <div class="col-sm-10">
                          <select name="status" class="form-select" id="status">
                            <option selected value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                          </select>
                        </div>
                      </div>
                      <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="image">Gambar Promo</label>
                        <div class="col-sm-10">
                          <input type="file" class="form-control" id="image" name="image" required />
                        </div>
                      </div>
                      <div class="row justify-content-end">
                        <div class="col-sm-10">
                          <button type="submit" class="btn btn-primary">Tambah Promo</button>
                        </div>
                      </div>
                    </form>
                  </div>
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
  <script src="<?php echo $urlweb; ?>/assets/admin/js/pages-account-settings-account.js"></script>

  <!-- Place this tag in your head or just before your close body tag. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
</html>
