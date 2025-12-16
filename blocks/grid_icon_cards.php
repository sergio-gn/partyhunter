<style>
    .gap_1{
        gap: 1rem;
    }
    .grid_text h1,.grid_text h2,.grid_text h3 {
        font-weight: 900;
        font-size: 2.5rem;
        color:#000;
        margin:0;
    }
    .grid_text h4,.grid_text h5,.grid_text h6{
        font-weight: 900;
        color: var(--main_colour);
        font-size: 1.2rem;
    }
    .grid_text p{
        font-size:1.1rem;
    }
    .text_card_img{
        padding: 1rem 1rem 0.5rem 1rem;
    }
    .text_card_icon{
        padding: 0.5rem 1rem 1rem 1rem;
    }
    .text_card_icon h1,.text_card_icon h2,.text_card_icon h3 {
        font-weight: 900;
        font-size: 1.6rem;
        color:var(--main_colour);
        margin:0;
    }
    .text_card_icon h4,.text_card_icon h5,.text_card_icon h6{
        font-weight: 900;
        color: var(--main_colour);
        font-size: 1.2rem;
    }
    .text_card_icon p{
        font-size:1.1rem;
    }
    .front-grid{
        padding: 2rem 0;
    }
    .front-grid_card-icon-wrap {
        position: relative;
        overflow: hidden;
        height: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.25rem;
    }
    .grid-square_icon {
        position: relative;
        padding: 0 1rem;
        background: #fff;
        align-items: center;
        border-radius: 1rem;
        gap:0.5rem;
            box-shadow: 0px 12px 18px -6px rgba(0, 0, 0, 0.3);
            
    }
    .grid-square_icon a{
        text-decoration: none;
    }
	.grid-square_icon p{
		color:var(--grey_tone);
        text-align:left;
	}
	.grid_square_icon_background_primary{
	    background-color: var(--main_colour);
	            border: 2px solid var(--main_colour);
	}
	.grid_square_icon_background_primary:hover{
	            border: 2px solid var(--white_tone);
	}
	.grid_square_icon_background_secondary{
	    background-color: var(--white-tone);
	            border: 2px solid var(--white_tone);
	}
	.grid_square_icon_background_secondary:hover{
	        border: 2px solid var(--main_colour);
	}
    .gridtitle h1, .gridtitle h2, .gridtitle h3, .gridtitle h4, .gridtitle h5, .gridtitle h6{
        color:#000;
        font-size: 1.2rem;
        font-weight:800;
    } 
    .view_more_button{
        margin: 1rem;
        background: var(--main_colour);
        font-size: 17px;
        padding: 16px 30px;
        color: #fff;
        cursor: pointer;
        border:none;
    }
    .view_more_button:hover{
        background: var(--main_colour_darker);
    }
    .front-grid-2{
        grid-template-columns: 1fr 1fr;
    }
    .front-grid-3{
        grid-template-columns: 1fr 1fr 1fr;
    }
    .front-grid-4{
        grid-template-columns: 1fr 1fr 1fr 1fr;
    }
    .front-grid-5{
        grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
    }

    @media(orientation: portrait){
        .front-grid_mb{
            grid-template-columns: 1fr;
        }
    }
</style>
<?php $bgcolour = get_sub_field('bg_colour'); ?>
<section style="background:<?php echo $bgcolour; ?>" class="front-grid d-flex_newcss justify-center_newcss">
    <div class="container">
        <div class="grid_text">
            <?php echo get_sub_field('grid_text')?>
        </div>
        <?php $gridNumber = get_sub_field('grid_column_number');?>
        <?php if( have_rows('grid_images') ): ?>
            <div class="front-grid-<?php echo $gridNumber; ?> front-grid_mb d-grid_newcss gap_1">
                <?php $count = 0; ?>
                <?php while( have_rows('grid_images') ): the_row(); 
                    $link = get_sub_field('link');
                    $image = get_sub_field('image');
                    $gridContent = get_sub_field('content');
                    $backgroundColour = get_sub_field('background_colour');
                    $countLimit = 10;
                ?>
                    <div class="grid-square_icon <?php if ($count >= $countLimit) echo 'extra'; ?> grid_square_icon_<?php echo $backgroundColour?>" style="<?php if ($count >= 6) echo 'display:none;'; ?>">
                        <?php if (!empty($link) && isset($link['url'])): ?>
                            <a href="<?php echo esc_url($link['url']); ?>">
                        <?php endif; ?>
                        <div class="text_card_img d-flex_newcss justify-center_newcss align_center_blocks">
                            <?php if (!empty($image) && isset($image['url'])): ?>
                                <div class="front-grid_card-icon-wrap">
                                    <img class="img__thumb" loading="lazy" src="<?php echo $image['url'] ?>" alt="<?php echo $image['alt'] ?>">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="text_card_icon text-center">
                            <?php echo $gridContent ?>
                        </div>
                        <?php if (!empty($link) && isset($link['url'])): ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php $count++; ?>
                <?php endwhile; ?>
                <!-- View More Button -->
            </div>
            <?php if ($count >= $countLimit): ?>
            <div class="d-flex_newcss justify-center_newcss">
                <button class="view_more_button" onclick="toggleExtraGrid()">View More</button>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <script>
        function toggleExtraGrid() {
            var extraGrid = document.querySelectorAll('.grid-square_icon.extra');
            extraGrid.forEach(function(grid) {
                grid.style.display = (grid.style.display === 'none' || grid.style.display === '') ? 'block' : 'none';
            });
        }
    </script>


<?php endif; ?>
