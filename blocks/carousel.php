<style>
    /**  custom glide css  **/
    .carousel{
        position:relative;
    }
    .carousel-container {
        position: relative;
        padding: 3rem 0;
        z-index: 1;
    }
    .carousel_frame{
        border-radius:1rem;
        display:flex;
        justify-content:center;
    }
    .slide_glide_content {
        margin-top: -4.3rem;
        position: relative;
        padding: 1rem;
        color: #fff;
        font-size: 1.7rem;
        font-weight: 700;
        text-align: left;
    }
    .custom_slider_content{
        width: 35%;
        display: flex;
        align-items: center;
    }
    .custom_slider_slide{
        width: 65%
    }
    .custom_slider_slide_alone{
        width:100%;
    }
    .img_background{
        object-fit: cover;
        width:100%;
        height:100%;
        top: 0;
        left:0;
        right:0;
        bottom:0;
        position:absolute;
    }
    /**  library css  **/
    .carousel .glide {
        position: relative;
        width: 100%;
        box-sizing: border-box;
    }
    .carousel .glide * {
        box-sizing: inherit;
    }
    .carousel .glide__track {
        overflow: hidden;
    }
    .carousel .glide__slides {
        position: relative;
        width: 100%;
        list-style: none;
        backface-visibility: hidden;
        transform-style: preserve-3d;
        touch-action: pan-Y;
        overflow: hidden;
        padding: 0;
        white-space: nowrap;
        display: flex;
        flex-wrap: nowrap;
        will-change: transform;
    }
    .carousel .glide__slides--dragging {
        user-select: none;
    }
    .carousel .glide__slide {
        overflow:hidden;
        width: 100%;
        flex-shrink: 0;
        white-space: initial;
        user-select: none;
        -webkit-touch-callout: none;
        -webkit-tap-highlight-color: transparent;
    }
    .carousel .glide__slide a {
        text-decoration: none;
        user-select: none;
        -webkit-user-drag: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }
    .carousel .glide__arrows {
        -webkit-touch-callout: none;
        user-select: none;
    }
    .carousel .glide__bullets {
        -webkit-touch-callout: none;
        user-select: none;
    }
    .carousel .glide--rtl {
        direction: rtl;
    }
    .carousel .glide {
        cursor: -webkit-grab;
        width: 100%;
        margin: auto;
    }
    .slider__arrow--prev {
        left: 1.5rem;
    }
    .fas {
        right: 5px;
        position: absolute;
        top: 6px;
    }
    .slider__arrow--next {
        right: 1.5rem;
    }
    .slider__arrow {
        position: absolute;
        top: 50%;
        z-index: 1;
        width: 24px;
        height: 24px;
        margin-top: -9px;
        border-radius: 50%;
        background-color: #454545;
        -webkit-transition: all 0.2s ease-in-out;
        transition: all 0.2s ease-in-out;
        -webkit-box-shadow: 0 0.5rem 4rem 0 rgba(0, 0, 0, 0.5);
        box-shadow: 0 0.5rem 4rem 0 rgba(0, 0, 0, 0.5);
    }
    .slider__arrow--prev {
        left: 1.5rem;
    }
    .slider__frame[class*=active] {
        -webkit-box-shadow: none;
        box-shadow: none;
        transition: background 0.1s ease-in;
    }
    .glide__arrows {
        position: relative;
        z-index: 2;
        margin: 1rem;
    }
    .glide__arrow--prev{
        position:absolute;
        left: 0;
        top: 0;
        bottom: 0;
        pointer-events: auto;
    }
    .glide__arrow--next{
        position:absolute;
        right: 0;
        top: 0;
        bottom: 0;
        pointer-events: auto;
    }
    .glide__arrow {
        border: none;
        background: #ffffff00;
        cursor: pointer;
        z-index: 2;
    }
    .glide__arrow:hover {
        transform: scale(1.1);
        transition: ease-in 0.2s;
    }
    .glide_controls{
        display:block;
    }

    li.carousel_frame.glide__slide a{
        max-width: 5rem;
        display: flex;
    }
    li.carousel_frame.glide__slide a img{
        object-fit:cover;
        width:100%;
        height:100%;
    }
    @media(orientation: portrait){
        li.carousel_frame.glide__slide a{
            max-width: 10rem;
            display: flex;
        }
        li.carousel_frame.glide__slide a img{
            object-fit:cover;
            width:100%;
            height:100%;
        }
        .glide_controls{
            display:block;
        }
        .slide_glide_content {
            font-size: 2rem;
        }
        .custom_slider_slide{
            width: unset;
        }
        .carousel_container_mb{
            flex-direction: column
        }
        .custom_slider_content{
            width:100%;
        }
    }
</style>

