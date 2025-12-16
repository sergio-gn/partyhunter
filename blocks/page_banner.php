<style>
    .container_banner{
        padding: 0 2rem;
    }
    .homepage_hero{
        display: flex;
        align-items: center;
        position: relative;
        background-position: bottom;
    }
    .sidepage_hero{
        width: 100%;
        position:relative;
        display: flex;
        align-items: center;
    }
    .banner_small{
        height: 40vh;
    }
    .banner_medium{
        height: 60vh;
    }
    .banner_large{
        height: 80vh;
    }
    .center_title{
    	position:relative;
        z-index:1;
    }
    .center_title_sidebar{
        top: unset;
        position: relative;
        z-index: 1;
    }
    .banner-text {
        width: 50vw;
        color: var(--white_tone);
    }
    .hero_img_background{
        object-fit: cover;
        width:100%;
        top: 0;
        left:0;
        right:0;
        bottom:0;
        position:absolute;
        z-index: -1;
    }
    .banner-text h1{
        font-size:4rem;
        line-height: 1;
        margin: 1rem 0;
        color: var(--white_tone);
    }
    .banner-text h1 strong{
        background: -webkit-linear-gradient(var(--font_gradient_left), var(--font_gradient_right));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    @media(max-width:1300px){
        .banner-text h1{
            font-size: 2.5rem;
        }
        .homepage_hero{
            margin-top:0;
            height:40vh;
        }
    }
    @media(max-width:798px){
        .banner-text{
            width:100%;
        }
    }
</style>
<?php if (have_rows('page_banner')) :
    while (have_rows('page_banner')) : the_row();
        $content = get_sub_field('content');
        $link = get_sub_field('link');
        $image = get_sub_field('image');
        $bannerSize = get_sub_field('banner_size');
?>
        <section class="<?php echo $bannerSize ?> <?php echo is_front_page() ? 'homepage_hero' : 'sidepage_hero'; ?>">
            <div class="container">
                <div class="<?php echo is_front_page() ? 'center_title' : 'center_title_sidebar'; ?>">
                    <div class="banner-text">
                        <?php echo $content ?>
                    </div>
                </div>
                <img class="hero_img_background" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>">
            </div>    
        </section>
<?php
    endwhile;
endif;
?>
