<?php
// Create a new WP_Query instance
$the_query = new WP_Query(array(
    'post_type'      => 'page',
    'posts_per_page' => -1,
    'order'          => 'ASC',
    'orderby'        => 'publish_date',
    'meta_query'     => array(
        array(
            'key'   => '_wp_page_template',
            'value' => 'blocks_locations.php'
        )
    )
));
?>
<style>
    .alphabet_title{
        background:#1094c4;
    }
    .area_list{
        margin: 1rem 0;
        background: #1094c412;
        padding: 1rem;
    }
    .area_link{
        color: #1094c4;
        text-decoration: none;
        font-weight: bold;
    }
    .area_list_link{
        list-style: none;
    }
</style>
<div class="tab-content position-relative" id="pills-tabContent">
    <div class="tab-pane fade show active" id="pills-general" role="tabpanel" aria-labelledby="pills-general-tab">
        <div class="areawrap" data-aos="fade-up">
            <div class="container">
                <div class="row justify-content-center">
                    <?php
                    // The Loop
                    if ($the_query->have_posts()) {
                        $count = 0;
                        $htm3 = array_fill_keys(range('A', 'Z'), '');

                        while ($the_query->have_posts()) {
                            $the_query->the_post();
                            $title = get_the_title();
                            $title_no_Plumber = str_replace('Plumber', '', $title);
                            $first_char = strtoupper($title[0]);

                            // Check if the first character is a letter and corresponds to the range A-Z
                            if (isset($first_char) && ctype_alpha($first_char)) {
                                $liclass = str_replace(' ', '-', strtolower($title));
                                $htm3[$first_char] .= '<li class="area_list_link col-xl-3 col-lg-4 col-6 ' . $liclass . '"><a class="area_link" href="' . get_the_permalink() . '">' . $title_no_Plumber . '</a></li>';
                            }
                        }
                        ?>
                        <div class="arealistwrap">
                            <div class="row">
                                <?php
                                foreach ($htm3 as $char => $content) {
                                    if (!empty($content)) {
                                        ?>
                                        <div class="col-12 area_list char<?= $char ?> <?= ($count > 3) ? 'arealisthide' : ''; ?>">
                                            <div class="row">
                                                <div class="col-xl-1 col-2 mr-md-4">
                                                    <h3 class="alphabet_title font-weight-black text-uppercase text-white text-center p-md-3 p-2">
                                                        <?= $char ?>
                                                    </h3>
                                                </div>
                                                <div class="col">
                                                    <ul class="row">
                                                        <?= $content; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="mbottom"></div>
                                        </div>
                                        <?php
                                        $count++;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <!--<div class="loadmorewrap"><a href="javascript:void(0)" class="loadmorebtn">Load More</a></div>-->
                        <?php
                    } else {
                        echo '<p>No pages found.</p>';
                    }
                    // Restore original Post Data
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>