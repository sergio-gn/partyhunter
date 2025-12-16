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
    .repeater_zigzag_container{
        padding: 2rem;
    }
    .repeater_zigzag p{
        padding:1rem 0;
        font-size: 1rem;
        margin: 0;
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
<?php if( have_rows('double_wysiwyg') ): ?>
    <?php while( have_rows('double_wysiwyg') ): the_row(); 
        $contentOne = get_sub_field('content_1');
        $contentTwo = get_sub_field('content_2');
        $order = get_sub_field('order');
        $bgColour = get_sub_field('bg_colour');
        $padding = get_sub_field('padding');
    ?>
        <section class="repeater_zigzag d-flex_newcss justify-center_newcss <?php if($padding){echo 'blocks-padding';} ?> <?php echo $bgColour; ?>">
            <div class="repeater_zigzag_container container" <?php if(!$padding){echo 'style="padding:0"';} ?>>
                <div class="d-flex_newcss fd-column-r_mb vthz-center gap_3 <?php echo $order ?>">
                    <div class="flex-1">
                        <div class="front-zig zig_content">
                            <?php echo $contentOne ?>
                        </div>
                    </div>
                    <?php if ($order !== 'order-center'): ?>
                        <div class="front-zag flex-1 img_wrapper_repeater_zigzag">
                            <div class="front-zig zig_content">
                                <?php echo $contentTwo ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endwhile; ?>
<?php endif; ?>
