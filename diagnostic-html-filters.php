<?php
/*
WordPress設定診断とHTMLフィルタリング無効化
管理者のみアクセス可能
*/

// セキュリティチェック
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/../../../../');
    require_once(ABSPATH . 'wp-config.php');
    require_once(ABSPATH . 'wp-includes/wp-db.php');
    require_once(ABSPATH . 'wp-includes/pluggable.php');
}

// 管理者チェック
if (!current_user_can('administrator')) {
    die('アクセス権限がありません');
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>WordPress HTMLフィルタリング診断</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; }
        .section { border: 2px solid #0073aa; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .success { border-color: #28a745; background-color: #d4edda; }
        .warning { border-color: #ffc107; background-color: #fff3cd; }
        .danger { border-color: #dc3545; background-color: #f8d7da; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 3px; overflow-x: auto; }
        .fix-button { background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; margin: 5px; }
    </style>
</head>
<body>
    <h1>WordPress HTMLフィルタリング診断</h1>
    
    <?php
    // 現在の設定を診断
    echo '<div class="section">';
    echo '<h2>🔍 現在の設定状況</h2>';
    
    // WordPress定数の確認
    echo '<h3>WordPress定数</h3>';
    echo '<p><strong>DISALLOW_UNFILTERED_HTML:</strong> ' . (defined('DISALLOW_UNFILTERED_HTML') ? (DISALLOW_UNFILTERED_HTML ? 'true (フィルタリング強制)' : 'false') : '未定義') . '</p>';
    echo '<p><strong>ALLOW_UNFILTERED_UPLOADS:</strong> ' . (defined('ALLOW_UNFILTERED_UPLOADS') ? (ALLOW_UNFILTERED_UPLOADS ? 'true' : 'false') : '未定義') . '</p>';
    
    // ユーザー権限の確認
    echo '<h3>現在のユーザー権限</h3>';
    echo '<p><strong>管理者:</strong> ' . (current_user_can('administrator') ? '✅ Yes' : '❌ No') . '</p>';
    echo '<p><strong>投稿編集:</strong> ' . (current_user_can('edit_posts') ? '✅ Yes' : '❌ No') . '</p>';
    echo '<p><strong>フィルターなしHTML:</strong> ' . (current_user_can('unfiltered_html') ? '✅ Yes' : '❌ No') . '</p>';
    
    // フィルターの確認
    echo '<h3>アクティブなフィルター</h3>';
    
    $filters_to_check = ['content_save_pre', 'content_filtered_save_pre', 'excerpt_save_pre'];
    foreach ($filters_to_check as $filter) {
        echo "<h4>{$filter}</h4>";
        if (isset($GLOBALS['wp_filter'][$filter])) {
            echo '<ul>';
            foreach ($GLOBALS['wp_filter'][$filter]->callbacks as $priority => $callbacks) {
                foreach ($callbacks as $callback_id => $callback) {
                    echo "<li>優先度 {$priority}: {$callback_id}</li>";
                }
            }
            echo '</ul>';
        } else {
            echo '<p>フィルターなし ✅</p>';
        }
    }
    echo '</div>';
    
    // 修正処理
    if (isset($_POST['fix_html_filtering'])) {
        echo '<div class="section success">';
        echo '<h2>🔧 修正処理を実行中...</h2>';
        
        // wp-config.phpに定数を追加
        $wp_config_path = ABSPATH . 'wp-config.php';
        if (file_exists($wp_config_path)) {
            $wp_config_content = file_get_contents($wp_config_path);
            
            $additions = '';
            if (strpos($wp_config_content, 'DISALLOW_UNFILTERED_HTML') === false) {
                $additions .= "\ndefine('DISALLOW_UNFILTERED_HTML', false); // HTMLフィルタリングを無効化\n";
            }
            if (strpos($wp_config_content, 'ALLOW_UNFILTERED_UPLOADS') === false) {
                $additions .= "define('ALLOW_UNFILTERED_UPLOADS', true); // フィルターなしアップロードを許可\n";
            }
            
            if ($additions) {
                $wp_config_content = str_replace(
                    "/* That's all, stop editing! Happy publishing. */",
                    $additions . "\n/* That's all, stop editing! Happy publishing. */",
                    $wp_config_content
                );
                
                if (file_put_contents($wp_config_path, $wp_config_content)) {
                    echo '<p>✅ wp-config.phpに設定を追加しました</p>';
                } else {
                    echo '<p>❌ wp-config.phpの書き込みに失敗しました</p>';
                }
            }
        }
        
        // フィルターを即座に無効化
        kses_remove_filters();
        remove_all_filters('content_save_pre');
        remove_all_filters('content_filtered_save_pre');
        remove_all_filters('excerpt_save_pre');
        
        echo '<p>✅ 現在のセッションでHTMLフィルターを無効化しました</p>';
        echo '<p><strong>注意:</strong> 変更を有効にするため、一度WordPressからログアウトして再ログインしてください。</p>';
        echo '</div>';
    }
    
    // テスト用投稿作成
    if (isset($_POST['create_test_post'])) {
        $test_content = '<p style="color: red; text-align: center; font-weight: bold;">赤い中央寄せ太字テスト</p>
<p style="color: blue; text-align: right;">青い右寄せテスト</p>
<p style="background-color: yellow; padding: 10px;">黄色背景テスト</p>';
        
        // フィルターを完全無効化
        kses_remove_filters();
        remove_all_filters('content_save_pre');
        
        $post_data = array(
            'post_title' => 'HTMLフォーマット保持テスト - ' . date('Y-m-d H:i:s'),
            'post_content' => $test_content,
            'post_status' => 'draft',
            'post_type' => 'post'
        );
        
        $post_id = wp_insert_post($post_data);
        
        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, 'menu_no', 'TEST_' . date('His'));
            
            echo '<div class="section success">';
            echo '<h2>✅ テスト投稿を作成しました</h2>';
            echo '<p><strong>投稿ID:</strong> ' . $post_id . '</p>';
            echo '<p><a href="/wp-admin/post.php?post=' . $post_id . '&action=edit" target="_blank">WordPress標準エディターで確認</a></p>';
            
            // 保存された内容を確認
            $saved_post = get_post($post_id);
            echo '<h3>保存された内容:</h3>';
            echo '<div style="border: 1px solid #ccc; padding: 15px; background: #f9f9f9;">';
            echo $saved_post->post_content;
            echo '</div>';
            echo '</div>';
        }
    }
    ?>
    
    <div class="section warning">
        <h2>⚠️ 問題の解決</h2>
        <p>WordPressのHTMLフィルタリングが原因で、文字色や配置などのフォーマットが保存されない問題が発生しています。</p>
        
        <form method="post" style="margin: 20px 0;">
            <button type="submit" name="fix_html_filtering" class="fix-button">
                HTMLフィルタリングを完全無効化
            </button>
            <p><small>この操作により、wp-config.phpに設定を追加し、HTMLフィルタリングを無効化します。</small></p>
        </form>
        
        <form method="post" style="margin: 20px 0;">
            <button type="submit" name="create_test_post" style="background: #28a745;" class="fix-button">
                フォーマット保持テスト投稿を作成
            </button>
            <p><small>フォーマット付きのテスト投稿を作成して、保持状況を確認します。</small></p>
        </form>
    </div>
    
    <div class="section">
        <h2>📝 MENU投稿の確認</h2>
        <?php
        $menu_posts = get_posts(array(
            'post_type' => 'post',
            'meta_query' => array(
                array(
                    'key' => 'menu_no',
                    'compare' => 'EXISTS'
                )
            ),
            'posts_per_page' => 5,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        
        if ($menu_posts) {
            foreach ($menu_posts as $post) {
                $menu_no = get_post_meta($post->ID, 'menu_no', true);
                echo '<div style="border: 1px solid #ddd; padding: 15px; margin: 10px 0;">';
                echo '<h4>' . esc_html($post->post_title) . ' (ID: ' . $post->ID . ')</h4>';
                echo '<p><strong>MENU No:</strong> ' . esc_html($menu_no) . '</p>';
                echo '<div style="background: #f9f9f9; padding: 10px; border-left: 4px solid #0073aa;">';
                echo '<h5>保存されている内容:</h5>';
                echo $post->post_content;
                echo '</div>';
                echo '<p><a href="/wp-admin/post.php?post=' . $post->ID . '&action=edit" target="_blank">編集</a></p>';
                echo '</div>';
            }
        } else {
            echo '<p>MENU投稿が見つかりません。</p>';
        }
        ?>
    </div>
</body>
</html>