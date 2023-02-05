<?php function load_more_portfolio() {
    $nonce_check = check_ajax_referer('portfolio', 'security');
    if (!$nonce_check) {
        return;
    }
    $catslug = $_POST['catslug'];
    $offset = $_POST['offset'];
    if ($catslug === "all") {
        $args = [
            'post_type' => 'post_portfolios',
            'post_status' => 'publish',
            'posts_per_page' => '8',
            'offset' => $offset
        ];
    } else {
        $args = [
            'post_type' => 'post_portfolios',
            'post_status' => 'publish',
            'posts_per_page' => '8',
            'offset' => $offset,
            'tax_query' => array(
                array(
                    'taxonomy' => 'post_portfolios_cat',
                    'field' => 'slug',
                    'terms' => array($catslug),
                    'operator' => 'IN'
                ),
            ),
        ];
    }

    $wp_query = new WP_Query($args);
    if ($wp_query->have_posts()) {
        while ($wp_query->have_posts()) {
            $wp_query->the_post();
            $featured_img_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
            $watch_now = get_field('watch_now');
            ?> 	
            <?php if ($featured_img_url) { ?> 	
                <div class="portfolio-item">
                    <div class="portfolio_item_inner" style="background-image: url(<?php echo $featured_img_url; ?>);"> 
                        <?php if ($category->name === "Videos"): ?>
                            <div class="portfolio_content_wrap">
                                <div> 				
                                    <div class="h3"> <?php the_title(); ?> </div>
                                    <div class="content"> <?php echo wp_trim_words(get_the_content(), 21, ''); ?> </div>
                                </div>
                                <?php if ($watch_now): ?>
                                    <div class="btn-wrap"> 
                                        <a href="<?php echo $watch_now; ?>" class="theme-btn html5lightbox"> Watch Now </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php } ?>
            <?php
        }
        wp_reset_postdata();
    }
    wp_die();
}

add_action('wp_ajax_load_more_portfolio', 'load_more_portfolio');
add_action('wp_ajax_nopriv_load_more_portfolio', 'load_more_portfolio');