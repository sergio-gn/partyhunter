<style>
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
    .text_card h1,.text_card h2,.text_card h3 {
        font-weight: 900;
        font-size: 1.6rem;
        color:#000;
        margin:0;
    }
    .text_card h4,.text_card h5,.text_card h6{
        font-weight: 900;
        color: var(--main_colour);
        font-size: 1.2rem;
    }
    .text_card p{
        font-size:1.1rem;
    }
    .front-grid{
        padding: 2rem 0;
    }
    .front-grid_card-img-wrap{
        position: relative;
        overflow: hidden;
            max-width: 8rem;
        border-radius: 2rem;
        height: auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .grid-square{
        border: 4px double #ccc;
        position:relative;
        padding: 1rem;
        background: #fff;
        border-radius: 1rem;
    }
    .grid-square:hover{
        border: 4px solid var(--main_colour);
    }
	.grid-square p{
		color:#000;
	}
    .gridtitle h1, .gridtitle h2, .gridtitle h3, .gridtitle h4, .gridtitle h5, .gridtitle h6{
        color:#000;
        font-size: 1.2rem;
        font-weight:800;
    } 
    .view-more-button{
        margin: 1rem;
        background: var(--main_colour);
        font-size: 17px;
        padding: 16px 30px;
        color: #fff;
        cursor: pointer;
        border:none;
    }
    .view-more-button:hover{
        background: var(--main_colour);
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

<section class="front-grid d-flex_newcss justify-center_newcss">
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
                    $gridTitle = get_sub_field('title');
                ?>
                    <div class="grid-square <?php if ($count >= 6) echo 'extra'; ?>" <?php if ($count >= 6) echo 'style="display:none;"'; ?>>
                        <?php if (!empty($link) && isset($link['url'])): ?>
                            <a href="<?php echo esc_url($link['url']); ?>">
                        <?php endif; ?>
                        <div class="d-flex_newcss justify-center_newcss align_center_blocks">
                            <?php if (!empty($image) && isset($image['url'])): ?>
                                <div class="front-grid_card-img-wrap">
                                    <img class="img__thumb" loading="lazy" src="<?php echo $image['url'] ?>" alt="<?php echo $image['alt'] ?>">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="text_card text-center">
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
            <?php if ($count >= 6): ?>
            <div class="d-flex_newcss justify-center_newcss">
                <button class="view-more-button" onclick="toggleExtraGrid()">View More</button>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <script>
        function toggleExtraGrid() {
            var extraGrid = document.querySelectorAll('.grid-square.extra');
            extraGrid.forEach(function(grid) {
                grid.style.display = (grid.style.display === 'none' || grid.style.display === '') ? 'block' : 'none';
            });
        }
    </script>


<?php endif; ?>
