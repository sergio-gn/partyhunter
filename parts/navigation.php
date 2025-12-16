<header style="<?php if ( is_user_logged_in()) { echo 'margin-top: 32px;'; } ?>">
	<div class="navbar-bar">
		<div class="container mobile_nav">
	        <div class="d-flex align-center justify-between gap-1">
	            <div class="custom-logo">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home" aria-current="page">
                        <?php
                            $custom_logo_id = get_theme_mod( 'custom_logo' );
                            $custom_logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
                            if ( $custom_logo_url ) {
                                echo '<img src="' . esc_url( $custom_logo_url ) . '" alt="' . get_bloginfo( 'name' ) . '">';
                            } else {
                                echo '<img src="' . esc_url( get_template_directory_uri() . '/path-to-default-logo.png' ) . '" alt="' . get_bloginfo( 'name' ) . '">';
                            }
                        ?>
                    </a>
	            </div>
                <div class="navigation">
                    <div class="container">
                        <div class="nav-class">
                            <?php if (is_user_logged_in()): ?>
                                <!-- Simple logged-in user menu -->
                                <ul class="nav-class">
                                    <li>
                                        <div class="cta_buttons_nav">
                                            <?php echo do_shortcode('[profile_picture]') ?>
                                            <?php if (function_exists('ph_get_friend_requests_widget')) echo ph_get_friend_requests_widget(); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <?php echo display_logout_button(); ?>
                                    </li>
                                </ul>
                            <?php else: ?>
                                <!-- Full menu for non-logged-in users -->
                                <?php
                                    wp_nav_menu(array(
                                        'theme_location' => 'primary_menu',
                                        'menu_class' => 'nav-class',
                                    ));
                                ?>
                                <div class="cta_buttons_nav">
                                    <?php echo do_shortcode('[profile_picture]') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <button class="button_mb desktop_none" onclick="toggleActiveClass()">
                    ☰
                </button>
	        </div>
        </div>
	</div>
    <script>
        function toggleActiveClass() {
          var menuContainer = document.querySelector('.navigation');
          menuContainer.classList.toggle('active');
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all toggle buttons
            var toggleButtons = document.querySelectorAll('.sub-menu-toggle');

            toggleButtons.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Find the parent menu item
                    var parentItem = this.closest('.menu-item-has-children');
                    
                    // Toggle the 'open' class on the parent menu item
                    parentItem.classList.toggle('open');
                    
                    // Find the sub-menu within the parent item and toggle its display
                    var subMenu = parentItem.querySelector('.sub-menu');
                    if (subMenu) {
                        if (subMenu.style.display === 'block') {
                            subMenu.style.display = 'none';
                        } else {
                            subMenu.style.display = 'block';
                        }
                    }
                    
                    // Toggle the aria-expanded attribute
                    if (this.getAttribute('aria-expanded') === 'true') {
                        this.setAttribute('aria-expanded', 'false');
                    } else {
                        this.setAttribute('aria-expanded', 'true');
                    }
                });
            });
        });

    </script>
</header>