    <?php if ( !is_user_logged_in() && !wp_is_mobile()) : ?>
        <footer class="modular_theme_footer">
            <section class="site-footer z-1" id="colophon" itemtype="https://schema.org/WPFooter" itemscope="itemscope" itemid="#colophon">
                <div class="container">
                    <div class="logo_footer_wrap py-1">
                        <aside class="footer_logo">
                            <div class="custom-logo custom_logo_footer">
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
                        </aside>
                    </div>

                    <div class="d-grid grid_footer gap-2">
                        <div class="site-footer-primary-section-2 site-footer-section site-footer-section-2">
                            <aside class="footer-widget-area widget-area site-footer-focus-item footer-widget-area-inner">
                                <section id="custom_html-12" class="widget_text widget widget_custom_html">
                                    <div class="textwidget custom-html-widget">	
                                        <p class="widget-title-1">
                                            <?php
                                            $menu_locations = get_nav_menu_locations();

                                            if ( isset( $menu_locations['footer-1'] ) ) {
                                                $menu_id = $menu_locations['footer-1'];
                                                
                                                $menu_object = wp_get_nav_menu_object( $menu_id );
                                                
                                                if ( $menu_object ) {
                                                    echo esc_html( $menu_object->name );
                                                }
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </section>
                                <section id="nav_menu-4" class="widget widget_nav_menu">
                                    <?php
                                        wp_nav_menu( array(
                                            'theme_location' => 'footer-1',
                                            'container'      => false,
                                            'menu_class'     => 'footer-menu-list',
                                        ) );
                                    ?>
                                </section>		
                            </aside>
                        </div>
                        <div class="site-footer-primary-section-3 site-footer-section site-footer-section-3">
                            <aside class="footer-widget-area widget-area site-footer-focus-item footer-widget-area-inner">
                                <section id="custom_html-17" class="widget_text widget widget_custom_html">
                                    <div class="textwidget custom-html-widget">	
                                        <p class="widget-title-1">
                                            <?php
                                                $menu_locations = get_nav_menu_locations();

                                                if ( isset( $menu_locations['footer-2'] ) ) {
                                                    $menu_id = $menu_locations['footer-2'];
                                                    
                                                    $menu_object = wp_get_nav_menu_object( $menu_id );
                                                    
                                                    if ( $menu_object ) {
                                                        echo esc_html( $menu_object->name );
                                                    }
                                                }
                                            ?>
                                        </p>
                                    </div>
                                </section>
                                <section id="nav_menu-8" class="widget widget_nav_menu">
                                    <?php
                                        wp_nav_menu( array(
                                            'theme_location' => 'footer-2',
                                            'container'      => false,
                                            'menu_class'     => 'footer-menu-list',
                                        ) );
                                    ?>
                                </section>
                            </aside>
                        </div>
                        <div class="site-footer-primary-section-4 site-footer-section site-footer-section-4">
                            <section id="custom_html-20" class="widget_text widget widget_custom_html">
                                <div class="textwidget custom-html-widget">	
                                    <p class="widget-title-1">
                                        <?php
                                            $menu_locations = get_nav_menu_locations();

                                            if ( isset( $menu_locations['footer-3'] ) ) {
                                                $menu_id = $menu_locations['footer-3'];
                                                
                                                $menu_object = wp_get_nav_menu_object( $menu_id );
                                                
                                                if ( $menu_object ) {
                                                    echo esc_html( $menu_object->name );
                                                }
                                            }
                                        ?>
                                    </p>
                                </div>
                            </section>
                            <section id="nav_menu-9" class="widget widget_nav_menu">
                                <?php
                                    wp_nav_menu( array(
                                        'theme_location' => 'footer-3',
                                        'container'      => false,
                                        'menu_class'     => 'footer-menu-list',
                                    ) );
                                ?>
                            </section>
                        </div>
                        <div class="site-footer-primary-section-5 site-footer-section site-footer-section-5">
                            <aside class="footer-widget-area widget-area site-footer-focus-item footer-widget-area-inner">
                                <section class="widget_text widget widget_custom_html">
                                    <div class="textwidget custom-html-widget">
                                        <div class="footer-contact">
                                            <p class="widget-title-1">
                                                Info
                                            <div class="d-flex gap-1 py-1">
                                                <a href="/cadastrar-produtor-eventos/">Você é um produtor? Aplique aqui.</a>
                                            </div>
                                        </div>
                                        <div class="gmap">
                                            <?php 
                                                $google_map = get_field('google_maps','option'); 
                                                if($google_map):
                                                    echo $google_map;
                                                endif;
                                            ?>
                                        </div>
                                    </div>
                                </section>
                            </aside>
                        </div>
                    </div>
                </div>
            </section>
            <div class="container">
                <div class="footer_row">
                    <div class="col-auto">
                        <div class="footer_info_">
                            <div class="col-auto text-center">© Copyright <?php echo date("Y"); ?></div>
                            <div class="col-auto text-primary">|</div>
                            <div class="col-auto text-center"><?php echo get_bloginfo('name'); ?></div>
                            <div class="col-auto text-primary">|</div>
                            <div class="col-auto text-center">Todos os Direitos Reservados</div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex align-center gap-2">
                            <div class="col-12 col-lg-auto">
                                <div class="footer_info_">
                                    <div class="col-auto text-center">Termos &amp; Condições</div>
                                    <div class="col-auto text-primary">|</div>
                                    <div class="col-auto text-center">Políticas de Privacidade</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <a href="javascript:" id="return-to-top">
            <div class="return_to_top">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="#ffffff" class="bi bi-arrow-up" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z" />
                </svg>
            </div>
        </a>
    <?php endif; ?>

    <?php if ( is_user_logged_in() && wp_is_mobile()) : ?>
        <nav class="mobile_nav_sticky">
            <a class="mobile_nav_sticky_link" href="https://partyhunter.com.br/">
                <svg width="29" height="34" viewBox="0 0 29 34" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.3758 0.333528C13.8904 -0.111176 14.611 -0.111176 15.1256 0.333528L27.9512 11.4169C28.2983 11.7168 28.5014 12.1781 28.5014 12.6667V30.875C28.5014 32.1866 27.5443 33.25 26.3638 33.25H18.5259V20.5833C18.5259 19.7089 17.8879 19 17.1008 19H11.4006C10.6135 19 9.97549 19.7089 9.97549 20.5833V33.25H2.1376C0.957034 33.25 0 32.1866 0 30.875V12.6667C0 12.1781 0.20303 11.7168 0.550162 11.4169L13.3758 0.333528Z" fill="#D5FF74"/></svg>
            </a>
            <a class="mobile_nav_sticky_link" href="/melhores-festas/">
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.05435 21.5H1.81087C0.724348 21.5 0 22.2167 0 23.2917V37.625C0 38.7 0.724348 39.4167 1.81087 39.4167H9.05435C10.1409 39.4167 10.8652 38.7 10.8652 37.625V23.2917C10.8652 22.2167 10.1409 21.5 9.05435 21.5ZM38.0283 14.3333H30.7848C29.6983 14.3333 28.9739 15.05 28.9739 16.125V37.625C28.9739 38.7 29.6983 39.4167 30.7848 39.4167H38.0283C39.1148 39.4167 39.8391 38.7 39.8391 37.625V16.125C39.8391 15.05 39.1148 14.3333 38.0283 14.3333ZM23.5413 0H16.2978C15.2113 0 14.487 0.716667 14.487 1.79167V37.625C14.487 38.7 15.2113 39.4167 16.2978 39.4167H23.5413C24.6278 39.4167 25.3522 38.7 25.3522 37.625V1.79167C25.3522 0.716667 24.6278 0 23.5413 0Z" fill="white"/></svg>
            </a>
            <div class="mobile_nav_sticky_link">
                <?php echo do_shortcode('[profile_picture]') ?>
            </div>
        </nav>
    <?php endif; ?>
    </body>
    <?php wp_footer();?>
</html>