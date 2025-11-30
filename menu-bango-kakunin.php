

<?php
/*
Template Name: MENU番号確認ページ
*/

get_header();

// 管理者以外はアクセス不可
if (!current_user_can('manage_options')) {
    echo '<p>管理者のみアクセス可能です。</p>';
    get_footer();
    exit;
}

// 投稿取得
$args = [
    'post_type'      => 'post',
    'posts_per_page' => -1,
    'orderby'        => 'menu_no',
    'order'          => 'ASC'
];
$posts = get_posts($args);

// 一覧表示
echo '<div style="max-width:1000px; margin:20px auto;">';
echo '<h2>MENU番号一覧（管理者用）</h2>';
echo '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; width:100%;">';
echo '<tr style="background:#f0f0f0;"><th>タイトル</th><th>MENU番号</th><th>カテゴリ</th></tr>';

foreach($posts as $post) {
    $menu_no = get_field('menu_no', $post->ID); // ACFのフィールド
    $categories = wp_get_post_categories($post->ID, ['fields'=>'names']);
    echo '<tr>';
    echo '<td>' . esc_html($post->post_title) . '</td>';
    echo '<td>' . esc_html($menu_no) . '</td>';
    echo '<td>' . implode(', ', $categories) . '</td>';
    echo '</tr>';
}

echo '</table>';
echo '</div>';

get_footer();
?>
