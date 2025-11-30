
<?php
/* 
* Template Name: メニュー編集（管理者用ー3） 
 */
/**
 * テーマのメインとなるテンプレートファイル
 * MENU番号検索システム対応版 - 個別表示修正版 + 編集機能
 */

// ========================================
// MENU編集システム - 最優先処理
// ========================================
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['menu_number'])) {
    $menu_number = trim($_GET['menu_number']);
    // 管理者チェック
    if (!current_user_can('administrator')) {
        wp_die('管理者のみアクセス可能です。');
    }
    // 投稿を検索
    global $wpdb;
    $post_data = $wpdb->get_row($wpdb->prepare("
        SELECT p.* FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE pm.meta_key = 'menu_no'
        AND pm.meta_value = %s
        AND p.post_type = 'post'
        LIMIT 1
    ", $menu_number));
    if (!$post_data) {
        wp_die('MENU番号「' . esc_html($menu_number) . '」が見つかりません。');
    }
    $page_id = $post_data->ID;
    $message = '';
    // 更新処理
    if (isset($_POST['menu_update'])) {
        $page_id = intval($_POST['page_id']);
        $menu_title = trim(sanitize_text_field($_POST['menu_title']));
        $menu_no = trim(sanitize_text_field($_POST['menu_no']));
        $editor_name = trim(sanitize_text_field($_POST['editor_name']));
        $post_content = trim($_POST['post_content']);
        if (empty($menu_title) || empty($menu_no) || empty($editor_name)) {
            $message = '❌ 必須項目が未入力です。';
        } else {
            $update_result = wp_update_post([
                'ID' => $page_id,
                'post_title' => $menu_title,
                'post_content' => $post_content,
                'post_status' => 'publish'
            ], true);
            if (is_wp_error($update_result)) {
                $message = '❌ 更新失敗: ' . $update_result->get_error_message();
            } else {
                update_post_meta($page_id, 'menu_no', $menu_no);
                update_post_meta($page_id, 'menu_title', $menu_title);
                update_post_meta($page_id, 'editor_name', $editor_name);
                $message = '✅ 更新が完了しました！';
                // データを再取得
                $post_data = get_post($page_id);
            }
        }
    }
    // 現在のデータ取得
    $menu_title = get_post_meta($page_id, 'menu_title', true) ?: $post_data->post_title;
    $menu_no = get_post_meta($page_id, 'menu_no', true);
    $editor_name = get_post_meta($page_id, 'editor_name', true);
    $post_content = $post_data->post_content;
    get_header();
    ?>
    <style>
    .menu-edit-container { max-width: 900px; margin: 20px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .menu-edit-container input[type="text"], .menu-edit-container textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
    .menu-edit-container textarea { height: 300px; font-family: monospace; }
    .menu-edit-container label { display: block; margin-top: 15px; margin-bottom: 5px; font-weight: bold; color: #333; }
    .menu-edit-container button { background: #2196F3; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
    .menu-edit-container button:hover { background: #1976D2; }
    .message { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
    .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
    <div class="menu-edit-container">
        <h1>MENU編集: <?php echo esc_html($menu_no); ?></h1>
        <?php if (!empty($message)): ?>
        <div class="message <?php echo strpos($message, '❌') !== false ? 'error' : 'success'; ?>">
            <?php echo esc_html($message); ?>
        </div>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="page_id" value="<?php echo esc_attr($page_id); ?>">
            <label>MENUタイトル *</label>
            <input type="text" name="menu_title" value="<?php echo esc_attr($menu_title); ?>" required>
            <label>MENU番号 *</label>
            <input type="text" name="menu_no" value="<?php echo esc_attr($menu_no); ?>" required>
            <label>編集者 *</label>
            <input type="text" name="editor_name" value="<?php echo esc_attr($editor_name); ?>" required>
            <label>本文内容</label>
            <textarea name="post_content"><?php echo esc_textarea($post_content); ?></textarea>
            <div style="margin-top: 20px;">
                <button type="submit" name="menu_update">✅ 更新する</button>
                <a href="<?php echo home_url('/menu-list-admin1/?admin=1'); ?>" style="margin-left: 10px; padding: 10px 20px; background: #666; color: white; text-decoration: none; border-radius: 4px; display: inline-block;">← 一覧に戻る</a>
            </div>
        </form>
    </div>
    <?php
    get_footer();
    exit;
}

// MENU個別表示システム - 最優先処理
if (isset($_GET['menu_number']) && !empty(trim($_GET['menu_number']))) {
    $menu_number = trim($_GET['menu_number']);
    if (!empty($menu_number)) {
        $single_post = null;
        global $wpdb;
        $post_with_menu = $wpdb->get_row($wpdb->prepare("
            SELECT p.* FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE pm.meta_key = 'menu_no'
            AND pm.meta_value = %s
            AND p.post_status = 'publish'
            AND p.post_type = 'post'
            LIMIT 1
        ", $menu_number));
        if ($post_with_menu) {
            $single_post = $post_with_menu;
        }
        if ($single_post) {
            get_header();
            ?>
            <div style="max-width: 800px; margin: 20px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                <?php
                $is_admin_mode = isset($_GET['admin']) && $_GET['admin'] == '1';
                if ($is_admin_mode) {
                    $back_url = home_url('/menu-list-admin1/?admin=1');
                } else {
                    $back_url = home_url('/menu詳細/');
                }
                ?>
                <a href="<?php echo esc_url($back_url); ?>" style="display: inline-block; background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-bottom: 30px;">← 一覧に戻る</a>
                <h1 style="color: #333; font-size: 2em; margin-bottom: 30px; border-bottom: 3px solid #4ecdc4; padding-bottom: 15px;">
                    <?php echo esc_html($single_post->post_title); ?>
                </h1>
                <div style="line-height: 1.8; font-size: 16px; margin-bottom: 30px; background: #f9f9f9; padding: 20px; border-radius: 8px;">
                    <h3 style="color: #4ecdc4; margin-top: 0; margin-bottom: 15px;">📄 投稿内容</h3>
                    <?php
                    $content = apply_filters('the_content', $single_post->post_content);
                    if (!empty(trim(strip_tags($content)))) {
                        echo $content;
                    } else {
                        echo '<p style="color: #999; font-style: italic;">この投稿には本文がありません。</p>';
                    }
                    ?>
                </div>
                <?php if ($is_admin_mode): ?>
                <div style="background: #e8f5e8; padding: 20px; border-radius: 8px; border-left: 4px solid #28a745;">
                    <h3 style="color: #28a745; margin-top: 0; margin-bottom: 15px;">🏷️ 管理項目</h3>
                    <?php
                    $menu_no = get_post_meta($single_post->ID, 'menu_no', true);
                    $post_author = get_the_author_meta('display_name', $single_post->post_author);
                    $end_date = get_post_meta($single_post->ID, 'end_date', true);
                    if (empty($end_date)) {
                        $end_date = get_post_meta($single_post->ID, '掲載終了日', true);
                    }
                    $editor = get_post_meta($single_post->ID, 'editor', true);
                    if (empty($editor)) {
                        $editor = get_post_meta($single_post->ID, '編集者', true);
                    }
                    ?>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px;">
                        <div><strong>MENU番号:</strong> <?php echo esc_html($menu_no ?: '未設定'); ?></div>
                        <div><strong>投稿者:</strong> <?php echo esc_html($post_author); ?></div>
                        <div><strong>掲載終了日:</strong> <?php echo esc_html($end_date ?: '未設定'); ?></div>
                        <div><strong>編集者:</strong> <?php echo esc_html($editor ?: '未設定'); ?></div>
                    </div>
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ccc; font-size: 12px; color: #666;">
                        投稿ID: <?php echo $single_post->ID; ?> |
                        投稿日: <?php echo date('Y年m月d日', strtotime($single_post->post_date)); ?> |
                        更新日: <?php echo date('Y年m月d日', strtotime($single_post->post_modified)); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php
            get_footer();
            exit;
        } else {
            get_header();
            ?>
            <div style="max-width: 600px; margin: 40px auto; background: #fff3cd; padding: 30px; border-radius: 10px; border-left: 4px solid #ffc107;">
                <h2 style="color: #856404; margin-top: 0;">⚠️ MENU番号が見つかりません</h2>
                <p>MENU番号「<?php echo esc_html($menu_number); ?>」に該当する投稿がありません。</p>
                <a href="<?php echo home_url(); ?>" style="display: inline-block; background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">ホームに戻る</a>
            </div>
            <?php
            get_footer();
            exit;
        }
    }
}

// 通常のインデックス表示（MENU番号がない場合）
get_header();
?>
<main>
    <h1>メニュー一覧</h1>
    <?php if (have_posts()) :
        while (have_posts()) : the_post(); ?>
            <h2><?php the_title(); ?></h2>
            <?php the_content(); ?>
        <?php endwhile;
    else : ?>
        <p>投稿がありません。</p>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
        AND pm.meta_value = %s

