<?php
$cidade = get_sub_field('cidade');

if (!empty($cidade)) {
    $cidade_id = $cidade[0];  // Get the first (and presumably only) term ID from the array

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
        'tax_query'      => array(
            array(
                'taxonomy' => 'category',  // Assuming 'cidade' is a category or related taxonomy
                'field'    => 'term_id',  // Use 'term_id' since we're dealing with term IDs
                'terms'    => $cidade_id,  // Use the term ID
                'operator' => 'IN',
            ),
        ),
    );
    $query = new WP_Query($args);
}
?>

<section class="ranking_city">
    <div class="container">
        <?php if ($query->have_posts()) :
            $position = 1;
            while ($query->have_posts()) : $query->the_post();
                $post_id = get_the_ID();
                $vote_count = (int) get_post_meta($post_id, 'vote', true);
                $partyDate = get_post_meta($post_id, 'date', true);
                $partyLocation = get_post_meta($post_id, 'location', true);
                $partyPrice = get_post_meta($post_id, 'price', true);
                $user_ip = $_SERVER['REMOTE_ADDR'];
                $vote_status = get_post_meta($post_id, 'user_vote_' . $user_ip, true);

                
                $place_class = '';
                if ($position == 1) {
                    $place_class = 'first-place';
                } elseif ($position == 2) {
                    $place_class = 'second-place';
                } elseif ($position == 3) {
                    $place_class = 'third-place';
                }
        ?>
                <div class="post-card">
                    <div class="vote-buttons">
                    <?php
                        $previous_vote = get_post_meta($post_id, 'user_vote_' . $user_ip, true);
                    ?>
                        <button class="vote-button" data-post-id="<?php the_ID(); ?>" data-action="increase" data-previous-vote="<?php echo $previous_vote; ?>" <?php echo ($previous_vote === 'up') ? 'disabled' : ''; ?>>&#9650;</button>
                        <p><span id="votes_<?php the_ID(); ?>"><?php echo $vote_count ?: 0; ?></span></p>
                        <button class="vote-button" data-post-id="<?php the_ID(); ?>" data-action="decrease" data-previous-vote="<?php echo $previous_vote; ?>" <?php echo ($previous_vote === 'down') ? 'disabled' : ''; ?>>&#9660;</button>
                    </div>
                    <a class="party_info" href="<?php the_permalink(); ?>">
                        <div>
                            <h2 class="<?php echo $place_class; ?>"><?php the_title(); ?></h2>
                        </div>
                        <div class="party_info_details">
                            <p>
                                <svg width="18" height="18" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 8H18M4.77778 1V3M14.2222 1V3M4.02222 19H14.9778C16.0356 19 16.5646 19 16.9687 18.782C17.3241 18.5903 17.6131 18.2843 17.7941 17.908C18 17.4802 18 16.9201 18 15.8V6.2C18 5.07989 18 4.51984 17.7941 4.09202C17.6131 3.71569 17.3241 3.40973 16.9687 3.21799C16.5646 3 16.0356 3 14.9778 3H4.02222C2.96435 3 2.4354 3 2.03135 3.21799C1.67593 3.40973 1.38697 3.71569 1.20588 4.09202C1 4.51984 1 5.07989 1 6.2V15.8C1 16.9201 1 17.4802 1.20588 17.908C1.38697 18.2843 1.67593 18.5903 2.03135 18.782C2.4354 19 2.96434 19 4.02222 19Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <?php echo $partyDate ?>
                            </p>
                            <p>
                                <svg width="18" height="18" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.5 10C7.98528 10 10 7.98528 10 5.5C10 3.01472 7.98528 1 5.5 1C3.01472 1 1 3.01472 1 5.5C1 7.98528 3.01472 10 5.5 10Z" stroke="black" stroke-width="0.8"/><path d="M5.5 2.22729V8.77275" stroke="black" stroke-width="0.8" stroke-linecap="round"/><path d="M7.13637 3.9659C7.13637 3.23161 6.40377 2.63635 5.50001 2.63635C4.59625 2.63635 3.86365 3.23161 3.86365 3.9659C3.86365 4.70018 4.59625 5.29544 5.50001 5.29544C6.40377 5.29544 7.13637 5.89071 7.13637 6.62499C7.13637 7.35927 6.40377 7.95453 5.50001 7.95453C4.59625 7.95453 3.86365 7.35927 3.86365 6.62499" stroke="black" stroke-width="0.8" stroke-linecap="round"/></svg>
                                <?php echo $partyPrice ?>
                            </p>
                            <p>
                                <svg width="18" height="18" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7 8.4375C6.06794 8.4375 5.3125 7.68206 5.3125 6.75C5.3125 5.81794 6.06794 5.0625 7 5.0625C7.93206 5.0625 8.6875 5.81794 8.6875 6.75C8.6875 7.68206 7.93206 8.4375 7 8.4375ZM7 3.9375C5.44694 3.9375 4.1875 5.19638 4.1875 6.75C4.1875 8.30363 5.44694 9.5625 7 9.5625C8.55306 9.5625 9.8125 8.30363 9.8125 6.75C9.8125 5.19638 8.55306 3.9375 7 3.9375ZM7 16.3125C6.06456 16.3176 1.375 9.10181 1.375 6.75C1.375 3.64388 3.89331 1.125 7 1.125C10.1067 1.125 12.625 3.64388 12.625 6.75C12.625 9.07031 7.92081 16.3176 7 16.3125ZM7 0C3.27231 0 0.25 3.02231 0.25 6.75C0.25 9.57263 5.87781 18.0062 7 18C8.10475 18.0062 13.75 9.53438 13.75 6.75C13.75 3.02231 10.7277 0 7 0Z" fill="black"/></svg>
                                <?php echo $partyLocation ?>
                            </p>
                        </div>
                    </a>
                </div>
        <?php 
                $position++;
            endwhile;
            wp_reset_postdata();
        else :
            echo 'Nenhum post encontrado.';
        endif; ?>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const buttons = document.querySelectorAll(".vote-button");

    buttons.forEach(button => {
        button.addEventListener("click", function () {
            const postId = this.getAttribute("data-post-id");
            const action = this.getAttribute("data-action");
            const voteDisplay = document.getElementById("votes_" + postId);
            let currentVoteCount = parseInt(voteDisplay.textContent, 10);

            const previousVote = this.getAttribute("data-previous-vote");  // We'll simulate this attribute in the HTML

            // Simulate the vote change instantly
            if (action === "increase") {
                if (previousVote === "down") {
                    // User already voted up, so they cannot increase further. Only option is to decrease to "down".
                    voteDisplay.textContent = currentVoteCount + 2;
                    console.log('previousVote === "down" + decreasing this post 2' );
                } else {
                    // First vote or changing from down to up, increase by 1
                    voteDisplay.textContent = currentVoteCount + 1;
                }
            } else if (action === "decrease") {
                if (previousVote === "up") {
                    // User already voted down, so they cannot decrease further. Only option is to increase to "up".
                    voteDisplay.textContent = currentVoteCount - 2;
                    console.log('previousVote === "up" + increasing this post 2' );
                } else {
                    // First vote or changing from up to down, decrease by 1
                    voteDisplay.textContent = currentVoteCount - 1;
                }
            }

            // Disable the clicked button and enable the opposite one
            if (action === "increase") {
                document.querySelector(`[data-post-id="${postId}"][data-action="increase"]`).disabled = true;
                document.querySelector(`[data-post-id="${postId}"][data-action="decrease"]`).disabled = false;
            } else {
                document.querySelector(`[data-post-id="${postId}"][data-action="decrease"]`).disabled = true;
                document.querySelector(`[data-post-id="${postId}"][data-action="increase"]`).disabled = false;
            }

            // Update button styles to reflect disabled state
            updateButtonStyles(postId);

            // Now send the AJAX request to update the vote on the backend
            fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `action=vote_post&post_id=${postId}&vote_action=${action}`
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // If the request failed, revert the vote count
                    if (action === "increase") {
                        voteDisplay.textContent = currentVoteCount;  // Revert to original count
                    } else if (action === "decrease") {
                        voteDisplay.textContent = currentVoteCount;  // Revert to original count
                    }
                    alert(data.message);  // Show an error message if needed
                }
            });
        });
    });

    function updateButtonStyles(postId) {
        const increaseButton = document.querySelector(`[data-post-id="${postId}"][data-action="increase"]`);
        const decreaseButton = document.querySelector(`[data-post-id="${postId}"][data-action="decrease"]`);

        // Apply CSS styles for disabled buttons
        if (increaseButton.disabled) {
            increaseButton.classList.add("disabled");
        } else {
            increaseButton.classList.remove("disabled");
        }

        if (decreaseButton.disabled) {
            decreaseButton.classList.add("disabled");
        } else {
            decreaseButton.classList.remove("disabled");
        }
    }
});
</script>