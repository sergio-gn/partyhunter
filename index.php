<?php
/**
 * Blogs 
 **/
get_header();

if ( ! wp_is_mobile() ) {
    get_template_part( 'parts/navigation' );
}

?>
<style>
    .blog_title{
        padding-top: 2rem;
        padding-bottom:2rem;
        background: var(--main_colour);
        color: var(--white_tone);
    }
    .blog_link{
        color: var(--main_colour);
        text-decoration: none;
    }
    .blog_space{
        padding-top: 2rem;
        padding-bottom: 2rem;
    }
    .blog_grid{
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
    }
    .post__header {
        border: 2px solid #ccc;
        position: relative;
        padding: 0 1rem;
        background: #fff;
        align-items: center;
        border-radius: 1rem;
        gap: 0.5rem;
        box-shadow: 0px 12px 18px -6px rgba(0, 0, 0, 0.3);
    }
    .post__header:hover {
        border: 2px solid var(--main_colour);
    }
    .page-title{
        font-size: 2.5rem;
    }
    @media(orientation: portrait){
        .blog_grid{
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
        }
    }
</style>
<?php if ( is_home() && ! is_front_page() && ! empty( single_post_title( '', false ) ) ) : ?>
    <section class="blog_title">
    	<header class="container">
    		<h1 class="page-title"><?php single_post_title(); ?></h1>
    	</header>
    </section>
<?php endif; ?>

<div class="container blog_space">
    <div class="blog_grid">
        <?php
         // set up the arguments for the query to select the main post
         $args = array(
        	 'post_type' => 'post',
        	 'post_status' => 'publish',
             'posts_per_page' => 20,
             'meta_query' => array(
                 array(
                     'key' => 'post_visibility',
                     'value' => array('group','followers'),
                     'compare' => 'NOT IN'
                 )
             )
         );
        
         // create a new WP_Query instance with the arguments
         $query = new WP_Query( $args );
        
         // start the loop
         if ( $query->have_posts() ) : 
        	 while ( $query->have_posts() ) : $query->the_post(); 
         ?>
			 <article class="d-flex" data-post-id="<?php the_ID(); ?>">
        			<div>
        				<?php
        					if (has_post_thumbnail()): ?>
        						<a href="<?php the_permalink(); ?>">
        							<div class="img-wrapper_first-post">
        								<div class="post__thumbnail">
        									<?php if (has_post_thumbnail()) :
        										$thumbnail_id = get_post_thumbnail_id(); // Get the ID of the post thumbnail
        										$thumbnail = wp_get_attachment_image($thumbnail_id, 'medium_large', false, array('class' => 'post__thumbnail')); // Get the HTML for the post thumbnail without lazy loading
        										?>
        										<?php echo $thumbnail; ?>
        									<?php endif; ?>
        								</div>
        							</div>
        						</a>
        					<?php
        					endif;
        					?>
        
        				<header class="post__header p-2" role="heading">
            				<h2 class="fs-2">
            					<a class="blog_link" href="<?php the_permalink(); ?>">
            					    <?php the_title(); ?>
            					</a>
            				</h2>
            				<p class="post__date"><time><?php echo human_time_diff(strtotime($post->post_date)) . ' ' . __('ago'); ?></time></p>
            				<p class="fc-5">
            					<?php 
            					$excerpt = get_the_excerpt();
            					$excerpt = wp_trim_words( $excerpt, 20, '...' );
            					echo $excerpt;
            					?>
            				</p>
        				</header>
                            <?php if (current_user_can('administrator')): ?>
                                <button class="ph-admin-delete-post-btn" data-post-id="<?php the_ID(); ?>" style="background:#f8d7da;color:#721c24;border:none;padding:0.5rem 0.75rem;border-radius:0.5rem;cursor:pointer;margin-top:0.5rem;">Deletar</button>
                            <?php endif; ?>
        			</div>
        		 </article>
         <?php
        	 endwhile;
         endif;
        ?>
    </div>
</div>
<?php
 // reset the query
 wp_reset_postdata();
get_footer();
?>
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.ph-admin-delete-post-btn').forEach(btn=>{
        btn.addEventListener('click', function(){
            const postId = this.dataset.postId;
            if (!postId) return;
            if (!confirm('Deseja realmente deletar este post? Esta ação é irreversível.')) return;
            this.disabled = true;
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body: new URLSearchParams({ action: 'admin_delete_post', nonce: '<?php echo wp_create_nonce('social_nonce'); ?>', post_id: postId })
            }).then(r=>r.json()).then(data=>{
                this.disabled = false;
                if (data.success) {
                    const art = document.querySelector('article[data-post-id="' + postId + '"]');
                    if (art) art.remove();
                } else {
                    alert(data.data || 'Erro ao deletar post');
                }
            }).catch(()=>{ this.disabled = false; alert('Erro de rede'); });
        });
    });
});
</script>