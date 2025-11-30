# プラグインインストール手順

## Query Monitor のインストール

### 方法1: WordPress管理画面から
1. WordPress管理画面にログイン
2. 「プラグイン」→「新規追加」
3. 検索窓に「Query Monitor」と入力
4. 「今すぐインストール」→「有効化」

### 方法2: 手動インストール
1. https://ja.wordpress.org/plugins/query-monitor/ からダウンロード
2. `wp-content/plugins/` にアップロード
3. 管理画面で有効化

## 使用方法

### Query Monitor の確認項目
1. **Template**: 使用中のテンプレートファイル
2. **AJAX**: AJAX通信の成功/失敗
3. **PHP Errors**: PHPエラー・警告
4. **Hooks**: add_action/add_filter の実行状況

### 現在の問題調査用
- 管理画面で Menu エディタページを開く
- 画面上部の「Query Monitor」バーをクリック
- 「AJAX」タブで `wp_ajax_save_menu_post` の状況確認
- 「PHP Errors」タブでエラー確認
- 「Template」タブで使用ファイル確認

## 代替手段: functions.php に追加可能なデバッグコード

```php
// デバッグ情報を画面に表示
add_action('wp_footer', function() {
    if (current_user_can('administrator')) {
        global $template;
        echo '<div style="position:fixed;top:0;right:0;background:#000;color:#fff;padding:10px;z-index:9999;">';
        echo 'Template: ' . basename($template) . '<br>';
        echo 'Theme: ' . get_template() . '<br>';
        echo 'User: ' . wp_get_current_user()->user_login;
        echo '</div>';
    }
});
```