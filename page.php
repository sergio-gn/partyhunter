<?php
/**
 * The template for displaying all single posts
*/

get_header();
get_template_part( 'parts/navigation' );
?>
<main class="main mt-xl-3" role="main">
    <div class="container-blog">
        <div class="d-flex justify-center gap-5 flex-d-columb-mb">
            <div class="mainpost">
                <?php while ( have_posts() ) : the_post(); ?>
                    <article <?php post_class(); ?>>
                        <?php if (has_post_thumbnail()): ?>
                            <div class="featured-image-wrapper">
                                <?php the_post_thumbnail('full', ['class' => 'featured-image']); ?>
                            </div>
                        <?php endif; ?>
                        <header class="post__header" role="heading">
                            <h1 class="post__title"><?php the_title(); ?></h1>
                        </header>
                        <p class="post__date pb-1">
                            Data: <?php echo esc_html(get_the_date('F j, Y'));?>
                        </p>
                        <hr>
                        <div class="post__content">
                            <?php the_content(); ?>
                        </div>
            
                        <footer class="post__footer">
                            <p class="post__date"><time><?php echo human_time_diff(strtotime($post->post_date)) . ' ' . __('atrás'); ?></time></p>
                            <div class="comments-area">
                                <?php
                                    if (comments_open() || get_comments_number()) {
                                        comments_template();
                                    }
                                ?>
                            </div>
                            <p class="post__comments">
                                <?php comments_popup_link(__('Sem comentários'), __('1 Comentário'), __('% Comentários')); ?>
                            </p>
                        </footer>
                    </article>
                <?php endwhile; ?>
            </div>
            <div class="sidebar">
                <div>
                    <?php
                            // set up the arguments for the query to select the main post
                            $args = array(
                                'post_type' => 'post',
                                'post_status' => 'publish',
                                'posts_per_page' => 5, // Set the number of recent posts to display
                                'orderby' => 'date',
                                'order' => 'DESC'
                            );
                        
                            // create a new WP_Query instance with the arguments
                            $query = new WP_Query( $args );
                        
                            // start the loop
                            if ( $query->have_posts() ) : 
                                while ( $query->have_posts() ) : $query->the_post(); 
                        ?>
                                <div style="border-top: 1px solid #414141;">
                                    <article <?php post_class(); ?>>
                                        <div class="d-flex align-items-center">
                                            <?php if(has_post_thumbnail()): ?>
                                                <div class="img-wrapper_sixpack_sidebar">
                                                    <div style="border-radius:.5rem" class="img-wrapper_thumbnail">
                                                        <?php the_post_thumbnail( array(40, 40) ); ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <header class="post__header px-1" role="heading">
                                                <p class="recent-posts-title">
                                                    <a class="bold" href="<?php the_permalink(); ?>">
                                                        <?php the_title(); ?>
                                                    </a>
                                                </p>
                                            </header>
                                        </div>
                                    </article>
                                </div>
                        <?php
                                endwhile;
                            endif;
                            // reset the query
                            wp_reset_postdata();
                        ?>
                </div>
            </div>
        </div>
    </div>
</main>


<?php get_footer();?>