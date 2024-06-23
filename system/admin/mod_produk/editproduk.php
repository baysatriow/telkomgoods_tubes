<?php
ob_start();
session_start();
require('config/database.php');
$sid = session_id();

// Get the product ID from the URL
$id_produk = isset($_GET['id']) ? $_GET['id'] : '';

if ($id_produk == '') {
    die('Product ID is required.');
}

// Fetch product details
$product_query = pg_query($conn, "SELECT * FROM tb_produk WHERE id_produk = '$id_produk'");
$product = pg_fetch_array($product_query);

if (!$product) {
    die('Product not found.');
}

// Fetch product images
$images_query = pg_query($conn, "SELECT * FROM tb_produk_gambar WHERE id_produk = '$id_produk'");
$images = pg_fetch_all($images_query);

// Fetch product variants
$variants_query = pg_query($conn, "SELECT * FROM tb_products_options WHERE id_produk = '$id_produk'");
$variants = pg_fetch_all($variants_query);

// Fetch categories
$categories_query = pg_query($conn, "SELECT * FROM tb_kategori_produk");
$categories = pg_fetch_all($categories_query);

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
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $urlweb; ?>/assets/admin/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Pengaturan Akun! - <?php echo $s0['nama_toko']; ?> </title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="<?php echo $urlweb; ?>/assets/admin/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo $urlweb; ?>/assets/admin/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="<?php echo $urlweb; ?>/assets/admin/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?php echo $urlweb; ?>/assets/admin/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?php echo $urlweb; ?>/assets/admin/css/demo.css" />
    <link rel="stylesheet" href="<?php echo $urlweb; ?>/assets/admin/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="<?php echo $urlweb; ?>/assets/plugins/izitoast/css/iziToast.min.css">
    <script src="<?php echo $urlweb; ?>/assets/plugins/izitoast/js/iziToast.min.js"></script>
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/js/helpers.js"></script>
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
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php include 'system/admin/component/sidebar.php' ?>
        <div class="layout-page">
            <?php include 'system/admin/component/topbar.php' ?>
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <a href="javascript:history.back()" class="btn btn-secondary mb-4">Kembali</a>
                    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Produk /</span> Edit Produk</h4>
                    <div class="row">
                        <div class="col-xxl">
                            <div class="card mb-4">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <h5 class="mb-0">Informasi Produk</h5>
                                </div>
                                <div class="card-body">
                                    <form id="formEditProduk" method="POST" action="http://localhost/go-app/editproduks" enctype="multipart/form-data">
                                        <input type="hidden" name="id_produk" value="<?= $product['id_produk'] ?>">
                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label" for="nama_produk">Nama Produk</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="<?= $product['nama_produk'] ?>" />
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label" for="kategori">Kategori</label>
                                            <div class="col-sm-10">
                                                <select name="kategori" class="form-select" id="kategori">
                                                    <option value="">Pilih Kategori</option>
                                                    <?php foreach ($categories as $kategori) { ?>
                                                        <option value="<?= $kategori['id_kategori'] ?>" <?= $kategori['id_kategori'] == $product['kategori'] ? 'selected' : '' ?>>
                                                            <?= $kategori['nama_kategori'] ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
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
                                        <h5 class="mt-4 mb-4">Detail Produk</h5>
                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label" for="deskripsi_produk">Deskripsi</label>
                                            <div class="col-sm-10">
                                                <textarea rows="13" id="deskripsi_produk" name="deskripsi_produk" class="form-control"><?= $product['deskripsi_produk'] ?></textarea>
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
                                            <label class="col-sm-2 col-form-label" for="status">Status</label>
                                            <div class="col-sm-10">
                                                <select name="status" class="form-select" id="status">
                                                    <option value="1" <?= $product['status'] == 1 ? 'selected' : '' ?>>Aktif</option>
                                                    <option value="0" <?= $product['status'] == 0 ? 'selected' : '' ?>>Tidak Aktif</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label" for="stok">Stok</label>
                                            <div class="col-sm-10">
                                                <input type="number" class="form-control" id="stok" name="stok" value="<?= $product['stok'] ?>" />
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label" for="harga">Harga</label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control" id="harga" name="harga" value="<?= number_format($product['harga'], 0, ',', '.') ?>" />
                                                    <input type="hidden" id="harga_hidden" name="harga_hidden" value="<?= $product['harga'] ?>" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label" for="berat">Berat (gram)</label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <span class="input-group-text">Gr</span>
                                                    <input type="text" class="form-control" id="berat" name="berat" value="<?= number_format($product['berat'], 0, ',', '.') ?>" />
                                                    <input type="hidden" id="berat_hidden" name="berat_hidden" value="<?= $product['berat'] ?>" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label" for="kode_sku">SKU</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="kode_sku" name="kode_sku" value="<?= $product['kode_sku'] ?>" />
                                            </div>
                                        </div>
                                        <div class="row justify-content-end">
                                            <div class="col-sm-10">
                                                <button type="submit" class="btn btn-primary">Save changes</button>
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

                                <?php foreach ($images as $i => $image) { ?>
                                    const imgPreview = document.getElementById('imagePreview<?= $i+1 ?>');
                                    imgPreview.src = 'data:image/jpeg;base64,<?= $image['gambar'] ?>';
                                    imgPreview.style.display = 'block';
                                    document.getElementById('icon<?= $i+1 ?>').style.display = 'none';
                                <?php } ?>

                                <?php foreach ($variants as $variant) { ?>
                                    addVariant();
                                    const variantTypeElem = document.getElementById('variantType' + variantCount);
                                    variantTypeElem.value = '<?= $variant['option_name'] ?>';
                                    const variantOptionsInput = document.getElementById('variantOptions' + variantCount + 'Input');
                                    variantOptionsInput.value = '<?= $variant['option_value'] ?>';
                                    addVariantOption(variantCount);
                                <?php } ?>

                                <?php for ($i = 1; $i <= 5; $i++) { ?>
                                    document.getElementById('imageInput<?= $i ?>').addEventListener('change', function(event) {
                                        const file = event.target.files[0];
                                        if (file) {
                                            const reader = new FileReader();
                                            reader.onload = function(e) {
                                                const imgPreview = document.getElementById('imagePreview<?= $i ?>');
                                                imgPreview.src = e.target.result;
                                                imgPreview.style.display = 'block';
                                                document.getElementById('icon<?= $i ?>').style.display = 'none';
                                            };
                                            reader.readAsDataURL(file);
                                        }
                                    });
                                <?php } ?>
                            });
                        </script>
                    </div>
                </div>
                <?php include 'system/admin/component/footer.php' ?>
                <div class="content-backdrop fade"></div>
            </div>
        </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
</div>

<script src="<?php echo $urlweb; ?>/assets/admin/vendor/libs/jquery/jquery.js"></script>
<script src="<?php echo $urlweb; ?>/assets/admin/vendor/libs/popper/popper.js"></script>
<script src="<?php echo $urlweb; ?>/assets/admin/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="<?php echo $urlweb; ?>/assets/admin/vendor/js/bootstrap.js"></script>
<script src="<?php echo $urlweb; ?>/assets/admin/vendor/js/menu.js"></script>
<script src="<?php echo $urlweb; ?>/assets/admin/js/main.js"></script>
<script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
</html>
