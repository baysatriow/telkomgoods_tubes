<nav class="navbar">
    <ul class="navbar_menu">
        <li><a href="#">home</a></li>
        <li><a href="#">shop</a></li>
        <li><a href="contact.html">contact</a></li>
    </ul>
    <ul class="navbar_user">
        <li><a href="#"><i class="fa fa-search" aria-hidden="true"></i></a></li>
        <li><a href="#"><i class="fa fa-user" aria-hidden="true"></i></a></li>
        <?php if ($level == '') { ?>
        <li class="checkout">
            <a href="login">
                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                <!-- <span id="checkout_items" class="checkout_items"></span> -->
            </a>
        </li>
        <?php } else { ?>
        <li class="checkout">
            <a href="keranjang">
                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                <span id="checkout_items" class="checkout_items"><?= $cart_count ?></span>
            </a>
        </li>
        <?php } ?>
    </ul>
    <div class="hamburger_container">
        <i class="fa fa-bars" aria-hidden="true"></i>
    </div>
</nav>
