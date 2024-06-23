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
$s1 = pg_fetch_array($result);
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

    <title>Pengaturan Aplikasi! - <?php echo $s0['nama_toko']; ?> </title>

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
              <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pengaturan /</span> Pengaturan Aplikasi!</h4>

              <div class="row">
                <div class="col-md-12">
                <div class="card mb-4">
                <h5 class="card-header">Detail Toko</h5>
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
                                message: 'Gagal Update Gambar!',
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
                  <div class="row">
                  </div>
                </div>
                <hr class="my-0" />
                <div class="card-body">
                  <form id="formAccountSettings" method="POST" action="http://localhost/go-app/updatesetting" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Logo & Favicon -->
                        <div class="mb-3 col-lg-6">
                          <label for="state" class="form-label">Logo Toko</label>
                          <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <img src="data:image/png;base64,<?php echo $s0['logo']?>" alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedLogo"/>
                            <div class="button-wrapper">
                              <label for="uploadLogo" class="btn btn-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block">Upload Logo Baru</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" id="uploadLogo" name="logo" class="logo-image-input" hidden accept="image/png, image/jpeg"/>
                              </label>
                              <button type="button" class="btn btn-outline-secondary logo-image-reset mb-4">
                                <i class="bx bx-reset d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Reset</span>
                              </button>
                              <p class="text-muted mb-0">Format di Terima JPG, GIF atau PNG. Maksimal 5MB !</p>
                            </div>
                          </div>
                        </div>

                        <div class="mb-3 col-lg-6">
                          <label for="state" class="form-label">Favicon Toko</label>
                          <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <img src="data:image/png;base64,<?php echo $s0['favicon']?>" alt="user-favicon" class="d-block rounded" height="100" width="100" id="uploadedFavicon"/>
                            <div class="button-wrapper">
                              <label for="uploadFavicon" class="btn btn-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block">Upload Favicon Baru</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" id="uploadFavicon" name="favicon" class="favicon-image-input" hidden accept="image/png, image/jpeg"/>
                              </label>
                              <button type="button" class="btn btn-outline-secondary favicon-image-reset mb-4">
                                <i class="bx bx-reset d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Reset</span>
                              </button>
                              <p class="text-muted mb-0">Format di Terima JPG, GIF atau PNG. Maksimal 5MB !</p>
                            </div>
                          </div>
                        </div>  
                        <!-- End -->
                      <div class="mb-3 col-md-6">
                        <label for="nama_toko" class="form-label">Nama Toko</label>
                        <input class="form-control" type="text" id="nama_toko" name="nama_toko" value="<?php echo $s0['nama_toko']; ?>" autofocus/>
                      </div>
                      <div class="mb-3 col-md-6">
                        <label for="nama_pemilik" class="form-label">Nama Pemilik</label>
                        <input class="form-control" type="text" name="nama_pemilik" id="nama_pemilik" value="<?php echo $s0['nama_pemilik']; ?>"/>
                      </div>
                      <div class="mb-3 col-md-6">
                        <label for="email" class="form-label">E-mail Toko</label>
                        <input class="form-control" type="text" id="email" name="email" value="<?php echo $s0['email']; ?>" placeholder="Masukkan Email!"/>
                      </div>
                      <div class="mb-3 col-md-6">
                        <?php
                        $phoneNumber = $s0['no_telp'];
                        if (substr($phoneNumber, 0, 1) === '0') {
                            $phoneNumber = substr($phoneNumber, 1);
                        }
                        ?>
                        <label class="form-label" for="no_telp">Phone Number</label>
                        <div class="input-group input-group-merge">
                          <span class="input-group-text">ID (+62)</span>
                          <input type="text" id="no_telp" name="no_telp" class="form-control" value="<?php echo $phoneNumber; ?>" placeholder="813 7775 4080"/>
                        </div>
                      </div>
                      <div class="mb-3 col-md-6">
                        <label for="alamat" class="form-label">Alamat</label>
                        <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Address" value="<?php echo $s0['alamat']; ?>"/>
                      </div>
                      <div class="mb-3 col-md-6">
                        <label for="kota" class="form-label">Kota</label>
                        <input class="form-control" type="text" id="kota" name="kota" placeholder="Jakarat" value="<?php echo $s0['kota']; ?>"/>
                      </div>
                      <div class="mb-3 col-md-6">
                        <label for="provinsi" class="form-label">Provinsi</label>
                        <input class="form-control" type="text" id="provinsi" name="provinsi" placeholder="DKI Jakarta" value="<?php echo $s0['provinsi']; ?>"/>
                      </div>
                      <div class="mb-3 col-md-6">
                        <label for="zipCode" class="form-label">Kode Pos</label>
                        <input type="text" class="form-control" id="zipCode" name="zipCode" placeholder="231465" maxlength="6"/>
                      </div>
                      <div class="mb-3 col-md-6">
                        <label for="api_wa" class="form-label">API KEY WA</label>
                        <input class="form-control" type="text" id="api_wa" name="api_wa" placeholder="Ambil Dari Provider!" value="<?php echo $s0['api_wa']; ?>"/>
                      </div>
                      <div class="mb-3 col-md-6">
                        <label for="no_rek" class="form-label">NO REKENING</label>
                        <input class="form-control" type="text" id="no_rek" name="no_rek" placeholder="5775 xxxx" value="<?php echo $s0['no_rek']; ?>"/>
                      </div>
                      <div class="mb-3 col-md-6">
                        <label for="nama_bank" class="form-label">NAMA BANK</label>
                        <input class="form-control" type="text" id="nama_bank" name="nama_bank" placeholder="BRI" value="<?php echo $s0['nama_bank']; ?>"/>
                      </div>
                      <div class="mb-3 col-md-6">
                        <label for="nama_rek" class="form-label">NAMA REKENING</label>
                        <input class="form-control" type="text" id="nama_rek" name="nama_rek" placeholder="Bayu Satrio Wibowo" value="<?php echo $s0['nama_rek']; ?>"/>
                      </div>
                    </div>
                    <div class="mt-2">
                      <button type="submit" class="btn btn-primary me-2">Simpan</button>
                      <button type="reset" class="btn btn-outline-secondary">Batal</button>
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
              <?php include 'system/admin/component/footer.php'?>
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
      <script src="../assets/admin/vendor/libs/jquery/jquery.js"></script>
      <script src="../assets/admin/vendor/libs/popper/popper.js"></script>
      <script src="../assets/admin/vendor/js/bootstrap.js"></script>
      <script src="../assets/admin/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

      <script src="../assets/admin/vendor/js/menu.js"></script>
      <!-- endbuild -->

      <!-- Vendors JS -->
      <script src="../assets/admin/vendor/libs/apex-charts/apexcharts.js"></script>

      <!-- Main JS -->
      <script src="../assets/admin/js/main.js"></script>

      <!-- Page JS -->
      <script src="<?php echo $urlweb; ?>/assets/admin/js/image_replace.js"></script>

      <!-- Place this tag in your head or just before your close body tag. -->
      <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
