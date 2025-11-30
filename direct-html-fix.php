<?php
/*
直接HTML修正ツール
投稿のHTMLを直接データベースに保存
*/

// セキュリティチェック
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/../../../../');
    require_once(ABSPATH . 'wp-config.php');
    require_once(ABSPATH . 'wp-includes/wp-db.php');
    require_once(ABSPATH . 'wp-includes/pluggable.php');
}

// 処理の実行
if (isset($_POST['fix_post']) && isset($_POST['post_id'])) {
    $post_id = intval($_POST['post_id']);
    
    // 修正したいHTML内容
    $fixed_content = 'ｓｄｋｌｓｖｖｋｌｓｋｌｄｓｖｋｓｖｄｋｌｓｖｄｋｋｌｄｖｓｖ

ｌｓｄｖｋｓｄｖｋｊｌｓｄｖｋｌｊｓｖｄ

<p style="color: red; text-align: center; font-weight: bold;">あｂｃ</p>';
    
    // 直接データベースを更新
    global $wpdb;
    $result = $wpdb->update(
        $wpdb->posts,
        array('post_content' => $fixed_content),
        array('ID' => $post_id),
        array('%s'),
        array('%d')
    );
    
    if ($result !== false) {
        // キャッシュクリア
        if (function_exists('clean_post_cache')) {
            clean_post_cache($post_id);
        }
        if (function_exists('wp_cache_delete')) {
            wp_cache_delete($post_id, 'posts');
        }
        
        $success_message = "投稿 ID {$post_id} を直接修正しました！";
    } else {
        $error_message = "修正に失敗しました。";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>直接HTML修正ツール</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .section { border: 2px solid #0073aa; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .button { background: #0073aa; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; }
        .danger { background: #dc3545; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px solid #e9ecef; overflow-x: auto; }
        input[type="number"] { padding: 8px; border: 1px solid #ccc; border-radius: 3px; width: 100px; }
    </style>
</head>
<body>
    <h1>🔧 直接HTML修正ツール</h1>
    
    <?php if (isset($success_message)): ?>
        <div class="success">
            <h3>✅ 修正完了</h3>
            <p><?php echo htmlspecialchars($success_message); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="error">
            <h3>❌ エラー</h3>
            <p><?php echo htmlspecialchars($error_message); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="section">
        <h2>📝 テスト投稿の直接修正</h2>
        <p>WordPressのHTMLフィルタリングをバイパスして、直接データベースにHTMLを保存します。</p>
        
        <form method="post" style="margin: 20px 0;">
            <p><strong>修正する投稿ID:</strong></p>
            <input type="number" name="post_id" value="<?php echo isset($_GET['post_id']) ? intval($_GET['post_id']) : ''; ?>" placeholder="投稿ID" required>
            
            <p><strong>修正内容:</strong></p>
            <pre>ｓｄｋｌｓｖｖｋｌｓｋｌｄｓｖｋｓｖｄｋｌｓｖｄｋｋｌｄｖｓｖ

ｌｓｄｖｋｓｄｖｋｊｌｓｄｖｋｌｊｓｖｄ

&lt;p style="color: red; text-align: center; font-weight: bold;"&gt;あｂｃ&lt;/p&gt;</pre>
            
            <p>
                <button type="submit" name="fix_post" class="button">
                    💾 直接データベース修正を実行
                </button>
            </p>
        </form>
    </div>
    
    <div class="section">
        <h2>📋 最近の投稿一覧</h2>
        <?php
        // 最近の投稿を表示
        $recent_posts = $wpdb->get_results("
            SELECT ID, post_title, post_content, post_date, post_status 
            FROM {$wpdb->posts} 
            WHERE post_type = 'post' 
            ORDER BY post_date DESC 
            LIMIT 10
        ");
        
        if ($recent_posts):
            foreach ($recent_posts as $post):
        ?>
            <div style="border: 1px solid #ddd; padding: 15px; margin: 10px 0; background: #f9f9f9;">
                <h4><?php echo htmlspecialchars($post->post_title); ?> <small>(ID: <?php echo $post->ID; ?>)</small></h4>
                <p><strong>投稿日:</strong> <?php echo $post->post_date; ?></p>
                <p><strong>ステータス:</strong> <?php echo $post->post_status; ?></p>
                
                <div style="background: white; padding: 10px; border: 1px solid #ccc; margin: 10px 0;">
                    <strong>現在の内容（HTMLソース）:</strong>
                    <pre><?php echo htmlspecialchars(substr($post->post_content, 0, 500)); ?><?php echo strlen($post->post_content) > 500 ? '...' : ''; ?></pre>
                </div>
                
                <div style="background: white; padding: 10px; border: 1px solid #ccc; margin: 10px 0;">
                    <strong>表示結果:</strong>
                    <div style="border: 1px solid #eee; padding: 10px;">
                        <?php echo $post->post_content; ?>
                    </div>
                </div>
                
                <p>
                    <a href="?post_id=<?php echo $post->ID; ?>" class="button" style="text-decoration: none; display: inline-block;">
                        この投稿を修正
                    </a>
                </p>
            </div>
        <?php 
            endforeach;
        else:
        ?>
            <p>投稿が見つかりません。</p>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <h2>⚠️ 使用方法</h2>
        <ol>
            <li>上の投稿一覧から修正したい投稿の「この投稿を修正」をクリック</li>
            <li>投稿IDが自動入力されるので、「直接データベース修正を実行」をクリック</li>
            <li>修正後、WordPressサイトで投稿を表示して確認</li>
            <li>赤い文字・中央寄せが表示されているか確認</li>
        </ol>
        
        <p><strong>注意:</strong> この方法はWordPressのHTMLフィルタリングを完全にバイパスします。</p>
    </div>
</body>
</html>