<?php $useGlobalCarousel = get_sub_field('use_global_carousel');?>
<?php if ($useGlobalCarousel && in_array('use_global', $useGlobalCarousel)): ?>
    <section class="carousel">
        <?php 
        $content = get_sub_field('content');
        $howManySlidesCarousel = get_sub_field('slide_number');
        $bg_image = get_sub_field('bg_img');
        ?>
    
        <div class="carousel-container">
            <?php if(have_rows('carousel_images','option')):?>
                <div class="carousel_container_mb container d-flex gap-1">
                    <?php if($content): ?>
                        <div class="custom_slider_content heading_raw">
                                <div>
                                    <?php echo $content ?>
                                </div>
                        </div>
                    <?php endif; ?>
                    <div class="custom_slider_slide <?php if (!$content) { echo 'custom_slider_slide_alone'; } ?>">
                        <div class="carousel_glide main-carousel slider glide glide_carousel glide--ltr glide--carousel glide--swipeable">
                            
                            <div class="glide_controls" data-glide-el="controls">
                                <button class="glide__arrow glide__arrow--prev" data-glide-dir="<">
                                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="16.1486" cy="15.9245" r="15.7993" fill="#636364"/><path d="M18.3707 20.3687L13.9266 15.9246L18.3707 11.4805" stroke="white" stroke-width="1.5"/></svg>
                                </button>
                            </div>
                            
                            <div class="slider__track glide__track" data-glide-el="track">
                                <ul class="slider__slides glide__slides">
                                    <?php while(have_rows('carousel_images','option')) : the_row(); ?>
                                        <li class="carousel_frame glide__slide">
                                            <?php
                                            if (get_sub_field('image') || get_sub_field('text') || get_sub_field('link')):
                                                $image = get_sub_field('image');
                                                $image_url = $image['url'];
                                                $image_alt = $image['alt'];
                                                $text = get_sub_field('text');
                                                $link = get_sub_field('link');
                                            ?>
                                                <?php if($link):?>
                                                    <a href="<?php echo esc_url($link['url']);?>">
                                                <?php endif;?>
                                                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>">
                                                        <?php if($text):?>
                                                            <div class="slide_glide_content">
                                                                <?php echo $text; ?>
                                                            </div>
                                                        <?php endif;?>
                                                <?php if($link):?>
                                                    </a>
                                                <?php endif;?>
                                            <?php endif; ?>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                            
                            <div class="glide_controls" data-glide-el="controls">
                                <button class="glide__arrow glide__arrow--next" data-glide-dir="&gt;">
                                    <svg width="33" height="32" viewBox="0 0 33 32" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="16.2477" cy="15.9245" r="15.7993" fill="#636364"/><path d="M14.0257 20.3687L18.4698 15.9246L14.0257 11.4805" stroke="white" stroke-width="1.5"/></svg>
                                </button>
                            </div>
                            
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php if($bg_image):?>
            <img loading="lazy" class="img_background" src="<?php echo $bg_image['url']?>" alt="<?php echo $bg_image['alt']?>"/>
        <?php endif ?>
    </section>
<?php else: ?>
    <section class="carousel">
        <?php 
        $content = get_sub_field('content');
        $howManySlidesCarousel = get_sub_field('slide_number');
        $bg_image = get_sub_field('bg_img');
        ?>
    
        <div class="carousel-container">
            <?php if(have_rows('carousel_images')):?>
                <div class="carousel_container_mb container d-flex gap-1">
                    <?php if($content): ?>
                        <div class="custom_slider_content heading_raw">
                                <div>
                                    <?php echo $content ?>
                                </div>
                        </div>
                    <?php endif; ?>
                    <div class="custom_slider_slide <?php if (!$content) { echo 'custom_slider_slide_alone'; } ?>">
                        <div class="main-carousel carousel_glide slider glide glide_carousel glide--ltr glide--carousel glide--swipeable">
                            
                            <div class="glide_controls" data-glide-el="controls">
                                <button class="glide__arrow glide__arrow--prev" data-glide-dir="<">
                                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="16.1486" cy="15.9245" r="15.7993" fill="#636364"/><path d="M18.3707 20.3687L13.9266 15.9246L18.3707 11.4805" stroke="white" stroke-width="1.5"/></svg>
                                </button>
                            </div>
                            
                            <div class="slider__track glide__track" data-glide-el="track">
                                <ul class="slider__slides glide__slides">
                                    <?php while(have_rows('carousel_images')) : the_row(); ?>
                                        <li class="carousel_frame glide__slide">
                                            <?php
                                            if (get_sub_field('image') || get_sub_field('text') || get_sub_field('link')):
                                                $image = get_sub_field('image');
                                                $image_url = $image['url'];
                                                $image_alt = $image['alt'];
                                                $text = get_sub_field('text');
                                                $link = get_sub_field('link');
                                            ?>
                                                <?php if($link):?>
                                                    <a href="<?php echo esc_url($link['url']);?>">
                                                <?php endif;?>
                                                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>">
                                                        <?php if($text):?>
                                                            <div class="slide_glide_content">
                                                                <?php echo $text; ?>
                                                            </div>
                                                        <?php endif;?>
                                                <?php if($link):?>
                                                    </a>
                                                <?php endif;?>
                                            <?php endif; ?>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                            
                            <div class="glide_controls" data-glide-el="controls">
                                <button class="glide__arrow glide__arrow--next" data-glide-dir="&gt;">
                                    <svg width="33" height="32" viewBox="0 0 33 32" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="16.2477" cy="15.9245" r="15.7993" fill="#636364"/><path d="M14.0257 20.3687L18.4698 15.9246L14.0257 11.4805" stroke="white" stroke-width="1.5"/></svg>
                                </button>
                            </div>
                            
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php if($bg_image):?>
            <img loading="lazy" class="img_background" src="<?php echo $bg_image['url']?>" alt="<?php echo $bg_image['alt']?>"/>
        <?php endif ?>
    </section>
<?php endif; ?>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const slidersCarousel = document.querySelectorAll('.carousel_glide'); // NodeList of elements
		slidersCarousel.forEach(item => {
			const conf = {
				type: 'carousel',
				focusAt: 'center',
				perView: <?php echo $howManySlidesCarousel ?>,
				animationDuration: 300,
				autoplay: false,
				hoverpause: true,
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