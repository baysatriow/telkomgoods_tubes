<div class="hamburger_menu">
        <div class="hamburger_close"><i class="fa fa-times" aria-hidden="true"></i></div>
        <div class="hamburger_menu_content text-right">
            <ul class="menu_top_nav">
			<?php if ($level != '') { ?>
				<li class="menu_item">
                    <a href="#">Akun Saya <i class="fa fa-angle-down"></i></a>
                </li>
			<?php } else {?>
                <li class="menu_item has-children">
                    <a href="#">Akun Saya <i class="fa fa-angle-down"></i></a>
                    <ul class="menu_selection">
                        <li><a href="#"><i class="fa fa-sign-in" aria-hidden="true"></i>Sign In</a></li>
                        <li><a href="#"><i class="fa fa-user-plus" aria-hidden="true"></i>Register</a></li>
                    </ul>
                </li>
			<?php } ?>
                <li class="menu_item"><a href="#">home</a></li>
                <li class="menu_item"><a href="#">shop</a></li>
                <li class="menu_item"><a href="#">contact</a></li>
            </ul>
        </div>
    </div>