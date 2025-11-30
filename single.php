// 権限・ログイン確認
if (!is_user_logged_in()) {
    auth_redirect();
}

// MENU番号で検索
$args = array(
    'post_type' => 'any',
    'meta_query' => array(
        array(
            'key' => 'menu_no',
            'value' => $menu_no,
            'compare' => '='
        )
    ),
    'posts_per_page' => 1,
    'post_status' => array('publish', 'draft', 'pending', 'private')
);

$query = new WP_Query($args);

if ($query->have_posts()) {
    $query->the_post();
    $post_id = get_the_ID();

    if (!current_user_can('edit_post', $post_id)) {
        wp_die('⚠️ この投稿を編集する権限がありません。');
    }

    $edit_link = get_edit_post_link($post_id);
    ...
}
