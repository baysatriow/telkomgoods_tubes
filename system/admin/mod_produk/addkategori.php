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
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Produk /</span> Kategori</h4>

                <div class="row">
                    <div class="col-md-12">
                 <!-- Menu Tambahan -->
                  <ul class="nav nav-pills flex-column flex-md-row mb-3">
                    <li class="nav-item">
                      <a class="nav-link" href="myproduk"><i class="bx bx-user me-1"></i> Data Produk</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="addproduk"
                        ><i class="bx bx-link-alt me-1"></i>Tambah Produk</a
                      >
                    </li>
                    <li class="nav-item">
                      <a class="nav-link active" href="javascript:void(0);"
                        ><i class="bx bx-link-alt me-1"></i>Kategori</a
                      >
                    </li>
                  </ul>
                        <div class="card mb-4">
                        <h5 class="card-header">Tambah Kategori</h5>
                        <!-- Kategori -->
                        <hr class="my-0" />
                        <div class="card-body">
                            <?php
                            error_reporting(0);
                            if (!empty($_GET['notif'])) {
                                echo '<script>';
                                if ($_GET['notif'] == 1) {
                                    echo "
                                        iziToast.error({
                                            title: 'Perhatian!',
                                            message: 'Kategori Berhasil di Hapus!',
                                            position: 'topRight'
                                        });
                                    ";
                                }
                                if ($_GET['notif'] == 2) {
                                    echo "
                                        iziToast.success({
                                            title: 'Perhatian!',
                                            message: 'Kategori Berhasil di Ubah!',
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
                                            message: 'Gagal Tambah Kategori!',
                                            position: 'topRight'
                                        });
                                    ";
                                }
                                if ($_GET['notif'] == 5) {
                                    echo "
                                        iziToast.success({
                                            title: 'Well done!',
                                            message: 'Kategori Berhasil Di Tambahkan!',
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
                            <form id="formAddKategori" method="POST" action="http://localhost/go-app/addkategori">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                <label for="nama_kategori" class="form-label">Nama Kategori</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    id="nama_kategori"
                                    name="nama_kategori"
                                    placeholder="Masukkan Nama Kategori"
                                    autofocus
                                />
                                </div>
                                <div class="mb-3 col-md-6">
                                <label for="exampleFormControlSelect1" class="form-label">Status Kategori</label>
                                <select name="status" class="form-select" id="exampleFormControlSelect1" aria-label="Default select example">
                                <option selected value="1">Pilih Status</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                                </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                <label for="exampleFormControlSelect1" class="form-label">Status Kategori</label>
                                <select name="terbaru" class="form-select" id="exampleFormControlSelect1" aria-label="Default select example">
                                <option selected value="1">Tmapilkan Terbaru?</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                                </select>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Save changes</button>
                                <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                            </div>
                            </form>
                        </div>
                        <!-- Tambah Kategori -->
                        </div>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">List Kategori</h5>
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <?php
                                    $query = pg_query($conn, "SELECT * FROM tb_kategori_produk");
                                    $no = 0;
                                    while ($kategori = pg_fetch_array($query)) {
                                        $no++;
                                ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><strong><?= $kategori['nama_kategori']?></strong></td>
                                    <td>
                                        <?php 
                                        if ($kategori['status'] == 1) {
                                            echo '<span class="badge bg-label-primary me-1">Aktif</span>';
                                        } else if ($kategori['status'] == 0) {
                                            echo '<span class="badge bg-label-danger me-1">Tidak Aktif</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <button
                                                type="button"
                                                class="btn btn-primary me-2"
                                                data-bs-toggle="modal"
                                                data-bs-target="#edit<?= $kategori['id_kategori']?>"
                                            >
                                                <i class="bx bx-edit-alt"></i> Edit
                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade" id="edit<?= $kategori['id_kategori']?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <form id="formEditKategori" method="POST" action="http://localhost/go-app/editkategori">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Kategori</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <input type="hidden" id="id_kategori" name="id_kategori" value="<?= $kategori['id_kategori']; ?>" />
                                                                    <label for="nama_kategori" class="form-label">Nama Kategori</label>
                                                                    <input
                                                                        type="text"
                                                                        id="nama_kategori"
                                                                        name="nama_kategori"
                                                                        class="form-control"
                                                                        placeholder="Masukkan Nama Kategori"
                                                                        value="<?= $kategori['nama_kategori']; ?>"
                                                                        autofocus
                                                                    />
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="status_kategori" class="form-label">Status Kategori</label>
                                                                    <select name="status" id="status_kategori" class="form-select">
                                                                        <option selected value="<?= $kategori['status']; ?>">
                                                                            <?= $kategori['status'] == 0 ? "Dipilih (Tidak Aktif)" : "Dipilih (Aktif)" ?>
                                                                        </option>
                                                                        <option value="1">Aktif</option>
                                                                        <option value="0">Tidak Aktif</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="status_kategori" class="form-label">Status Terbaru</label>
                                                                    <select name="terbaru" id="status_kategori" class="form-select">
                                                                        <option selected value="<?= $kategori['terbaru']; ?>">
                                                                            <?= $kategori['terbaru'] == 0 ? "Dipilih (Tidak Aktif)" : "Dipilih (Aktif)" ?>
                                                                        </option>
                                                                        <option value="1">Aktif</option>
                                                                        <option value="0">Tidak Aktif</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <a class="btn btn-danger" href="http://localhost/go-app/delkategori?id=<?= $kategori['id_kategori'] ?>">
                                                <i class="bx bx-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
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
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/js/bootstrap.js"></script>

    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="<?php echo $urlweb; ?>/assets/admin/js/main.js"></script>

    <!-- Page JS -->
    <script src="<?php echo $urlweb; ?>/assets/admin/js/pages-account-settings-account.js"></script>
    <!-- Page JS -->
    <!-- <script src="<?php echo $urlweb; ?>/assets/admin/js/ui-modals.js"></script> -->
    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
