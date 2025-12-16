<style>
    .faq_blocks{
        padding: 2rem;
    }
    .accordion {
        display: flex;
        flex-direction: column;
        width: 75%;
        margin:auto;
        height: auto;
        box-sizing: border-box;
        margin: 2rem auto;
    }
    .accordion .a-container {
        display: flex;
        flex-direction: column;
        width: 100%;
        padding-bottom: 5px;
    }
    .accordion .a-container .a-btn {
        margin: 0;
        position: relative;
        padding: 15px 30px;
        color: var(--white_tone);
        font-weight: 400;
        display: block;
        font-weight: 500;
        background-color: var(--black_tone);
        cursor: pointer;
        transition: all 0.3s ease-in-out;
        border-radius: 10rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, .15), 0 10px 10px -5px rgba(0, 0, 0, .1) !important;
    }
    .accordion .a-container .a-btn:hover{
        color:var(--white_tone);
    }
    .accordion .a-container .a-btn span {
        display: block;
        position: absolute;
        height: 14px;
        width: 14px;
        right: 20px;
        top: 18px;
    }
    .accordion .a-container .a-btn span:after {
        content: '';
        width: 14px;
        height: 3px;
        border-radius: 2px;
        background-color: var(--main_colour);
        position: absolute;
        top: 6px;
    }
    .accordion .a-container .a-btn span:before {
        content: '';
        width: 14px;
        height: 3px;
        border-radius: 2px;
        background-color: var(--main_colour);
        position: absolute;
        top: 6px;
        transform: rotate(90deg);
        transition: all 0.3s ease-in-out;
    }
    .accordion .a-container .a-panel {
        background: #f8f8f8;
        width: 100%;
        color: #484848;
        transition: all 0.2s ease-in-out;
        opacity: 0;
        height: auto;
        max-height: 0;
        overflow: hidden;
        padding: 0px 10px;
        border-width: 1px;
        border-color: #f0f0f0;
    }
    .accordion .a-container.active .a-btn {
        color:var(--white_tone);
    }
    .accordion .a-container.active .a-btn span::before {
        transform: rotate(0deg);
    }
    .accordion .a-container.active .a-panel {
        padding: 15px 10px 10px 10px;
        opacity: 1;
        box-sizing: border-box;
        max-height: 500px;
    }
    .faq_text{
        margin: auto;
    }
    .faq_text h2{
        font-size: 2.5rem;
        font-weight: 800;
        color:var(--black_tone);
    }
    .accordion .a-container:nth-child(n+8) {
        display: none;
    }
    
    /* Style the Show More button */
    #show-more-btn {
        display: block;
        margin: 20px auto;
        padding: 10px 20px;
        background-color: var(--main_colour);
        color: var(--white_tone);
        border: none;
        cursor: pointer;
        border-radius: 10rem;
    }
    
    #show-more-btn.show-less {
        background-color: var(--main_colour);
        color: var(--white_tone);
    }
    @media(orientation: portrait){
        .accordion{
            width: 100%;
        }
    }
</style>

<?php $use_global_faq = get_sub_field('use_global_faq');?>
<section class="faq_blocks container">
    <div class="faq_text">
        <?php 
            $faqGlobal = get_field('faq_text', 'option');
            $faq = get_sub_field('faq_text');
            if($use_global_faq && in_array('use_global', $use_global_faq)){
                echo $faqGlobal;
            }else{
                echo $faq;
            }
        ?>
    </div>
    <div class="accordion v1">
        <?php 
        $accordion_count = 0; // Initialize counter
    
        if ($use_global_faq && in_array('use_global', $use_global_faq)): ?>
            <?php if (have_rows('repeater_faq_global', 'option')): ?>
                <?php while (have_rows('repeater_faq_global', 'option')): the_row(); ?>
                    <div class="a-container">
                        <p class="a-btn"><?php the_sub_field('question'); ?><span></span></p>
                        <div class="a-panel">
                            <p><?php the_sub_field('answer'); ?></p>
                        </div>
                    </div>
                    <?php $accordion_count++; // Increment counter ?>
                <?php endwhile; ?>
            <?php endif; ?>
        <?php else: ?>
            <?php if (have_rows('repeater_faq')): ?>
                <?php while (have_rows('repeater_faq')): the_row(); ?>
                    <div class="a-container">
                        <p class="a-btn"><?php the_sub_field('question'); ?><span></span></p>
                        <div class="a-panel">
                            <p><?php the_sub_field('answer'); ?></p>
                        </div>
                    </div>
                    <?php $accordion_count++; // Increment counter ?>
                <?php endwhile; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <?php if ($accordion_count > 7): // Show button only if there are more than 7 accordions ?>
        <button id="show-more-btn" class="show-more">Show More</button>
    <?php endif; ?>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var showMoreBtn = document.getElementById('show-more-btn');
    var accordions = document.querySelectorAll('.accordion .a-container');
    
    if (showMoreBtn) {
        // Hide extra accordions
        for (var i = 7; i < accordions.length; i++) {
            accordions[i].style.display = 'none';
        }

        showMoreBtn.addEventListener('click', function() {
            var isShowingMore = this.classList.toggle('show-less');

            if (isShowingMore) {
                // Show all accordions
                for (var i = 7; i < accordions.length; i++) {
                    accordions[i].style.display = 'block';
                }
                this.textContent = 'Show Less';
            } else {
                // Hide extra accordions
                for (var i = 7; i < accordions.length; i++) {
                    accordions[i].style.display = 'none';
                }
                this.textContent = 'Show More';
            }
        });
    }
});

    function initAcc(elem, option){
    document.addEventListener('click', function (e) {
        if (!e.target.matches(elem+' .a-btn')) return;
        else{
            if(!e.target.parentElement.classList.contains('active')){
                if(option==true){
                    var elementList = document.querySelectorAll(elem+' .a-container');
                    Array.prototype.forEach.call(elementList, function (e) {
                        e.classList.remove('active');
                    });
                }            
                e.target.parentElement.classList.add('active');
            }else{
                e.target.parentElement.classList.remove('active');
            }
        }
    });
    }
initAcc('.accordion.v1', true);
</script>
