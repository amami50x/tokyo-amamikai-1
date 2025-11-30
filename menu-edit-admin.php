<?php
/*
Template Name: メニュー編集（管理用・修正済）
説明: menu_noに基づき投稿を読み込み、編集・更新（menu_no含む）するためのテンプレート。（$menu_no未定義エラーを修正）
*/

// =======================================================
// 1. 権限・ログイン確認
// =======================================================
if (!is_user_logged_in()) { 
    auth_redirect(); // 未ログインならログインページへリダイレクト
}

$menu_no = '';
$message = '';
$post_to_edit = null;

// =======================================================
// 2. 投稿の更新処理 (POSTリクエスト)
// =======================================================
if (isset($_POST['action']) && $_POST['action'] === 'update_menu' && isset($_POST['post_id'])) {
    
    $post_id = intval($_POST['post_id']);
    
    // セキュリティチェック
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'update_menu_' . $post_id)) {
        $message = '<div style="color: white; background: red; padding: 10px; border-radius: 4px;">セキュリティチェックに失敗しました。やり直してください。</div>';
    } else {
        
        // データをサニタイズして準備
        $updated_post = array(
            'ID'           => $post_id,
            'post_title'   => sanitize_text_field($_POST['post_title']),
            'post_content' => wp_kses_post($_POST['post_content']),
            // 'post_status' => sanitize_text_field($_POST['post_status']) // ステータスも変更可能にする場合
        );

        // 投稿を更新
        $result = wp_update_post($updated_post, true);

        if (is_wp_error($result)) {
            $message = '<div style="color: white; background: red; padding: 10px; border-radius: 4px;">更新エラー: ' . $result->get_error_message() . '</div>';
        } else {
            // ★カスタムフィールド (menu_no) の更新
            $new_menu_no = sanitize_text_field($_POST['menu_no']);
            update_post_meta($post_id, 'menu_no', $new_menu_no);

            $message = '<div style="color: white; background: green; padding: 10px; border-radius: 4px;">MENU ID: ' . $post_id . ' を正常に更新しました！ (新 menu_no: ' . esc_html($new_menu_no) . ')</div>';
            
            // 更新後、編集中の投稿を再ロードするために新しいmenu_noを設定
            $post_to_edit = get_post($post_id);
            $menu_no = $new_menu_no; // フォーム表示用に更新後の値を設定
        }
    }
}

// =======================================================
// 3. 投稿のロード処理 (GETまたはPOSTによるmenu_no検索)
// =======================================================

// GETまたはPOSTリクエストから入力値を取得
if (isset($_REQUEST['menu_no_input']) && !empty($_REQUEST['menu_no_input'])) {
    $menu_no = sanitize_text_field($_REQUEST['menu_no_input']);
}

// $menu_noがセットされている場合のみ検索を実行
if (!empty($menu_no)) {

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
        
        $post_to_edit = get_post($post_id);
        $edit_link = get_edit_post_link($post_id);
        
        wp_reset_postdata(); 

    } elseif (empty($message)) {
        $message = '<div style="color: #721c24; background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; border-radius: 4px;">❌ MENU-NO「' . esc_html($menu_no) . '」に一致する投稿が見つかりませんでした。</div>';
    }
}


get_header();
?>

<div id="menu-edit-admin" style="max-width: 900px; margin: 30px auto; padding: 20px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 8px; font-family: Arial, sans-serif;">
    
    <h1 style="color: #333; border-bottom: 2px solid #ccc; padding-bottom: 10px; margin-bottom: 20px;">MENU編集・更新ツール (管理用)</h1>
    
    <?php echo $message; // メッセージの表示 ?>

    <!-- 1. 投稿ロードフォーム -->
    <div style="padding: 15px; background: #fff; border: 1px solid #eee; border-radius: 6px; margin-bottom: 30px;">
        <h2 style="font-size: 1.2em; color: #007cba; margin-top: 0;">MENU検索</h2>
        <form action="<?php echo esc_url(get_permalink()); ?>" method="get">
            <label for="menu_no_input" style="display: block; margin-bottom: 8px; font-weight: bold;">編集したい MENU番号 を入力:</label>
            <input type="text" id="menu_no_input" name="menu_no_input" 
                   value="<?php echo esc_attr($menu_no); // $menu_noを使用 ?>"
                   placeholder="例: 101"
                   style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; width: 200px; margin-right: 10px;">
            <button type="submit" style="padding: 8px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; transition: background 0.2s;">
                MENUをロード
            </button>
        </form>
    </div>

    <!-- 2. 編集フォーム -->
    <?php if ($post_to_edit): ?>
    <div style="padding: 20px; background: #ffffff; border: 2px solid #dc3545; border-radius: 8px;">
        <h2 style="font-size: 1.5em; color: #dc3545; margin-top: 0;">
            編集中のMENU: <?php echo esc_html(get_the_title($post_to_edit->ID)); ?> 
            (ID: <?php echo $post_to_edit->ID; ?>)
        </h2>
        <p style="color: #6c757d; font-size: 0.9em;">
            [<a href="<?php echo esc_url(get_edit_post_link($post_to_edit->ID)); ?>" target="_blank" style="color: #007cba;">WordPress管理画面で編集</a>]
        </p>


        <form action="<?php echo esc_url(get_permalink()); ?>" method="post">
            <input type="hidden" name="action" value="update_menu">
            <input type="hidden" name="post_id" value="<?php echo $post_to_edit->ID; ?>">
            <?php wp_nonce_field('update_menu_' . $post_to_edit->ID); ?>

            <label for="input_menu_no" style="display: block; margin-top: 20px; margin-bottom: 5px; font-weight: bold; color: #dc3545;">
                ★ MENU番号 (カスタムフィールド 'menu_no'):
            </label>
            <input type="text" id="input_menu_no" name="menu_no" 
                   value="<?php echo esc_attr(get_post_meta($post_to_edit->ID, 'menu_no', true)); ?>"
                   style="width: 100%; padding: 10px; border: 2px solid #dc3545; border-radius: 4px; box-sizing: border-box; font-size: 1.1em;">


            <label for="post_title" style="display: block; margin-top: 20px; margin-bottom: 5px; font-weight: bold;">タイトル (post_title):</label>
            <input type="text" id="post_title" name="post_title" 
                   value="<?php echo esc_attr($post_to_edit->post_title); ?>"
                   style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">

            <label for="post_content" style="display: block; margin-top: 20px; margin-bottom: 5px; font-weight: bold;">内容 (post_content):</label>
            <textarea id="post_content" name="post_content" rows="15"
                      style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; resize: vertical; font-family: 'Segoe UI Mono', monospace;"><?php echo esc_textarea($post_to_edit->post_content); ?></textarea>

            <button type="submit" style="display: block; width: 100%; padding: 15px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1.2em; margin-top: 20px; transition: background 0.2s;">
                上記の変更を保存・更新
            </button>
        </form>
    </div>
    <?php elseif (!empty($menu_no)): ?>
        <p style="text-align: center; color: #999; padding: 20px; background: #fff; border-radius: 6px;">
            上記の入力欄に存在するMENU番号を入力してください。
        </p>
    <?php endif; ?>

</div>

<?php get_footer(); ?>