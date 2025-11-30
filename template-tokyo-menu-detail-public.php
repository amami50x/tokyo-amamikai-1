<?php
// デバッグ: menu_noとGET全体を出力
echo '<div style="background:#fcc; color:#222; font-size:13px; padding:6px; margin:6px 0; border:1px solid #f00;">';
echo '$_GET[\'menu_no\']: <b>' . (isset($_GET['menu_no']) ? htmlspecialchars($_GET['menu_no']) : '(未設定)') . '</b><br>';
echo '$_GET: <pre style="margin:0;">'.htmlspecialchars(var_export($_GET, true)).'</pre>';
echo '</div>';
/*
* Template Name: 東京奄美会メニュー詳細（公開用）
* Description: 公開用MENU詳細表示（一覧から遷移）
*/

$menu_post = null;
$menu_no = isset($_GET['menu_no']) ? trim(mb_convert_kana($_GET['menu_no'], 'as')) : '';
$return_to = isset($_GET['return_to']) ? esc_url_raw($_GET['return_to']) : home_url('/menu-list-public-2/');

if ($menu_no) {
    $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'menu_no',
                'value' => $menu_no,
                'compare' => '='
            )
        )
    );
    $posts = get_posts($args);
    if ($posts) {
        $menu_post = $posts[0];
    }
}
// menu_noで見つからなければpost_idで取得
if (!$menu_post && $post_id) {
    $post = get_post($post_id);
    if ($post && $post->post_status === 'publish') {
        $menu_post = $post;
    }
}
