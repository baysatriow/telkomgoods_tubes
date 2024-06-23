<?php
ob_start();
session_start();
require('config/database.php');
$sid = session_id();
$id_user = 1; // Ganti dengan ID user yang sesuai
$result = pg_query($conn, "SELECT * FROM tb_settings WHERE id_setting = 1");
$result1 = pg_query($conn, "SELECT * FROM tb_user WHERE id_user = $id_user");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Pengaturan Akun! - <?php echo $s0['nama_toko']; ?> </title>
    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $urlweb; ?>/assets/admin/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700&display=swap" rel="stylesheet" />

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
    <script src="<?php echo $urlweb; ?>/assets/plugins/izitoast/js/iziToast.min.js"></script>

    <!-- Helpers -->
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/js/helpers.js"></script>
    <script src="<?php echo $urlweb; ?>/assets/admin/js/config.js"></script>
  </head>

  <body>
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <?php include 'system/user/component/sidebar.php'?>

        <div class="layout-page">
        <?php include 'system/user/component/topbar.php'?>

          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Akun /</span> Alamat</h4>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                        <h5 class="card-header">Daftar Alamat</h5>
                        <hr class="my-0" />
                        <div class="card-body">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahAlamat">
                                + Tambah Alamat Baru
                            </button>
                            <hr>

                            <div class="list-group">
                                <?php
                                    $query = pg_query($conn, "SELECT * FROM tb_alamat WHERE id_user = $id_user");
                                    while ($alamat = pg_fetch_array($query)) {
                                ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?= $alamat['label'] ?> <span class="badge bg-primary">Utama</span></h5>
                                        <small>
                                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detail<?= $alamat['id_alamat']?>">Detail</button>
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?= $alamat['id_alamat']?>">Ubah Alamat</button>
                                            <a href="http://localhost/go-app/delalamat?id=<?= $alamat['id_alamat'] ?>" class="btn btn-danger btn-sm">Hapus</a>
                                        </small>
                                    </div>
                                    <p class="mb-1"><?= $alamat['alamat'] ?>, <?= $alamat['kota'] ?>, <?= $alamat['provinsi'] ?>, <?= $alamat['kode_pos'] ?></p>
                                    <small><?= $alamat['nama_penerima'] ?> - <?= $alamat['nohp_penerima'] ?></small>
                                </div>

                                <!-- Modal Detail Alamat -->
                                <div class="modal fade" id="detail<?= $alamat['id_alamat']?>" tabindex="-1" aria-labelledby="detailLabel<?= $alamat['id_alamat']?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="detailLabel<?= $alamat['id_alamat']?>">Detail Alamat</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Label:</strong> <?= $alamat['label'] ?></p>
                                                <p><strong>Alamat:</strong> <?= $alamat['alamat'] ?></p>
                                                <p><strong>Kota:</strong> <?= $alamat['kota'] ?></p>
                                                <p><strong>Provinsi:</strong> <?= $alamat['provinsi'] ?></p>
                                                <p><strong>Kode Pos:</strong> <?= $alamat['kode_pos'] ?></p>
                                                <p><strong>Nama Penerima:</strong> <?= $alamat['nama_penerima'] ?></p>
                                                <p><strong>No Hp:</strong> <?= $alamat['nohp_penerima'] ?></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Edit Alamat -->
                                <div class="modal fade" id="edit<?= $alamat['id_alamat']?>" tabindex="-1" aria-labelledby="editLabel<?= $alamat['id_alamat']?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form id="formEditAlamat" method="POST" action="http://localhost/go-app/editalamat">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editLabel<?= $alamat['id_alamat']?>">Ubah Alamat</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <input type="hidden" id="id_alamat" name="id_alamat" value="<?= $alamat['id_alamat']; ?>" />
                                                        <label for="label" class="form-label">Label</label>
                                                        <input type="text" class="form-control" id="label" name="label" placeholder="Masukkan Label" value="<?= $alamat['label']; ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="alamat" class="form-label">Alamat</label>
                                                        <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Masukkan Alamat" value="<?= $alamat['alamat']; ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="kota" class="form-label">Kota</label>
                                                        <input type="text" class="form-control" id="kota" name="kota" placeholder="Masukkan Kota" value="<?= $alamat['kota']; ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="provinsi" class="form-label">Provinsi</label>
                                                        <input type="text" class="form-control" id="provinsi" name="provinsi" placeholder="Masukkan Provinsi" value="<?= $alamat['provinsi']; ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="kode_pos" class="form-label">Kode Pos</label>
                                                        <input type="text" class="form-control" id="kode_pos" name="kode_pos" placeholder="Masukkan Kode Pos" value="<?= $alamat['kode_pos']; ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="nama_penerima" class="form-label">Nama Penerima</label>
                                                        <input type="text" class="form-control" id="nama_penerima" name="nama_penerima" placeholder="Masukkan Nama Penerima" value="<?= $alamat['nama_penerima']; ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="no_hp" class="form-label">No Hp</label>
                                                        <input type="text" class="form-control" id="no_hp" name="nohp_penerima" placeholder="Masukkan No Hp" value="<?= $alamat['nohp_penerima']; ?>">
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
                                <?php } ?>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Tambah Alamat -->
            <div class="modal fade" id="modalTambahAlamat" tabindex="-1" aria-labelledby="modalTambahAlamatLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form id="formTambahAlamat" method="POST" action="http://localhost/go-app/addalamat">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalTambahAlamatLabel">Tambah Alamat Baru</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="text" name="id_user" id="id_user" value="<?= $s1['id_user']?>">
                                <div class="mb-3">
                                    <label for="label" class="form-label">Label</label>
                                    <input type="text" class="form-control" id="label" name="label" placeholder="Masukkan Label">
                                </div>
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Masukkan Alamat">
                                </div>
                                <div class="mb-3">
                                    <label for="kota" class="form-label">Kota</label>
                                    <input type="text" class="form-control" id="kota" name="kota" placeholder="Masukkan Kota">
                                </div>
                                <div class="mb-3">
                                    <label for="provinsi" class="form-label">Provinsi</label>
                                    <input type="text" class="form-control" id="provinsi" name="provinsi" placeholder="Masukkan Provinsi">
                                </div>
                                <div class="mb-3">
                                    <label for="kode_pos" class="form-label">Kode Pos</label>
                                    <input type="text" class="form-control" id="kode_pos" name="kode_pos" placeholder="Masukkan Kode Pos">
                                </div>
                                <div class="mb-3">
                                    <label for="nama_penerima" class="form-label">Nama Penerima</label>
                                    <input type="text" class="form-control" id="nama_penerima" name="nama_penerima" placeholder="Masukkan Nama Penerima">
                                </div>
                                <div class="mb-3">
                                    <label for="no_hp" class="form-label">No Hp</label>
                                    <input type="text" class="form-control" id="no_hp" name="nohp_penerima" placeholder="Masukkan No Hp">
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

            <!-- / Content -->
            <?php include 'system/user/component/footer.php' ?>
            <div class="content-backdrop fade"></div>
          </div>
        </div>
      </div>
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    <!-- Core JS -->
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/libs/jquery/jquery.js"></script>
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/libs/popper/popper.js"></script>
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/js/bootstrap.js"></script>
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/js/menu.js"></script>
    <script src="<?php echo $urlweb; ?>/assets/admin/js/main.js"></script>
    <script src="<?php echo $urlweb; ?>/assets/admin/js/pages-account-settings-account.js"></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
