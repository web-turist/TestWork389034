<?php
/*
Template Name: Home
*/
?>

<?php get_header(); ?>

<section class="promo">
    <div class="container">
        <div class="promo__wrapper">
            <h1 class="promo__text">
                <p><?php the_field('one-row-title'); ?></p>
                <p> <?php the_field('two-row-title'); ?></p>
                <p><span><?php the_field('three-row-title'); ?></span></p>
                <p><?php the_field('four-row-title'); ?></p>
            </h1>
            <div class="promo__picture">
                <img src="<?php bloginfo('template_url'); ?>/assets/img/promo_img.png" alt="developer">
            </div>
        </div>
    </div>
</section>
<section class="stack">
    <div class="container">
        <div class="stack__wrapper">
            <div class="stack__item"><img src="<?php bloginfo('template_url'); ?>/assets/img/icon_css.svg" alt="css"></div>
            <div class="stack__item"><img src="<?php bloginfo('template_url'); ?>/assets/img/icon_html.svg" alt="html"></div>
            <div class="stack__item"><img src="<?php bloginfo('template_url'); ?>/assets/img/icon_js.svg" alt="javascript"></div>
            <div class="stack__item"><img src="<?php bloginfo('template_url'); ?>/assets/img/icon_react.svg" alt="react"></div>
            <div class="stack__item"><img src="<?php bloginfo('template_url'); ?>/assets/img/icon_redux.png" alt="redux"></div>
            <div class="stack__item"><img src="<?php bloginfo('template_url'); ?>/assets/img/icon_bootstrap.svg" alt="bootstrap"></div>
        </div>
    </div>
</section>
<section class="projects">
    <div class="container">
        <h2 class="projects__title">Projects</h2>
        <div class="projects__wrapper">

            <?php
            $new_args = array(
                'post_type' => 'WEBprojects',
                'publish' => true,
                'paged' => get_query_var('paged')
            );

            query_posts($new_args);
            if (have_posts()) :
                while (have_posts()) : the_post(); ?>

                    <div class="product">
                        <div class="product__picture">
                            <?php the_post_thumbnail(); ?>
                        </div>
                        <div class="product__wrapper-text">
                            <div class="product__title">
                                <?php the_title(); ?>
                            </div>
                            <div class="product__description">
                                <?php the_content(); ?>
                            </div>
                            <div class="product__stack">
                                <span>Tech stack:</span> <?php echo get_post_meta($post->ID, 'stack', true) ?>
                            </div>
                            <div class="product__prewiew">
                                <img src="<?php bloginfo('template_url'); ?>/assets/img/icon_link_small.svg" alt="icon link">
                                <a href="#">Live Preview</a>
                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>
            <?php else : ?>
                <div>Проектов пока нет</div>
            <?php endif; ?>

        </div>
    </div>

</section>
<?php get_footer(); ?>
