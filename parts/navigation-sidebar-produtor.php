<nav class="sidebar-navigation">
    <ul>
        <li class="active">
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
        </li>
        <li>
            <a href="/produtor">
                <svg fill="#ffffff" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 45.973 45.972" xml:space="preserve"><g><g><path d="M44.752,20.914L25.935,2.094c-0.781-0.781-1.842-1.22-2.946-1.22c-1.105,0-2.166,0.439-2.947,1.22L1.221,20.914 c-1.191,1.191-1.548,2.968-0.903,4.525c0.646,1.557,2.165,2.557,3.85,2.557h2.404v13.461c0,2.013,1.607,3.642,3.621,3.642h3.203 V32.93c0-0.927,0.766-1.651,1.692-1.651h6.223c0.926,0,1.673,0.725,1.673,1.651v12.168h12.799c2.013,0,3.612-1.629,3.612-3.642 V27.996h2.411c1.685,0,3.204-1,3.85-2.557C46.3,23.882,45.944,22.106,44.752,20.914z"/></g></g></svg>
            </a>
        </li>
        <li>
            <a href="/adicionar-evento/">
                <span class="tooltip">Adicionar Evento</span>
            </a>
        </li>
        <li>
            <a href="/meus-eventos/">
                <span class="tooltip">Meus Eventos</span>
            </a>
        </li>
        <li>
            <i class="fa fa-print"></i>
            <span class="tooltip">Editar Perfil</span>
        </li>
        <li>
            <?php  if (is_user_logged_in()) {?>
                <form action="<?php echo esc_url(home_url('?custom_logout=1')); ?>" method="post">
                    <button class="logout_btn" type="submit">
                        <svg height="24px" width="24px" version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512"  xml:space="preserve"><style type="text/css">.st0{fill:#ffffff;}</style><g><path class="st0" d="M319.982,142.443c21.826,0,39.521-17.702,39.521-39.529c0-21.828-17.694-39.529-39.521-39.529 c-21.827,0-39.522,17.701-39.522,39.529C280.46,124.741,298.154,142.443,319.982,142.443z"/><path class="st0" d="M503.418,398.064l-58.11-37.147l-46.814-73.562l-15.413-86.966l50.138-4.654l43.149,27.914 c5.799,3.737,13.459,2.686,18.005-2.506l0.248-0.296c5.044-5.745,4.515-14.479-1.206-19.562l-38.618-34.328 c-4.164-3.698-9.489-5.83-15.062-6.049l-96.354-5.363c-2.973-0.171-5.737,0.054-5.737,0.054c-1.238,0.101-2.491,0.264-3.737,0.459 c-7.021,1.207-13.327,4.118-18.566,8.213l-70.86,42.79l-44.628-24.98c-6.92-3.877-15.678-1.573-19.803,5.215l-0.553,0.926 c-2.039,3.37-2.662,7.395-1.72,11.217c0.957,3.799,3.378,7.084,6.764,9.092l53.182,31.744c6.609,3.954,14.681,4.671,21.898,1.946 l44.876-16.97l14.93,60.242l-63.021-0.242c-8.376-0.031-16.308,3.69-21.641,10.12c-5.34,6.452-7.496,14.938-5.908,23.15l16.697,86 c1.853,9.528,10.875,15.903,20.473,14.464l0.716-0.109c9.505-1.424,16.231-9.995,15.359-19.57l-5.652-61.621l87.698,2.772 l41.078,41.404c5.722,5.753,12.26,10.673,19.39,14.565l61.022,33.317c8.554,4.428,19.072,1.261,23.757-7.146l0.343-0.576 C514.41,413.616,511.622,403.046,503.418,398.064z"/><polygon class="st0" points="122.898,108.534 41.194,52.736 250.063,52.736 250.063,20.852 0,20.852 0,435.35 41.194,435.35 122.898,491.148 122.898,475.206 122.898,435.35 222.522,435.35 222.522,403.466 122.898,403.466"/></g></svg>
                    </button>
                </form>
            <?php } ?>
        </li>
    </ul>
</nav>