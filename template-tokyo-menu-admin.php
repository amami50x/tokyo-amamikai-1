<?php
/*
* Template Name: 東京奄美会メニュー（管理者用）
* 説明: 管理者用一覧表示（詳細画面リンク付き）
*/

get_header();
?>

<div class="tokyo-menu-list" style="max-width:1200px; margin:0 auto; padding:30px 20px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height:100vh;">
    <h1 style="background:#dc3545; color:white; padding:15px 20px; text-align:center; border-radius:6px; margin-bottom:20px; font-size:20px; font-weight:600;">
        管理者用 - 東京奄美会ホームページMENU
    </h1>

<?php
$exclude_keywords = ['TEST','テスト','test','Test','TESTです','HP管理関連'];
$all_posts = get_posts(array(
    'post_type'      => 'post',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'ASC'
));

$filtered_posts = array();
$seen_ids = array();

foreach ($all_posts as $post) {
    if (in_array($post->ID, $seen_ids)) continue;
    $seen_ids[] = $post->ID;

    $title = $post->post_title;
    $content = $post->post_content;

    $should_exclude = false;
    foreach ($exclude_keywords as $keyword) {
        if (trim($title) === $keyword || trim($content) === $keyword || stripos($title,$keyword)!==false || stripos($content,$keyword)!==false) {
            $should_exclude = true;
            break;
        }
    }
    if ($should_exclude) continue;

    $menu_no = get_post_meta($post->ID, 'menu_no', true);
    if (!empty($menu_no) && $menu_no !== '0' && $menu_no !== 'none') {
        $filtered_posts[] = $post;
    }
}

$categories_with_posts = array();
foreach ($filtered_posts as $post) {
    $post_categories = get_the_category($post->ID);
    if ($post_categories) {
        foreach ($post_categories as $cat) {
            if (!isset($categories_with_posts[$cat->term_id])) {
                $categories_with_posts[$cat->term_id] = ['category'=>$cat,'posts'=>array()];
            }
            $categories_with_posts[$cat->term_id]['posts'][] = $post;
        }
    }
}

if ($categories_with_posts) {
    foreach ($categories_with_posts as $cat_data) {
        $category = $cat_data['category'];
        $posts = $cat_data['posts'];

        echo '<section class="menu-category" style="margin-bottom:20px;">';
        echo '<div style="text-align:center; margin:15px 0 10px 0;">';
        echo '<h2 style="background:#0066cc; color:white; padding:8px 15px; border-radius:4px; font-size:16px; font-weight:600; text-align:center; display:inline-block; min-width:120px; margin:0;">' . esc_html($category->name) . '</h2>';
        echo '</div>';

        echo '<ul class="tokyo-menu-items" style="margin:0; padding:0; display:flex; flex-wrap:wrap; gap:0.5%; justify-content:center;">';
        global $post;
        $original_post = $post;
        foreach ($posts as $post) {
            setup_postdata($post);
            $post_id = $post->ID;

            $menu_no = get_post_meta($post_id, 'menu_no', true);
            $menu_no = trim(mb_convert_kana($menu_no, 'as')); // 前後空白除去・半角化
            $edit_url = home_url('/menu-edit-admin/');
            $menu_link = add_query_arg(array('menu_no' => $menu_no), $edit_url);
            $display_title = $menu_no . ' ' . get_the_title($post_id);

            echo '<li style="list-style:none; margin:2px 0; width:24%; text-align:center;">';
            echo '<a href="' . esc_url($menu_link) . '" style="display:block; padding:6px 8px; background:#ffe4e1; color:#003366; text-decoration:none; border-radius:4px; border:1px solid #ffb6c1; cursor:pointer; transition:all 0.2s ease; font-weight:500; font-size:12px; text-align:center; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; min-height:32px; line-height:20px;">';
            echo esc_html($display_title);
            echo '</a>';
            echo '</li>';
        }
        echo '</ul>';
        $post = $original_post;
        wp_reset_postdata();

        echo '</section>';
    }
} else {
    echo '<p>カテゴリがありません。</p>';
}
?>
</div>

<?php get_footer(); ?>