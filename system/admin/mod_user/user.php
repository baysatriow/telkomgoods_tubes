<?php
ob_start();
session_start();
require('config/database.php');
$sid = session_id();
$result = pg_query($conn, "SELECT * FROM tb_settings WHERE id_setting = 1");
if (!$result) {
    die('Query gagal: ' . pg_last_error());
}
$s0 = pg_fetch_array($result);
$urlweb = $uri;
$pengguna = 1;

// Query to get level data
$query = "SELECT id_level, nama_level FROM tb_level";
$result = pg_query($conn, $query);

$levels = [];
while ($row = pg_fetch_assoc($result)) {
    $levels[] = $row;
}

// Query to get user data
$userQuery = "SELECT u.*, l.nama_level FROM tb_user u LEFT JOIN tb_level l ON u.level = l.id_level";
$userResult = pg_query($conn, $userQuery);

$users = [];
while ($row = pg_fetch_assoc($userResult)) {
    $users[] = $row;
}

$ip = $_SERVER['REMOTE_ADDR'];
$date = date('Y-m-d');
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
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include 'system/admin/component/sidebar.php'?>
            <div class="layout-page">
                <?php include 'system/admin/component/topbar.php'?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pengguna /</span> Kelola Pengguna</h4>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <h5 class="card-header">Tambah Pengguna</h5>
                                    <hr class="my-0" />
                                    <div class="card-body">
                                        <?php
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
                                                      iziToast.success({
                                                          title: 'Mantap!',
                                                          message: 'Data Berhasil di Update!',
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
                                                          message: 'Data Berhasil di Tambahkan!',
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
                                            if ($_GET['notif'] == 8) {
                                              echo "
                                                  iziToast.error({
                                                      title: 'Perhatian!',
                                                      message: 'Data Berhasil di Hapus',
                                                      position: 'topRight'
                                                  });
                                              ";
                                          }
                                              echo '</script>';
                                          }
                                        ?>
                                        <form id="formAddPengguna" method="POST" action="http://localhost/go-app/adduser" enctype="multipart/form-data">
                                          <div class="d-flex align-items-start align-items-sm-center gap-4">
                                            <img src="" alt="user-avatar" class="d-block rounded mb-4" height="100" width="100" id="uploadeduser" />
                                            <div class="button-wrapper">
                                              <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                                <span class="d-none d-sm-block">Upload Foto</span>
                                                <i class="bx bx-upload d-block d-sm-none"></i>
                                                <input type="file" id="upload" class="user-image-input" hidden accept="image/png, image/jpeg" name="image" />
                                              </label>
                                              <button type="button" class="btn btn-outline-secondary user-image-reset mb-4">
                                                <i class="bx bx-reset d-block d-sm-none"></i>
                                                <span class="d-none d-sm-block">Reset</span>
                                              </button>
                                              <p class="text-muted mb-0">Format di Terima JPG, GIF atau PNG. Maksimal 2MB !</p>
                                            </div>
                                          </div>
                                            <hr class="my-0" />
                                            <div class="row">
                                                <div class="mb-3 col-md-6">
                                                    <label for="username" class="form-label">Username</label>
                                                    <input class="form-control" type="text" id="username" name="username" autofocus required />
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="name" class="form-label">Nama Lengkap</label>
                                                    <input class="form-control" type="text" id="name" name="name" required />
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="email" class="form-label">E-mail</label>
                                                    <input class="form-control" type="email" id="email" name="email" placeholder="email@example.com" required />
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label class="form-label" for="phoneNumber">Phone Number</label>
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text">ID (+62)</span>
                                                        <input type="text" id="phoneNumber" name="nohp" class="form-control" placeholder="82111249430" required />
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="level" class="form-label">Level</label>
                                                    <select id="level" name="level" class="form-select" required>
                                                        <option value="">Select Level</option>
                                                        <?php foreach ($levels as $level): ?>
                                                            <option value="<?php echo $level['id_level']; ?>"><?php echo $level['nama_level']; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="status" class="form-label">Status</label>
                                                    <select id="status" name="status" class="select2 form-select">
                                                        <option value="1">Select</option>
                                                        <option value="1">Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3 col-md-6 form-password-toggle">
                                                  <label class="form-label" for="password">Password Baru !</label>
                                                  <div class="input-group input-group-merge">
                                                    <input
                                                      type="password"
                                                      id="password"
                                                      class="form-control"
                                                      name="password"
                                                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                      aria-describedby="password"
                                                    />
                                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                                  </div>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <button type="submit" class="btn btn-primary me-2">Save changes</button>
                                                <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <h5 class="card-header">List Pengguna</h5>
                            <div class="table-responsive text-nowrap">
                                <table class="table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Username</th>
                                            <th>E-mail</th>
                                            <th>Phone Number</th>
                                            <th>Level</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        <?php
                                        $no = 0;
                                        foreach ($users as $user) {
                                            $no++;
                                            $statusBadge = $user['status'] == 1 ? 'bg-label-primary' : 'bg-label-danger';
                                            $statusText = $user['status'] == 1 ? 'Active' : 'Inactive';
                                            $defaultImage = $urlweb . "/assets/admin/img/avatars/1.png";
                                            $userImage = $user['image'] ? "data:image/jpeg;base64,{$user['image']}" : $defaultImage;
                                        ?>
                                            <tr>
                                                <td><?= $no ?></td>
                                                <td><strong><?= $user['name'] ?></strong></td>
                                                <td><?= $user['username'] ?></td>
                                                <td><?= $user['email'] ?></td>
                                                <td><?= $user['nohp'] ?></td>
                                                <td><?= $user['nama_level'] ?></td>
                                                <td><span class="badge <?= $statusBadge ?> me-1"><?= $statusText ?></span></td>
                                                <td>
                                                    <div class="d-flex">
                                                        <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#edit<?= $user['id_user'] ?>">
                                                            <i class="bx bx-edit-alt"></i> Edit
                                                        </button>

                                                        <!-- Modal Edit -->
                                                        <div class="modal fade" id="edit<?= $user['id_user'] ?>" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <form id="formEditPengguna" method="POST" action="http://localhost/go-app/edituser?id=<?= $user['id_user'] ?>" enctype="multipart/form-data">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Edit Pengguna</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="mb-3">
                                                                                <input type="hidden" id="id_user" name="id_user" value="<?= $user['id_user'] ?>" />
                                                                                <label for="editUsername" class="form-label">Username</label>
                                                                                <input type="text" id="editUsername" name="username" class="form-control" value="<?= $user['username'] ?>" required />
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label for="editName" class="form-label">Nama Lengkap</label>
                                                                                <input type="text" id="editName" name="name" class="form-control" value="<?= $user['name'] ?>" required />
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label for="editEmail" class="form-label">E-mail</label>
                                                                                <input type="email" id="editEmail" name="email" class="form-control" value="<?= $user['email'] ?>" required />
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label for="editPhoneNumber" class="form-label">Phone Number</label>
                                                                                <div class="input-group input-group-merge">
                                                                                    <span class="input-group-text">ID (+62)</span>
                                                                                    <input type="text" id="editPhoneNumber" name="nohp" class="form-control" value="<?= $user['nohp'] ?>" required />
                                                                                </div>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label for="editLevel" class="form-label">Level</label>
                                                                                <select id="editLevel" name="level" class="form-select" required>
                                                                                    <?php foreach ($levels as $level): ?>
                                                                                        <option value="<?= $level['id_level'] ?>" <?= $user['level'] == $level['id_level'] ? 'selected' : '' ?>>
                                                                                            <?= $level['nama_level'] ?>
                                                                                        </option>
                                                                                    <?php endforeach; ?>
                                                                                </select>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label for="editStatus" class="form-label">Status</label>
                                                                                <select id="editStatus" name="status" class="form-select" required>
                                                                                    <option value="1" <?= $user['status'] == '1' ? 'selected' : '' ?>>Active</option>
                                                                                    <option value="0" <?= $user['status'] == '0' ? 'selected' : '' ?>>Inactive</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="mb-3 form-password-toggle">
                                                                              <label class="form-label" for="editPassword">Password Baru !</label>
                                                                              <div class="input-group input-group-merge">
                                                                                <input
                                                                                  type="password"
                                                                                  id="password"
                                                                                  class="form-control"
                                                                                  name="password"
                                                                                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                                                  aria-describedby="password"
                                                                                />
                                                                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                                                              </div>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label for="editImage" class="form-label">Upload Foto Baru</label>
                                                                                <input type="file" class="form-control" id="editImage" name="image" accept="image/png, image/jpeg" />
                                                                                <img src="<?= $userImage ?>" alt="user-avatar" class="d-block rounded mt-3" height="100" width="100" />
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

                                                        <a class="btn btn-danger" href="http://localhost/go-app/deluser?id=<?= $user['id_user'] ?>">
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
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/libs/jquery/jquery.js"></script>
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/libs/popper/popper.js"></script>
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/js/bootstrap.js"></script>
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="<?php echo $urlweb; ?>/assets/admin/vendor/js/menu.js"></script>
    <script src="<?php echo $urlweb; ?>/assets/admin/js/main.js"></script>
    <!-- Page JS -->
    <script src="<?php echo $urlweb; ?>/assets/admin/js/image_replace.js"></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script>
        $(document).ready(function () {
            $("#formAddPengguna").validate({
                rules: {
                    username: {
                        required: true,
                    },
                    name: {
                        required: true,
                    },
                    email: {
                        required: true,
                        email: true,
                    },
                    nohp: {
                        required: true,
                        digits: true,
                    },
                    password: {
                        required: true,
                    },
                    status: {
                        required: true,
                    },
                    alamat: {
                        required: true,
                    },
                },
                messages: {
                    username: {
                        required: "Please enter your username",
                    },
                    name: {
                        required: "Please enter your full name",
                    },
                    email: {
                        required: "Please enter your email",
                        email: "Please enter a valid email address",
                    },
                    nohp: {
                        required: "Please enter your phone number",
                        digits: "Please enter only digits",
                    },
                    password: {
                        required: "Please provide a password",
                    },
                    status: {
                        required: "Please select a status",
                    },
                },
                submitHandler: function (form) {
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>
