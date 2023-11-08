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

<section class="cards">
        <div class="container">
            <h2 class="cards__title">Popular Products</h2>
            <button class="cards__button btn-modal">Add product</button>
            <div class="cards__wrapper">
                <div class="card">
                    <div class="card__wrapper">
                        <div class="card__img">
                            <img src="<?php bloginfo('template_url'); ?>/assets/img/card_white_mouse.png" alt="mouse">
                        </div>

                        <div class="details">
                            <a class="card__title" href="#">iOS mouse</a>
                            <div class="card__prices">
                                <div class="card__price">$ 249.99</div>
                                <div class="card__discount">/<span>$ 249.99</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card__wrapper">
                        <div class="card__img">
                            <img src="<?php bloginfo('template_url'); ?>/assets/img/card_speaker.png" alt="speaker">
                        </div>

                        <div class="details">
                            <a class="card__title" href="#">Black iPhone Speaker</a>
                            <div class="card__prices">
                                <div class="card__price">$ 249.99</div>
                                <div class="card__discount">/<span>$ 249.99</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card__wrapper">
                        <div class="card__img">
                            <img src="<?php bloginfo('template_url'); ?>/assets/img/card_keyborad.png" alt="keyboard">
                        </div>

                        <div class="details">
                            <a class="card__title" href="#">iOS Keyboard</a>
                            <div class="card__prices">
                                <div class="card__price">$ 249.99</div>
                                <div class="card__discount">/<span>$ 249.99</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="contact-form">
        <div class="substrate">
            <form class="form" action="#">
                <div class="form__title">Adding a Product</div>
                <div class="form__inputs">
                    <input class="form__input input-name" type="text" name="product-name" placeholder="Product name">
                    <input class="form__input input-price" type="text" name="product-price" placeholder="Product price">
                    <input class="form__input input-discount" type="text" name="product-discount" placeholder="Product price with discount">
                    <input class="form__input input-img" type="file" name="product-img" multiple>
                    <input class="form__input input-date" type="date" name="adding-date">
                    <select name="uniqueness" id="uniqueness-select">
                        <option value="rare">Rare</option>
                        <option value="frequent">Frequent</option>
                        <option value="unusual">Unusual</option>
                    </select>

                </div>
                <input class="form__btn" type="submit" value="Submit">
                <div class="form__checkbox">
                    <input class="form__checkbox-input" type="checkbox" name="checkbox" id="checkbox" checked required>
                    <label class="form__checkbox-label" for="checkbox">I give my consent to the processing of personal data.</label>
                </div>
            </form>
        </div>
    </section>

<?php get_footer(); ?>
