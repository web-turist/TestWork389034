<!DOCTYPE html>
<html <?php language_attributes('html');?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

 <?php wp_head(); ?>

    <title>Developer Test</title>
</head>

<body>
    <header class="header">
        <div class="container">
            <div class="header__wrapper">
                <div class="logo">
                        <?php the_custom_logo(); ?>
                </div>

                <?php
                    wp_nav_menu([
                        'theme_location' => 'top_menu'
                    ]);
                ?>
<!-- <?php bloginfo('template_url'); ?>/assets/img/icon_github.svg -->
<!-- <?php bloginfo('template_url'); ?>/assets/img/icon_twitter.svg -->
<!-- <?php bloginfo('template_url'); ?>/assets/img/icon_linkedin.svg -->

                <div class="icons">
                    <a class="icons__link" href="<?php the_field('github-link'); ?>" target="_blank">
                        <img class="icons__github" src="<?php the_field('one-icon-social') ?>" alt="github">
                    </a>
                    <a class="icons__link" href="<?php the_field('twitter-link') ?>" target="_blank">
                        <img class="icons__twitter" src="<?php the_field('two-icon-social') ?>" alt="twitter">
                    </a>
                    <a class="icons__link" href="<?php the_field('linkedin-link') ?>" target="_blank">
                        <img class="icons__likedin" src="<?php the_field('three-icon-social') ?>" alt="likedin">
                    </a>
                </div>
            </div>
        </div>
    </header>
