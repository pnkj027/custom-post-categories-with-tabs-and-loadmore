<?php
/**
 * Template Name: Our Portfolio
 */
get_header();
?>
<div class="main-wrap">
    <div class="portfolio-list common-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 m-auto mb-4 content-wrap">
                    <?php
                    while (have_posts()): the_post();
                        the_content();
                    endwhile;
                    ?>
                </div>
                <div class="col-lg-10 m-auto">
                    <?php
                    echo '<div class="categories-tabs-wrap"> <ul class="nav nav-tabs" role="tablist">
                          <li><span data-href="all" class="active" role="tab" data-toggle="tab"> All </span></li>';
                    $args = array(
                        'taxonomy' => 'post_portfolios_cat',
                        'hide_empty' => 0,
                        'orderby' => 'name',
                        'order' => 'ASC'
                    );
                    $categories = get_categories($args);
                    $act = 1;
                    foreach ($categories as $category) {
                        echo
                        '<li>
            <span data-active="' . $act . '" data-href="' . $category->slug . '" role="tab" data-toggle="tab" class="' . $active . '">    
                ' . $category->name . '
            </span>
        </li>';
                        $act++;
                    }
                    echo '</ul></div>';

                    echo '<div class="portfolio-content allll"><div class="portfolio-item-parent">';

                    echo '<div class="tab-pane" id="all">';
                    echo '<div class="tab-wraper" id="response-all">';
                    $wp_query = new WP_Query(array(
                        'post_type' => 'post_portfolios',
                        'posts_per_page' => 8,
                    ));
                    $found_post = $wp_query->found_posts;
                    while ($wp_query->have_posts()) :
                        $wp_query->the_post();
                        $featured_img_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
                        $watch_now = get_field('watch_now');
                        ?>
                        <?php if ($featured_img_url) { ?> 	
                            <div class="portfolio-item">
                                <div class="portfolio_item_inner" style="background-image: url(<?php echo $featured_img_url; ?>);"> 				

                                </div>
                            </div>
                        <?php } ?>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                    echo '</div>';
//                    echo '<div class="pagination-nav ssss">';
//                    the_posts_pagination(array('mid_size' => 1, 'prev_text' => __('« Prev', 'textdomain'),
//                        'next_text' => __('Next »', 'textdomain')));
//                    echo '</div>';
                    if ($wp_query->max_num_pages > 1):
                        echo '<div class="load-more-btn-wrap" data-id="response-all"><span class="theme-btn" id="loadmore" data-slug=""> Load More </span></div>';
                    endif;
                    echo '</div>';
                    //echo "<pre>";
                    //print_r($categories);
                    $loop = 1;
                    foreach ($categories as $category) {
                        echo '<div class="tab-pane" id="' . $category->slug . '">';
                        echo '<div class="tab-wraper" id="response-' . $loop . '">';
                        $catslug = $category->slug;
                        if ($category->name === "Videos"):
                            $wp_query = new WP_Query(array(
                                'post_type' => 'post_portfolios',
                                'posts_per_page' => 8,
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'post_portfolios_cat',
                                        'field' => 'slug',
                                        'terms' => array($catslug),
                                        'operator' => 'IN'
                                    ),
                                ),
                            ));
                        else:
                            $wp_query = new WP_Query(array(
                                'post_type' => 'post_portfolios',
                                'posts_per_page' => 8,
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'post_portfolios_cat',
                                        'field' => 'slug',
                                        'terms' => array($catslug),
                                        'operator' => 'IN'
                                    ),
                                ),
                            ));
                        endif;
                        $found_post = $wp_query->found_posts;
                        while ($wp_query->have_posts()) :
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
                        endwhile;
                        wp_reset_postdata();
                        echo '</div>';
//                        echo '<div class="pagination-nav ddd">';
//                        the_posts_pagination(array('mid_size' => 1, 'prev_text' => __('« Prev', 'textdomain'),
//                            'next_text' => __('Next »', 'textdomain')));
//                        echo '</div>';
                        if ($wp_query->max_num_pages > 1):
                            echo '<div class="load-more-btn-wrap" id="loadmore" data-id="response-' . $loop . '"><span class="theme-btn"> Load More </span></div>';
                        endif;
                        echo '</div>';
                        $loop++;
                    }
                    echo '</div></div>';
                    ?>
                </div>
            </div> 
        </div>
    </div>
</div>
<?php $nonce = wp_create_nonce('portfolio'); ?>
<script>
    jQuery(document).ready(function () {
        jQuery('.load-more-btn-wrap').click(function () {
            var total = <?php echo $found_post; ?>;
            offset = 8;
            var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
            jQuery(this).html('<div class="spinner-border text-primary" role="status"> <span class="visually-hidden">Loading...</span></div>');
            var clickedcat = "#" + $(this).data("id");
            var catslug = $(this).closest('.tab-pane').attr('id');
            console.warn(clickedcat);
            console.warn("clicked" + clickedcat);
            var data = {
                'action': 'load_more_portfolio',
                'offset': offset,
                'catslug': catslug,
                'security': '<?php echo $nonce; ?>',
            };

            jQuery.post(ajaxurl, data, function (response) {
                if (response !== '') {
                    jQuery(clickedcat).append(response);
                    offset += 8;
                    jQuery(clickedcat).css('opacity', '1');
                    jQuery('.load-more-btn-wrap').html('<span class="theme-btn" id="loadmore"> Load More </span>');
                    jQuery(clickedcat).children('div').each(function (index) {
                        if (total - 1 == index) {
                            jQuery(clickedcat).next('.load-more-btn-wrap').hide();
                        } else {
                            //alert('not');
                        }
                    });
                } else {
                    jQuery('.load-more-btn-wrap').hide();
                }
            });
        });
    });
</script>
<?php include get_template_directory() . '/inc/portfolio/cs.php'; ?> 
<?php include get_template_directory() . '/inc/home/partners.php'; ?> 
<div class = "page-last-section">

</div>
<script data-cfasync="false" data-wpmeteor-after="REORDER" type="javascript/blocked" data-wpmeteor-type="text/javascript"  data-wpmeteor-src="https://modernbarcart.com/wp-content/themes/ModernBarCart/js/bootstrap.min.js" id="BootstrapJS-js"></script>
<script>
    jQuery(".portfolio-content .tab-pane").each(function () {
        jQuery(this).hide();
        jQuery('.portfolio-content .tab-pane:first-child()').show();
    });

    jQuery('.nav-tabs li>span').on("click", function (e) {
        e.preventDefault();
        var id = jQuery(this).attr('data-href');
        jQuery(".portfolio-content .tab-pane").each(function () {
            jQuery(this).hide();
            if (jQuery(this).attr('id') == id) {
                jQuery(this).show();
            }
        });
    });

    jQuery('.nav-tabs li>span').on("click", function (e) {
        e.preventDefault();
        jQuery('.nav-tabs li>span').removeClass('active');
        jQuery(this).addClass('active');
    });
</script>
<?php get_footer(); ?>