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
    <style>
        .image-container {
            position: relative;
            width: 150px;
            height: 150px;
            border: 2px dashed #ddd;
            display: inline-block;
            margin: 10px;
            border-radius: 5px;
            overflow: hidden;
            transition: all 0.3s ease-in-out;
            background-color: #f8f9fa;
            text-align: center;
        }

        .image-container:hover {
            border-color: #00b300;
        }

        .image-container input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 10;
        }

        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
            display: none;
        }

        .image-container .icon {
            font-size: 48px;
            color: #ddd;
            line-height: 150px;
            z-index: 5;
        }

        .image-container .image-label {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            text-align: center;
            padding: 5px 0;
            font-size: 14px;
            z-index: 5;
        }

        .variant-options {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        .variant-option {
            display: inline-block;
            background: #f1f1f1;
            padding: 5px 10px;
            border-radius: 5px;
            margin: 5px 0;
        }
        .variant-option .remove-option {
            cursor: pointer;
            margin-left: 5px;
            color: red;
        }
    </style>
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
              <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Produk /</span> Tambah Produk</h4>

              <div class="row">
              <div class="col-xxl">
                <!-- Menu Tambahan -->
                <ul class="nav nav-pills flex-column flex-md-row mb-3">
                    <li class="nav-item">
                      <a class="nav-link" href="myproduk"><i class="bx bx-user me-1"></i> Data Produk</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link active" href="javascript:void(0);"
                        ><i class="bx bx-link-alt me-1"></i>Tambah Produk</a
                      >
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="addkategori"
                        ><i class="bx bx-link-alt me-1"></i>Kategori</a
                      >
                    </li>
                  </ul>
                  <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h5 class="mb-0">Informasi Produk</h5>
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
                                      message: 'Produk Berhasil di Tambahkan!',
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
                      <form id="formAddProduk" method="POST" action="http://localhost/go-app/addproduks" enctype="multipart/form-data">
                      <div class="row mb-3">
                          <label class="col-sm-2 col-form-label" for="basic-default-name">Nama Produk</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" id="nama_produk" name="nama_produk" placeholder="Logitech SuperlightX" />
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label class="col-sm-2 col-form-label" for="kategori">Kategori</label>
                          <div class="col-sm-10">
                          <select name="kategori" class="form-select" id="kategori" aria-label="Default select example">
                            <option selected value="">Pilih Kategori</option>
                            <?php
                                $query = pg_query($conn, "SELECT * FROM tb_kategori_produk");
                                while ($kategori = pg_fetch_array($query)) {
                            ?>
                            <option value="<?=$kategori['id_kategori']?>"><?=$kategori['nama_kategori']?></option>
                            <?php } ?>
                          </select>
                          </div>
                        </div>
                        <h5 class="mt-4 mb-4">Detail Produk</h5>
                        <div class="row mb-3">
                          <label class="col-sm-2 col-form-label" for="basic-default-message">Foto Produk</label>
                          <div class="col-sm-10">
                          <?php for ($i = 1; $i <= 5; $i++) { ?>
                            <div class="image-container">
                                <input type="file" id="imageInput<?= $i ?>" name="image<?= $i ?>">
                                <div class="icon" id="icon<?= $i ?>">+</div>
                                <img src="" alt="Selected Image" id="imagePreview<?= $i ?>">
                                <div class="image-label">Foto <?= $i ?></div>
                            </div>
                          <?php } ?>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label class="col-sm-2 col-form-label" for="deskripsi_produk">Deskripsi</label>
                          <div class="col-sm-10">
                            <textarea rows="13"
                              id="deskripsi_produk" name="deskripsi_produk"
                              class="form-control"
                              placeholder="Sepatu Sneakers Pria Tokostore Kanvas Hitam Seri C28B
- Model simple
- Nyaman Digunakan
- Tersedia warna hitam
- Sole PVC (injection shoes) yang nyaman dan awet untuk digunakan sehari - hari

Bahan:
Upper: Semi Leather (kulit tidak pecah-pecah)
Sole: Premium Rubber Sole

Ukuran
39 : 25,5 cm
40 : 26 cm
41 : 26.5 cm
42 : 27 cm
43 : 27.5 - 28 cm

Edisi terbatas dari Tokostore dengan model baru dan trendy untukmu. Didesain untuk bisa dipakai dalam berbagai acara. Sangat nyaman saat dipakai sehingga dapat menunjang penampilan dan kepercayaan dirimu. Beli sekarang sebelum kehabisan!"
                              aria-label="Hi, Do you have a moment to talk Joe?"
                              aria-describedby="basic-icon-default-message2"
                            ></textarea>
                          </div>
                        </div>
                        <h5 class="mt-4 mb-4">Varian Produk</h5>
                        <div class="row mb-3">
                            <div class="col-sm-10 offset-sm-2">
                            <button type="button" class="btn btn-secondary" id="addVariantButton" onclick="addVariant()">Tambah Varian</button>
                            <div id="variantSection">
                                <!-- Variant sections will be added here dynamically -->
                            </div>
                            </div>
                        </div>
                        <h5 class="mt-4 mb-4">Pengelolaan Produk</h5>
                        <div class="row mb-3">
                          <label class="col-sm-2 col-form-label" for="basic-default-name">Status</label>
                          <div class="col-sm-10">
                          <select name="status" class="form-select" id="exampleFormControlSelect1" aria-label="Default select example">                  
                          <option selected value="1">Pilih Status</option>
                          <option value="1">Aktif</option>
                          <option value="0">Tidak Aktif</option>
                          </select>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label class="col-sm-2 col-form-label" for="stok">Stok</label>
                          <div class="col-sm-10">
                            <input type="number" class="form-control" id="stok" name="stok" placeholder="1" value="1" />
                          </div>
                        </div>
                        <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="harga">Harga</label>
                        <div class="col-sm-10">
                        <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="harga" name="harga" placeholder="800.000" />
                                <input type="hidden" id="harga_hidden" name="harga_hidden" />
                            </div>
                        </div>
                        </div>
                        <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="Berat (gram)">Berat (gram)</label>
                        <div class="col-sm-10">
                        <div class="input-group">
                                <span class="input-group-text">Gr</span>
                                <input type="text" class="form-control" id="berat" name="berat" placeholder="1.000" oninput="formatGrams(this)" />
                                <input type="hidden" id="berat_hidden" name="berat_hidden" />
                            </div>
                        </div>
                        </div>
                        <div class="row mb-3">
                          <label class="col-sm-2 col-form-label" for="kode_sku">SKU</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" id="kode_sku" name="kode_sku" placeholder="ABC-XXXXX" />
                          </div>
                        </div>
                        <div class="row justify-content-end">
                          <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Send</button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <script>
                      let variantCount = 0;
                      function addVariant() {
                          if (variantCount >= 2) return;
                          variantCount++;
                          const variantSection = document.getElementById('variantSection');
                          const variantBlock = document.createElement('div');
                          variantBlock.className = 'variant-block mb-4';
                          variantBlock.id = `variantBlock${variantCount}`;
                          variantBlock.innerHTML = `
                              <div class="mb-3">
                                  <label class="form-label">Tipe Varian ${variantCount}</label>
                                  <select class="form-select" id="variantType${variantCount}" name="variant_type${variantCount}">
                                      <option selected>Pilih Varian</option>
                                      <option value="Warna">Warna</option>
                                      <option value="Ukuran">Ukuran</option>
                                  </select>
                              </div>
                              <div class="mb-3">
                                  <label class="form-label">Pilihan Varian</label>
                                  <input type="text" class="form-control" id="variantOptions${variantCount}Input" placeholder="Tambah Pilihan Varian" />
                                  <div class="mt-2 variant-options" id="variantOptions${variantCount}">
                                  </div>
                              </div>
                              <button type="button" class="btn btn-secondary" onclick="addVariantOption(${variantCount})">Tambah</button>
                              <button type="button" class="btn btn-danger" onclick="removeVariant(${variantCount})">Hapus Varian</button>
                          `;
                          variantSection.appendChild(variantBlock);
                          if (variantCount === 2) {
                              document.getElementById('addVariantButton').disabled = true;
                          }
                      }
                      function addVariantOption(variantNumber) {
                          const variantOptionsInput = document.getElementById(`variantOptions${variantNumber}Input`);
                          const variantOptions = document.getElementById(`variantOptions${variantNumber}`);
                          const newOption = variantOptionsInput.value.trim();
                          if (newOption && variantOptions.children.length < 5) {
                              const optionElement = document.createElement('span');
                              optionElement.className = 'variant-option';
                              optionElement.innerText = newOption;
                              const removeElement = document.createElement('span');
                              removeElement.className = 'remove-option';
                              removeElement.innerText = 'x';
                              removeElement.onclick = () => variantOptions.removeChild(optionElement);
                              optionElement.appendChild(removeElement);
                              
                              // Add hidden input to form
                              const hiddenInput = document.createElement('input');
                              hiddenInput.type = 'hidden';
                              hiddenInput.name = `variantOptions${variantNumber}[]`;
                              hiddenInput.value = newOption;

                              variantOptions.appendChild(optionElement);
                              variantOptions.appendChild(hiddenInput);
                              variantOptionsInput.value = '';
                          }
                      }
                      function removeVariant(variantNumber) {
                          const variantBlock = document.getElementById(`variantBlock${variantNumber}`);
                          variantBlock.remove();
                          variantCount--;
                          document.getElementById('addVariantButton').disabled = false;
                      }

                      function formatRupiah(input) {
                          let value = input.value.replace(/\D/g, '');
                          input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                          // Update hidden input with unformatted value
                          document.getElementById('harga_hidden').value = value;
                      }

                      function formatGrams(input) {
                          let value = input.value.replace(/\D/g, '');
                          input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                          // Update hidden input with unformatted value
                          document.getElementById('berat_hidden').value = value;
                      }

                      document.addEventListener('DOMContentLoaded', () => {
                          const hargaInput = document.getElementById('harga');
                          hargaInput.addEventListener('input', (event) => {
                              formatRupiah(event.target);
                          });

                          const beratInput = document.getElementById('berat');
                          beratInput.addEventListener('input', (event) => {
                              formatGrams(event.target);
                          });
                      });

                      <?php for ($i = 1; $i <= 5; $i++) { ?>
                          const imageInput<?= $i ?> = document.getElementById('imageInput<?= $i ?>');
                          const imagePreview<?= $i ?> = document.getElementById('imagePreview<?= $i ?>');
                          const icon<?= $i ?> = document.getElementById('icon<?= $i ?>');
                          imageInput<?= $i ?>.addEventListener('change', (event) => {
                              const file = event.target.files[0];
                              if (file) {
                                  const reader = new FileReader();
                                  reader.onload = (e) => {
                                      imagePreview<?= $i ?>.src = e.target.result;
                                      imagePreview<?= $i ?>.style.display = 'block';
                                      icon<?= $i ?>.style.display = 'none';
                                  };
                                  reader.readAsDataURL(file);
                              }
                          });
                      <?php } ?>
                  </script>
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
    <script src="../assets/admin/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/admin/vendor/libs/popper/popper.js"></script>
    <script src="../assets/admin/vendor/js/bootstrap.js"></script>
    <script src="../assets/admin/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../assets/admin/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="../assets/admin/js/main.js"></script>

    <!-- Page JS -->
    <script src="../assets/admin/js/pages-account-settings-account.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
