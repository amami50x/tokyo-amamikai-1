<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?> 
    </head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <div class="menu-container">
        <button id="menuToggle" class="menu-toggle-button">
            MENU
        </button>
                <!-- main-menu（旧メニュー）は削除しました -->
        <div id="corporateMenu" class="corporate-info-menu-wrapper" style="display: none;">
            <?php
            // カテゴリごとにMENU番号順で投稿を階層表示
            $categories = get_categories(array(
                'orderby' => 'name',
                'order' => 'ASC'
            ));
            echo '<ul class="category-menu">';
            foreach ($categories as $category) {
                echo '<li class="category-title">' . esc_html($category->name);
                $posts = get_posts(array(
                    'category__in' => array($category->term_id),
                    'numberposts' => -1,
                    'meta_key' => 'menu_no',
                    'orderby' => 'meta_value',
                    'order' => 'ASC',
                    'post_type' => 'post'
                ));
                if ($posts) {
                    echo '<ul class="post-menu">';
                    foreach ($posts as $post) {
                        $menu_no = get_post_meta($post->ID, 'menu_no', true);
                        echo '<li><a href="' . get_permalink($post->ID) . '">' . esc_html($post->post_title) . '</a>（' . esc_html($menu_no) . '）</li>';
                    }
                    echo '</ul>';
                }
                echo '</li>';
            }
            echo '</ul>';
            ?>
        </div>
    </div>
    <div id="primary" class="content-area">
        <main id="main" class="site-main">