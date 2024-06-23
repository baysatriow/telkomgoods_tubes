        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="index.html" class="app-brand-link">
              <span class="app-brand-logo demo">
              <img src="data:image/png;base64,<?php echo $s0['logo']?>" alt="user-avatar" class="d-block rounded" height="50" width="50" style="margin-left: -10px;"/>
              </span>
              <span class="app-brand-text demo menu-text fw-bolder ms-2"><?php echo $s0['nama_toko']; ?></span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
              <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>
            <!-- Get Pg Status -->
            <?php
            $current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
            // parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $query_params);
            // $notif = isset($query_params['notif']) ? $query_params['notif'] : null;
            ?>
          <ul class="menu-inner py-1">
            <!-- Dashboard -->
            <li class="menu-item">
              <a href="../" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Halaman Utama</div>
              </a>
            </li>

            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Main Menu Admin</span>
            </li>
            <li class="menu-item <?= ($current_page == 'user') ? 'active' : '' ?>">
              <a href="." class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
              </a>
            </li>

            <li class="menu-item <?= ($current_page == 'addres') ? 'active' : '' ?>">
              <a href="alamat" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Alamat Saya</div>
              </a>
            </li>

            <li class="menu-item <?= ($current_page == 'akun') || ($current_page == 'password')? 'active open' : '' ?>">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bxs-user"></i>
                <div data-i18n="Account Settings">Akun Saya</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item <?= ($current_page == 'akun') ? 'active' : '' ?>">
                  <a href="akun" class="menu-link">
                    <div data-i18n="Analytics">Data Pribadi</div>
                  </a>
                </li>
                <li class="menu-item <?= ($current_page == 'password') ? 'active' : '' ?>">
                  <a href="password" class="menu-link">
                    <div data-i18n="Pengaturan Aplikasi!">Password</div>
                  </a>
                </li>
              </ul>
            </li>
        </ul>
        </aside>
        <!-- / Menu -->