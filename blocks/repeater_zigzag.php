<style>
    .repeater_zigzag h1, .repeater_zigzag h2, .repeater_zigzag h3, .repeater_zigzag h4, .repeater_zigzag h5, .repeater_zigzag h6 {
        font-size: 2rem;
        color: var(--main_colour);
    }
    .repeater_zigzag h1 strong, .repeater_zigzag h2 strong, .repeater_zigzag h3 strong, .repeater_zigzag h4 strong, .repeater_zigzag h5 strong, .repeater_zigzag h6 strong {
        font-size: 3rem;
        font-weight: 900;
        background: -webkit-linear-gradient(var(--main_colour), var(--main_colour_darker));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        line-height: 1;
    }
    .repeater_zigzag p{
        padding:1rem 0;
        font-size: 1rem;
        margin: 0;
    }
    .repeater_zigzag.dark p{
        color: var(--white_tone);
    }
    .repeater_zigzag.dark li{
        color: var(--white_tone);
    }
    .repeater_zigzag li{
        list-style-image: url('data:image/svg+xml,<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M4 12.6111L8.92308 17.5L20 6.5" stroke="%2300aeef" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></g></svg>');
    }
    .img_wrapper_repeater_zigzag{
        overflow:hidden;
        max-height: 40rem;
    }
    .img_wrapper_repeater_zigzag_imgtag{
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 1rem;
    }
    @media(orientation: portrait){
        .img_wrapper_repeater_zigzag{
            height: 20rem;
            flex: unset;
        }
    }
</style>
<?php if( have_rows('repeater_zigzag') ): ?>
    <?php while( have_rows('repeater_zigzag') ): the_row(); 
        $image = get_sub_field('image');
        $video = get_sub_field('video');
        $content = get_sub_field('content');
        $link = get_sub_field('link');
        $order = get_sub_field('order');
        $bgColour = get_sub_field('bg_colour');
        $padding = get_sub_field('padding');
    ?>
        <section class="repeater_zigzag d-flex_newcss justify-center_newcss <?php if($padding){echo $padding;} ?> <?php echo $bgColour; ?>">
            <div class="repeater_zigzag_container container">
                <div class="d-flex_newcss fd-column-r_mb vthz-center gap_3 <?php echo $order ?>">
                    <div class="flex-1">
                        <div class="front-zig zig_content">
                            <?php echo $content ?>
                        </div>
                        <?php
                            if (!empty($link) && isset($link['url']) && isset($link['title'])) {
                                ?>
                                <a href="<?php echo esc_url($link['url']); ?>" class="cta_button">
                                    <?php echo esc_html($link['title']); ?>
                                </a>
                                <?php
                            }
                        ?>
                    </div>
                    <?php if ($order !== 'order-center'): ?>
                        <div class="front-zag flex-1 img_wrapper_repeater_zigzag">
                            <?php if ($video): ?>
                                <div class="video-container">
                                    <video video controls muted loop preload="none" class="lazyVideo w-100_newcss" src="<?php echo $video['url']; ?>"></video>
                                </div>
                            <?php elseif($image): ?>
                                <img loading="lazy" class="w-100_newcss img_wrapper_repeater_zigzag_imgtag" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endwhile; ?>
<?php endif; ?>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    var lazyVideos = document.getElementsByClassName("lazyVideo");
    var options = {
      rootMargin: "50px 0px", // Adjust the rootMargin based on your layout
      threshold: 0.01 // Lower threshold to trigger intersection when more of the video is visible
    };
    var observer = new IntersectionObserver(function(entries, observer) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          entry.target.play();
          observer.unobserve(entry.target);
        }
      });
    }, options);
    for (var i = 0; i < lazyVideos.length; i++) {
      observer.observe(lazyVideos[i]);
    }
  });
</script>
