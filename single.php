<?php
/**
 * The template for displaying all single posts
*/

get_header();
get_template_part( 'parts/navigation' );
?>
<style>
    .main{
        font-family: system-ui;
    }
    .container_blog {
        width: 100%;
        padding: 2rem 1rem;
        margin-right: auto;
        margin-left: auto;
        box-sizing: border-box;
    }
    .container_blog_inner{
        gap: 5rem;
        justify-content: center;
        display: flex;
    }
    .mainpost{
        width: 75%;
    }
    .sidebar{
        width: 25%;
    }
    .sidebar_link{
        text-decoration: none;
        color: var(--main_colour);
    }
    .post__title{
        color: var(--main_colour);
        font-size: 2rem;
    }
    .post__date{
        color: #fff;
    }
    .featured-image-wrapper{
        height: 30vh;
    }
    .featured-image-wrapper img{
        width: 100%;
        object-fit: cover;
        height: 100%;
        border-radius: .5rem;
    }
    .votes_insta{
        display: flex;
        justify-content:center;
        align-items:center;
        gap: 1rem;

        .votes, .insta{
            padding: .5rem 1rem;
            border-radius: .25rem;
            width: 6rem;
            text-align: center;
        }
        .votes{
            background: linear-gradient(90deg, rgba(9,255,154,1) 35%, rgba(103,255,193,1) 100%);
            color: #000;
        }
        .insta{
            background: #A555EC;
            color: #fff;
        }
        .insta a{
            text-decoration: none;
            color: #fff;
        }
}
    .post__meta {
        background: linear-gradient(180deg, rgba(9,255,154,1) 35%, rgba(103,255,193,1) 100%);
        padding: 1rem;
        border-radius: 5px;
        margin-top: 2rem;
        position: relative;
    }
    .post__meta_info{
        background: #424242;
        padding: 1rem;
        border-radius: 5px;
        color: #fff;
        margin-top: 2rem;
        position: relative;
    }
    .post__meta p {
        margin: 0.5rem 0;
    }
    .post__badge{
        position: absolute;
        background: #A555EC;
        color: #fff;
        top: -.5rem;
        left: 0;
        right: 0;
        width: fit-content;
        margin: auto;
        border-radius: .25rem;
        padding: .25rem 1rem;
    }
    .comments-area {
        margin-top: 2rem;
        padding: 1rem;
        border-radius: 5px;
    }
    .comments-title {
        font-size: 1.5rem;
        color: #ffffff;
    }
    .comment-list {
        list-style: none;
        padding: 0;
    }
    .comment{
        margin-bottom: 1rem;
        border-radius: 5px;
    }
    .comment-reply-link{
        color: #a555ec;
        text-decoration: none;
        padding: .25rem .5rem;
        border-radius: 1rem;
    }
    .reply{
        color: #a555ec;
    }
    .comment-meta{
        display: flex;
        gap: 1rem;
    }
    .comment_author a{
        text-decoration: none;
        color: #fff
    }
    .comment-content{
        color: #fff
    }
    .logged-in-as{
        color: #d0a7f5bd;
    }
    .comment-textarea-submit {
        display: flex;
    }
    .comment-textarea-submit textarea {
        width: 100%;
        resize: none;
        background: #4A4A4A;
        color: white;
        font-size: 16px;
        padding: 10px;
        font-family: system-ui;
    }
    .comment-textarea-submit textarea::placeholder {
        color: white; 
        font-size: 16px;
        opacity: 1;
    }
    .submit{
        height: 100%;
        width: 100%;
        padding: 2rem;
        background: #A555EC;
        border: none;
        color: #fff;
        cursor:pointer;
    }
    .avatar{
        border-radius: 10rem;
    }
    #reply-title {
        color: #fff;
        font-size: 1rem;
        font-weight: 400;
    }
    #reply-title a{
        color:#A555EC;
    }
    .comment-list {
        list-style-type: none;
        padding-left: 0;
    }
    .comment{
        list-style: none;
    }
    .sidebar-title{
        color: var(--white_tone);
        font-weight: bold;
    }
    .recent_parties{
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    @media(orientation: portrait){
        .featured-image-wrapper{
            height: 15vh;
        }
        .container_blog_inner{
            flex-direction: column;
        }
        .mainpost{
            width:100%;
        }
        .sidebar{
            width: 100%;
        }
    }
    @media (min-width: 576px) {
        .container_blog {
            max-width: 540px;
        }
    }
    @media (min-width: 768px) {
        .container_blog {
            max-width: 720px;
        }
    }
    @media (min-width: 992px) {
        .container_blog {
            max-width: 900px;
        }
    }
    @media (min-width: 1200px) {
        .container_blog {
            max-width: 1100px;
        }
    }
</style>
<main class="main mt-xl-3" role="main">
    <div class="container_blog">
        <div class="container_blog_inner">
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
						
                        <div class="votes_insta">
                            <div class="votes">
                                <strong>Vote:</strong> <?php echo esc_html(get_post_meta(get_the_ID(), 'vote', true)); ?>
                            </div>
                            <div class="insta">
                                <a href="https://www.instagram.com/<?php echo esc_html(get_post_meta(get_the_ID(), 'insta', true)); ?>">
                                    Ver no Insta
                                </a>
                            </div>
                        </div>
                        <div class="post__meta">
                            <div class="post__badge">
                                Informações
                            </div>
                            <p><strong>Data:</strong> <?php echo esc_html(get_post_meta(get_the_ID(), 'date', true)); ?></p>
                            <p><strong>Local:</strong> <?php echo esc_html(get_post_meta(get_the_ID(), 'location', true)); ?></p>
                            <p><strong>Preço:</strong> <?php echo esc_html(get_post_meta(get_the_ID(), 'price', true)); ?></p>
                        </div>
                        <div class="post__meta_info">
                            <div class="post__badge">
                                Lotes
                            </div>
                            <p><strong>Lote Promo:</strong> <?php echo esc_html(get_post_meta(get_the_ID(), 'lote_1', true)); ?></p>
                            <p><strong>Primeiro lote:</strong> <?php echo esc_html(get_post_meta(get_the_ID(), 'lote_2', true)); ?></p>
                            <p><strong>Segundo lote:</strong> <?php echo esc_html(get_post_meta(get_the_ID(), 'lote_3', true)); ?></p>
                            <p><strong>Terceiro lote:</strong> <?php echo esc_html(get_post_meta(get_the_ID(), 'lote_4', true)); ?></p>
                        </div>
                        <div class="post__content">
                            <?php the_content(); ?>
                        </div>
                        <div class="post__comments">
                            <?php 
                            if (comments_open() || get_comments_number()) {
                                comments_template();
                            }
                            ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <div class="sidebar">
                <div>
                    <p class="sidebar-title">Festas Recentes</p>
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
                                        <div class="recent_parties">
                                            <?php if(has_post_thumbnail()): ?>
                                                <div class="img-wrapper_sixpack_sidebar">
                                                    <div style="border-radius:.5rem" class="img-wrapper_thumbnail">
                                                        <?php the_post_thumbnail( array(40, 40) ); ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <header class="post__header px-1" role="heading">
                                                <p class="recent-posts-title">
                                                    <a class="sidebar_link bold" href="<?php the_permalink(); ?>">
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