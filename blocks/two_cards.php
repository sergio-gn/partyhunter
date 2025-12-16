<?php 
    if (have_rows('two_cards')) :
        while (have_rows('two_cards')) : the_row();
            $image = get_sub_field('image');
            $titleCard1 = get_sub_field('title_card_1');
            $contentCard1 = get_sub_field('content_card_1');
            $titleCard2 = get_sub_field('title_card_2');
            $contentCard2 = get_sub_field('content_card_2');
            $bgcolour = get_sub_field('bg_colour');
            $bgcolour2 = get_sub_field('bg_colour_2');
        endwhile;
    endif;
?>
<style>
    .front-two_cards_card{
        justify-content: space-between;
        display: flex;
        overflow-wrap: break-word;
        overflow:hidden;
    }
    .two_cards_1_content{
        padding: 2rem;
    }
    .two_cards_1,.two_cards_1_content{
        width:65%;
    }
    .two_cards_2,.two_cards_1_img_wrapper{
        width:35%;
    }
    .two_cards_1_img_wrapper{
        height:100%;
    }
    .two_cards_1_img_wrapper img{
        object-fit: cover;
        width: 100%;
        height: 100%;
    }
    @media(orientation: portrait){
        .front-two_cards{
            margin-top: unset;
        }
        .front-two_cards_card,.two_cards_1,.two_cards_1_content,.two_cards_1_img_wrapper,.two_cards_2{
            width:unset;
        }
        .two_cards_card_image{
            flex-direction:column;
        }
    }
</style>

<section class="d-flex_newcss justify-center_newcss blocks-padding">
    <div class="container">
        <div class="front-two_cards d-flex_newcss flex-d-column_mb justify-space-around gap_1">
            <div class="front-two_cards_card two_cards_1 b-radius-1 <?php echo $bgcolour;?>">
                <div class="two_cards_card_image d-flex align_center_blocks">
                    <div class="two_cards_1_content">
                        <?php if($contentCard1): ?>
                            <?php echo $contentCard1; ?>
                        <?php endif;?>
                        <?php if (!empty($button) && isset($button['url']) && isset($button['title'])): ?>
                            <a href="<?php echo esc_url($button['url']); ?>" class="cta_button">
                                <?php echo esc_html($button['title']); ?>
                            </a>
                        <?php endif;?>
                    </div>
                    <div class="two_cards_1_img_wrapper">
                        <?php if($image): ?>
                            <img loading="lazy" class="d-flex" alt="<?php echo $image['alt']; ?>" src="<?php echo $image['url']; ?>">
                        <?php endif;?>
                    </div>
                </div>
            </div>
            <div class="front-two_cards_card two_cards_2 b-radius-1 <?php echo $bgcolour2 ?>">
                <div class="fc-5 py-2">
                    <?php if($contentCard2): ?>
                        <?php echo $contentCard2; ?>
                    <?php endif;?>
                    <?php if (!empty($button) && isset($button['url']) && isset($button['title'])):?>
                        <a href="<?php echo esc_url($button['url']); ?>" class="cta_button">
                            <?php echo esc_html($button['title']); ?>
                        </a>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
</section>
