<?php
session_start();
require('config/database.php');

$sid = session_id();

$result = pg_query($conn, "SELECT * FROM tb_settings WHERE id_setting = 1");
$result1 = pg_query($conn, "SELECT * FROM tb_user WHERE id_user = 1");

if (!$result) {
    die('Query failed: ' . pg_last_error());
}

if (!$result1) {
    die('Query failed: ' . pg_last_error());
}

$s0 = pg_fetch_array($result);
$s1 = pg_fetch_array($result1);

$urlweb = $uri;
$pengguna = 1;
$ip = $_SERVER['REMOTE_ADDR'];
$date = date('Y-m-d');
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $urlweb; ?>/assets/admin/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Daftar Produk | <?php echo $s0['nama_toko']; ?></title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="<?php echo $urlweb; ?>/assets/admin/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
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
        .modal-body img {
            width: 200px;
            height: 200px;
            border-radius: 10px;
            margin-right: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .modal-body .product-details {
            font-size: 16px;
            line-height: 1.5;
            color: #333;
        }
        .modal-body .product-details strong {
            display: inline-block;
            width: 120px;
            color: #555;
        }
        .modal-body .product-details h5 {
            font-size: 24px;
            margin-bottom: 15px;
            white-space: pre-wrap;
        }
        .table-select {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        .table-select select {
            margin-right: 10px;
        }
        .table-responsive {
            border: 1px solid #dee2e6;
            border-radius: .375rem;
            overflow: hidden;
        }
        .table thead {
            background-color: #f8f9fa;
        }
        .table thead th {
            border-bottom: 2px solid #dee2e6;
        }
        .table tbody tr {
            border-bottom: 1px solid #dee2e6;
        }
        .table tbody tr:last-child {
            border-bottom: none;
        }
        .table tbody tr:hover {
            background-color: #f1f3f5;
        }
        .table img {
            border-radius: .375rem;
        }
    </style>
    <script>
        function splitProductName(name, maxLength) {
            const words = name.split(' ');
            let lines = [];
            let currentLine = '';

            words.forEach(word => {
                if ((currentLine + word).length > maxLength) {
                    lines.push(currentLine.trim());
                    currentLine = '';
                }
                currentLine += word + ' ';
            });

            if (currentLine.length > 0) {
                lines.push(currentLine.trim());
            }

            return lines.join('<br>');
        }

        let currentPage = 1;
        let limit = 10;

        function toggleSelectAll(source) {
            checkboxes = document.getElementsByName('select_product[]');
            for (var i in checkboxes) {
                checkboxes[i].checked = source.checked;
            }
        }

        function deleteSelected() {
            var selected = [];
            var checkboxes = document.getElementsByName('select_product[]');
            for (var i in checkboxes) {
                if (checkboxes[i].checked) {
                    selected.push(checkboxes[i].value);
                }
            }
            if (selected.length > 0) {
                if (confirm("Are you sure you want to delete selected products?")) {
                    window.location.href = 'http://localhost/go-app/delproduksbulk?id=' + selected.join(',');
                }
            } else {
                alert("Please select at least one product to delete.");
            }
        }

        function showModalDetail(product) {
            document.getElementById('modalDetailImage').src = 'data:image/jpeg;base64,' + product.gambar;
            document.getElementById('modalDetailNamaProduk').innerText = product.nama_produk;
            document.getElementById('modalDetailKategori').innerText = product.nama_kategori;
            document.getElementById('modalDetailHarga').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(product.harga);
            document.getElementById('modalDetailStok').innerText = product.stok;
            document.getElementById('modalDetailStatus').innerText = product.status == 1 ? 'Aktif' : 'Nonaktif';
            document.getElementById('modalDetailDeskripsi').innerText = product.deskripsi_produk;
            document.getElementById('modalDetailKodeSKU').innerText = product.kode_sku;
            var modal = new bootstrap.Modal(document.getElementById('productDetailModal'), {});
            modal.show();
        }

        function htmlspecialchars(str, quote_style, charset, double_encode) {
            var optTemp = 0,
                i = 0,
                noquotes = false;

            if (typeof quote_style === 'undefined' || quote_style === null) {
                quote_style = 2;
            }
            str = str.toString();
            if (double_encode !== false) {
                str = str.replace(/&/g, '&amp;');
            }
            str = str.replace(/</g, '&lt;').replace(/>/g, '&gt;');

            var OPTS = {
                'ENT_NOQUOTES': 0,
                'ENT_HTML_QUOTE_SINGLE': 1,
                'ENT_HTML_QUOTE_DOUBLE': 2,
                'ENT_COMPAT': 2,
                'ENT_QUOTES': 3,
                'ENT_IGNORE': 4
            };
            if (quote_style === 0) {
                noquotes = true;
            }
            if (typeof quote_style !== 'number') {
                quote_style = [].concat(quote_style);
                for (i = 0; i < quote_style.length; i++) {
                    if (OPTS[quote_style[i]] === 0) {
                        noquotes = true;
                    } else if (OPTS[quote_style[i]]) {
                        optTemp = optTemp | OPTS[quote_style[i]];
                    }
                }
                quote_style = optTemp;
            }
            if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
                str = str.replace(/'/g, '&#039;');
            }
            if (!noquotes) {
                str = str.replace(/"/g, '&quot;');
            }

            return str;
        }

        function fetchProducts(sort, order, page, limit) {
            const url = `http://localhost/go-app/fetchproduk?sort=${sort}&order=${order}&page=${page}&limit=${limit}`;
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    const tableBody = document.querySelector('.table tbody');
                    tableBody.innerHTML = '';
                    data.forEach((product, index) => {
                        const productData = htmlspecialchars(JSON.stringify(product), 'ENT_QUOTES', 'UTF-8');
                        const productNameFormatted = splitProductName(product.nama_produk, 50);
                        const rowNumber = (page - 1) * limit + index + 1;
                        const row = `
                            <tr>
                                <td>${rowNumber}</td> <!-- Add row number here -->
                                <td><input type="checkbox" name="select_product[]" value="${product.id_produk}" /></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="data:image/png;base64,${product.gambar}" alt="${product.nama_produk}" style="width: 50px; height: 50px; margin-right: 10px;">
                                        <div>
                                            <strong>${productNameFormatted}</strong><br>
                                            <small>SKU: ${product.kode_sku}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>${product.nama_kategori}</td>
                                <td>Rp ${new Intl.NumberFormat('id-ID').format(product.harga)}</td>
                                <td>${product.stok} ${product.stok == 0 ? '<span class="text-danger"> (Habis)</span>' : ''}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" ${product.status == 1 ? 'checked' : ''} disabled>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            Atur
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="editproduk?id=${product.id_produk}">Edit</a></li>
                                            <li><a class="dropdown-item" href="http://localhost/go-app/delproduks?id=${product.id_produk}">Delete</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="showModalDetail(${productData})">Detail</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });

                    updatePaginationControls();
                })
                .catch(error => {
                    console.error('Error fetching products:', error);
                    alert('Error fetching products. Please try again later.');
                });
        }

        function updatePaginationControls() {
            const paginationControls = document.getElementById('paginationControls');
            paginationControls.innerHTML = `
                <button class="btn btn-secondary" onclick="prevPage()" ${currentPage === 1 ? 'disabled' : ''}>
                    <i class="bx bx-left-arrow-alt"></i>
                </button>
                <span class="mx-2">Page ${currentPage}</span>
                <button class="btn btn-secondary" onclick="nextPage()">
                    <i class="bx bx-right-arrow-alt"></i>
                </button>
            `;
        }

        function prevPage() {
            if (currentPage > 1) {
                currentPage--;
                fetchProducts(document.getElementById('sortSelect').value, document.getElementById('orderSelect').value, currentPage, limit);
            }
        }

        function nextPage() {
            currentPage++;
            fetchProducts(document.getElementById('sortSelect').value, document.getElementById('orderSelect').value, currentPage, limit);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const sortSelect = document.getElementById('sortSelect');
            const orderSelect = document.getElementById('orderSelect');
            const limitSelect = document.getElementById('limitSelect');

            sortSelect.addEventListener('change', () => {
                fetchProducts(sortSelect.value, orderSelect.value, currentPage, limit);
            });

            orderSelect.addEventListener('change', () => {
                fetchProducts(sortSelect.value, orderSelect.value, currentPage, limit);
            });

            limitSelect.addEventListener('change', () => {
                limit = parseInt(limitSelect.value);
                fetchProducts(sortSelect.value, orderSelect.value, currentPage, limit);
            });

            fetchProducts(sortSelect.value, orderSelect.value, currentPage, limit);
        });
    </script>
</head>
<body>
<?php
error_reporting(0);
if (!empty($_GET['notif'])) {
    echo '<script>';
    switch ($_GET['notif']) {
        case 1:
            echo "iziToast.warning({title: 'Perhatian!', message: 'Username dan Password Wajib Diisi!', position: 'topRight'});";
            break;
        case 2:
            echo "iziToast.warning({title: 'Perhatian!', message: 'Username Wajib Diisi!', position: 'topRight'});";
            break;
        case 3:
            echo "iziToast.warning({title: 'Perhatian!', message: 'Gagal Update Data!', position: 'topRight'});";
            break;
        case 4:
            echo "iziToast.warning({title: 'Perhatian!', message: 'Gagal Update Gambar!', position: 'topRight'});";
            break;
        case 5:
            echo "iziToast.success({title: 'Well done!', message: 'Akun berhasil di verifikasi!', position: 'topRight'});";
            break;
        case 6:
            echo "iziToast.warning({title: 'Perhatian!', message: 'Terjadi Kesalahan!', position: 'topRight'});";
            break;
        case 7:
            echo "iziToast.warning({title: 'Perhatian!', message: 'Ukuran Gambar Terlalu Besar!', position: 'topRight'});";
            break;
    }
    echo '</script>';
}
?>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php include 'system/admin/component/sidebar.php' ?>
        <div class="layout-page">
            <?php include 'system/admin/component/topbar.php' ?>
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Produk /</span> Daftar Produk</h4>
                    <ul class="nav nav-pills flex-column flex-md-row mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" href="javascript:void(0);"><i class="bx bx-user me-1"></i> Data Produk</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="addproduk"><i class="bx bx-link-alt me-1"></i>Tambah Produk</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="addkategori"><i class="bx bx-link-alt me-1"></i>Kategori</a>
                        </li>
                    </ul>
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="table-select">
                                <select id="sortSelect" class="form-select">
                                    <option value="nama_produk">Nama</option>
                                    <option value="harga">Harga</option>
                                </select>
                                <select id="orderSelect" class="form-select">
                                    <option value="asc">Asc</option>
                                    <option value="desc">Desc</option>
                                </select>
                                <select id="limitSelect" class="form-select">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                                <button class="btn btn-danger" onClick="deleteSelected()"><i class="bx bx-trash"></i></button>
                            </div>
                            <button class="btn btn-primary" onclick="window.location.href='addproduk'">Tambah Produk</button>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th> <!-- Add this line -->
                                        <th><input type="checkbox" onClick="toggleSelectAll(this)" /></th>
                                        <th>Info Produk</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                        <th>Aktif</th>
                                        <th>Atur</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <!-- Data will be populated by JavaScript -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="8">
                                            <div id="paginationControls" class="d-flex justify-content-start mt-3"></div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php include 'system/admin/component/footer.php' ?>
                <div class="content-backdrop fade"></div>
            </div>
        </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
</div>
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productDetailModalLabel">Detail Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-start mb-3">
                    <img id="modalDetailImage" src="" alt="Product Image">
                    <div class="product-details">
                        <h5 id="modalDetailNamaProduk"></h5>
                        <p><strong>Kode SKU: </strong><span id="modalDetailKodeSKU"></span></p>
                        <p><strong>Kategori: </strong><span id="modalDetailKategori"></span></p>
                        <p><strong>Harga: </strong><span id="modalDetailHarga"></span></p>
                        <p><strong>Stok: </strong><span id="modalDetailStok"></span></p>
                        <p><strong>Status: </strong><span id="modalDetailStatus"></span></p>
                        <p><strong>Deskripsi: </strong><span id="modalDetailDeskripsi"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
