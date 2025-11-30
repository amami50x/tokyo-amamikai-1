<?php
/*
* Template Name: メニュー編集（管理者用-2）
*/


// 管理者のみアクセス可
if (!current_user_can('administrator')) {
    wp_die('管理者のみアクセス可能です。');
}

// ------------------------------------------------------
// ✅ 「検索画面へ戻る」ボタンの処理を削除済み
// ------------------------------------------------------

if (isset($_POST['menu_no_search'])) {
    $menu_no = trim(sanitize_text_field($_POST['menu_no']));
    if ($menu_no !== '') {
        global $wpdb;
        $post = $wpdb->get_row($wpdb->prepare("
            SELECT p.ID FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE pm.meta_key = 'menu_no'
            AND pm.meta_value = %s
            AND p.post_type = 'post'
            LIMIT 1
        ", $menu_no));
        if ($post) {
            // 標準の編集画面にリダイレクト
            wp_redirect(admin_url('post.php?post=' . $post->ID . '&action=edit'));
            exit;
        } else {
            $error = '該当するMENU番号の投稿が見つかりません。';
        }
    } else {
        $error = 'MENU番号を入力してください。';
    }
}

get_header();
?>
<div style="max-width:500px;margin:40px auto;background:#fff;padding:30px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
    <h1 style="font-size:1.5em;margin-bottom:20px;">MENU番号で編集画面へ</h1>

    <form method="post" style="margin-bottom:20px;">
        <label for="menu_no">MENU番号</label>
        <input type="text" id="menu_no" name="menu_no" style="width:100%;padding:8px;font-size:1em;margin:10px 0 20px 0;" required>
        <button type="submit" name="menu_no_search" style="background:#0073aa;color:#fff;padding:10px 20px;border:none;border-radius:4px;font-size:1em;">検索して編集画面へ</button>
    </form>

    <?php if (!empty($error)): ?>
        <div style="color:#b00;background:#fee;padding:10px 15px;border-radius:4px;">
            <?php echo esc_html($error); ?>
        </div>
    <?php endif; ?>

</div>
<?php get_footer(); ?>
