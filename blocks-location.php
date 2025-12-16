<?php
/* Template Name: Blocks Location */

get_header();

get_template_part( 'parts/navigation' );

// Start output buffering
ob_start();

if (have_posts()):
    while (have_posts()) : the_post(); ?>

        <section>
            <article class="main-article">
                <?php 
                    $has_glide = false;
                    
                    // Dynamically get block layouts from the 'blocks' directory
                    $block_directory = get_template_directory() . '/blocks';
                    $block_files = glob($block_directory . '/*.php');

                    $block_layouts = array_map(function($file) {
                        return basename($file, '.php');
                    }, $block_files);

                    if (have_rows('blocks','option')): 
                        while (have_rows('blocks','option')): the_row();
                            $layout = get_row_layout();
                            if (in_array($layout, ['testimonial_slider', 'carousel'])) {
                                $has_glide = true;
                            }

                            if (in_array($layout, $block_layouts)) {
                                include $block_directory . '/' . $layout . '.php';
                            }
                        endwhile;
                    endif;
                ?>

                <?php if ($has_glide): ?>
                    <script src="<?php echo esc_url(get_template_directory_uri() . '/blocks/carousel-assets/carousel.js'); ?>"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            <?php
                                $carouselConfig = [
                                    'testimonial' => isset($howManySlidesTestimonial) ? $howManySlidesTestimonial : 1,
                                    'carousel' => isset($howManySlidesCarousel) ? $howManySlidesCarousel : 1,
                                ];
                            ?>
                            const carouselConfig = <?php echo json_encode($carouselConfig); ?>;
                            const sliders = document.querySelectorAll('.glide');
                            sliders.forEach(item => {
                                let perView = 1;

                                if (item.classList.contains('testimonial-carousel')) {
                                    perView = carouselConfig.testimonial;
                                } else if (item.classList.contains('main-carousel')) {
                                    perView = carouselConfig.carousel;
                                }

                                const conf = {
                                    type: 'carousel',
                                    focusAt: 'center',
                                    startAt: 0, // Ensure the first slider is fully visible on load
                                    focusAt: 0, // Focus on the first slider
                                    perView: perView,
                                    animationDuration: 300,
                                    autoplay: false,
                                    hoverpause: true,
                                    peek: {
                                        before: 0,
                                        after: 150
                                    },
                                    breakpoints: {
                                        768: {
                                            perView: 1
                                        }
                                    }
                                };

                                new Glide(item, conf).mount();
                            });
                        });
                    </script>
                <?php endif; ?>
            </article>
        </section>
<?php
    endwhile;
endif;

// Get the content from the output buffer
$page_content = ob_get_clean();

// Get the page title
$page_title = get_the_title();

// Replace [suburb] and [Suburb] with the page title
$page_content = str_replace(['[suburb]', '[Suburb]'], $page_title, $page_content);

// Output the modified content
echo $page_content;

get_footer();
?>
