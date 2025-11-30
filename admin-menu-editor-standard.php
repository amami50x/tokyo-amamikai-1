<?php
// 管理画面専用の標準エディター対応メニュー編集
// このファイルは管理画面内で使用されます

// 直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

// MENU番号が指定されている場合の処理
$menu_no = isset($_GET['menu_no']) ? sanitize_text_field($_GET['menu_no']) : '';
?>

<style>
.standard-editor-container {
    background: #f1f1f1;
    padding: 20px;
    border-radius: 8px;
    margin-top: 20px;
}
.search-form {
    background: white;
    padding: 20px;
    border-radius: 6px;
    border: 1px solid #ccd0d4;
    margin-bottom: 20px;
}
.menu-found {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    padding: 20px;
    border-radius: 6px;
    margin-bottom: 20px;
}
.menu-not-found {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    padding: 20px;
    border-radius: 6px;
    margin-bottom: 20px;
}
.action-buttons {
    display: flex;
    gap: 10px;
    margin-top: 15px;
    flex-wrap: wrap;
}
.btn {
    padding: 10px 15px;
    text-decoration: none;
    border-radius: 4px;
    font-weight: bold;
    display: inline-block;
}
.btn-primary { background: #0073aa; color: white; }
.btn-secondary { background: #6c757d; color: white; }
.btn-warning { background: #ffc107; color: #212529; }
.btn-info { background: #17a2b8; color: white; }
</style>

<h1>📝 標準エディター対応 メニュー編集システム</h1>

<div class="standard-editor-container">
    
    <!-- 説明 -->
    <div style="background:#d1ecf1; border:1px solid #bee5eb; padding:15px; border-radius:6px; margin-bottom:20px;">
        <h3 style="color:#0c5460; margin-top:0;">✨ WordPress標準機能を活用した安全な編集方式</h3>
        <ul style="color:#0c5460; margin-bottom:0;">
            <li>🎯 <strong>MENU番号で検索</strong> → 該当投稿を素早く発見</li>
            <li>📝 <strong>WordPress標準エディター</strong> → 確実な保存・豊富な機能</li>
            <li>🔧 <strong>完全なツール対応</strong> → フォント・色・配置・表・画像挿入</li>
            <li>💾 <strong>問題解決済み</strong> → 更新されない・ツール不具合を完全回避</li>
        </ul>
    </div>

    <!-- MENU番号検索フォーム -->
    <div class="search-form">
        <h3>🔍 編集するMENU検索</h3>
        <form method="GET" style="display:flex; gap:15px; align-items:end;">
            <div style="flex:1;">
                <label><strong>MENU番号</strong></label><br>
                <input type="text" name="menu_no" value="<?php echo esc_attr($menu_no); ?>" 
                       placeholder="例: 01-02-01" 
                       style="width:100%; padding:8px; border:1px solid #8c8f94; border-radius:3px;">
            </div>
            <div>
                <input type="hidden" name="page" value="standard-menu-editor">
                <button type="submit" class="button-primary">🔍 検索・編集</button>
            </div>
        </form>
    </div>

    <?php if ($menu_no): ?>
        <?php
        // MENU番号で投稿を検索
        $menu_post = null;
        $all_posts = get_posts(array(
            'numberposts' => -1,
            'post_status' => array('publish', 'draft', 'private'),
            'post_type' => 'post'
        ));
        
        foreach($all_posts as $post) {
            $meta_menu_no = get_post_meta($post->ID, 'menu_no', true);
            if($meta_menu_no === $menu_no) {
                $menu_post = $post;
                break;
            }
        }

        if ($menu_post): 
            $edit_url = admin_url('post.php?post=' . $menu_post->ID . '&action=edit');
            $view_url = get_permalink($menu_post->ID);
            $editor_name = get_post_meta($menu_post->ID, 'editor_name', true);
            $end_date = get_post_meta($menu_post->ID, 'end_date', true);
        ?>
            
            <!-- 投稿が見つかった場合 -->
            <div class="menu-found">
                <h3 style="color:#155724; margin-top:0;">✅ MENU「<?php echo esc_html($menu_no); ?>」が見つかりました</h3>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">タイトル</th>
                        <td><strong><?php echo esc_html($menu_post->post_title); ?></strong></td>
                    </tr>
                    <tr>
                        <th scope="row">投稿ID</th>
                        <td><?php echo $menu_post->ID; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">状態</th>
                        <td><?php echo esc_html(get_post_status($menu_post->ID)); ?></td>
                    </tr>
                    <tr>
                        <th scope="row">最終更新</th>
                        <td><?php echo get_the_modified_date('Y年n月j日 H:i', $menu_post->ID); ?></td>
                    </tr>
                    <?php if ($editor_name): ?>
                    <tr>
                        <th scope="row">編集者</th>
                        <td><?php echo esc_html($editor_name); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($end_date): ?>
                    <tr>
                        <th scope="row">終了日</th>
                        <td><?php echo esc_html($end_date); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
                
                <!-- アクションボタン -->
                <div class="action-buttons">
                    <a href="<?php echo esc_url($edit_url); ?>" class="btn btn-primary">
                        📝 WordPress標準エディターで編集
                    </a>
                    <a href="<?php echo esc_url($view_url); ?>" target="_blank" class="btn btn-secondary">
                        👀 公開画面で表示
                    </a>
                    <a href="<?php echo admin_url('post.php?post=' . $menu_post->ID . '&action=edit&classic-editor'); ?>" class="btn btn-info">
                        📝 クラシックエディター
                    </a>
                </div>
            </div>

            <!-- 投稿内容プレビュー -->
            <div style="background:white; padding:20px; border:1px solid #ccd0d4; border-radius:6px;">
                <h3>📄 現在の内容プレビュー</h3>
                <div style="background:#f9f9f9; border:1px solid #ddd; padding:15px; border-radius:4px; max-height:400px; overflow-y:auto;">
                    <?php echo apply_filters('the_content', $menu_post->post_content); ?>
                </div>
            </div>

        <?php else: ?>
            
            <!-- 投稿が見つからなかった場合 -->
            <div class="menu-not-found">
                <h3 style="color:#721c24; margin-top:0;">❌ MENU「<?php echo esc_html($menu_no); ?>」が見つかりません</h3>
                <p style="color:#721c24;">
                    指定されたMENU番号の投稿が存在しないか、削除されている可能性があります。
                </p>
                
                <!-- 新規作成オプション -->
                <div style="margin-top:15px; padding:15px; background:#fff3cd; border:1px solid #ffeeba; border-radius:4px;">
                    <h4 style="color:#856404; margin-top:0;">💡 新規作成オプション</h4>
                    <p style="color:#856404;">このMENU番号で新しい投稿を作成できます。</p>
                    <div class="action-buttons">
                        <a href="<?php echo admin_url('post-new.php'); ?>" class="btn btn-warning">
                            ➕ 新規投稿作成
                        </a>
                    </div>
                    <p style="color:#856404; font-size:13px; margin-top:10px; margin-bottom:0;">
                        ※ 作成後、カスタムフィールド「menu_no」に「<?php echo esc_html($menu_no); ?>」を設定してください
                    </p>
                </div>
            </div>
            
        <?php endif; ?>
        
    <?php else: ?>
        
        <!-- MENU番号未入力時の説明 -->
        <div style="background:white; padding:20px; border:1px solid #ccd0d4; border-radius:6px;">
            <h3>📋 使用方法</h3>
            <ol style="line-height:1.6;">
                <li>上記フォームに編集したい<strong>MENU番号</strong>を入力</li>
                <li>「🔍 検索・編集」ボタンをクリック</li>
                <li>該当MENU投稿が表示される</li>
                <li>「📝 WordPress標準エディターで編集」をクリック</li>
                <li>WordPress標準の編集画面で安全に編集・保存</li>
            </ol>
            
            <div style="margin-top:20px; padding:15px; background:#e9ecef; border-radius:4px;">
                <h4 style="margin-top:0;">🎯 この方式のメリット</h4>
                <ul style="margin:0;">
                    <li>✅ WordPress標準機能による確実な保存</li>
                    <li>✅ 豊富な編集ツール（フォント・色・表・画像等）</li>
                    <li>✅ ビジュアル・テキストエディター切り替え可能</li>
                    <li>✅ プレビュー・リビジョン・自動保存機能</li>
                    <li>✅ 更新されない問題・ツール不具合の完全解決</li>
                </ul>
            </div>
        </div>
        
    <?php endif; ?>

    <!-- 便利リンク -->
    <div style="background:white; padding:20px; border:1px solid #ccd0d4; border-radius:6px; margin-top:20px;">
        <h3>🔗 便利リンク</h3>
        <div class="action-buttons">
            <a href="<?php echo home_url('/menu-list-admin1/'); ?>" class="btn btn-info">
                📋 MENU一覧（管理者用）
            </a>
            <a href="<?php echo admin_url('edit.php'); ?>" class="btn btn-secondary">
                📝 全投稿一覧
            </a>
            <a href="<?php echo admin_url('post-new.php'); ?>" class="btn btn-warning">
                ➕ 新規投稿作成
            </a>
        </div>
    </div>

</div>