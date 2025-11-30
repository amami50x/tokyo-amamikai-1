# Query Monitor 診断手順

## 🎯 Query Monitor での更新ボタン問題診断

### STEP 1: 基本確認
1. WordPress管理画面にログイン
2. Menu エディタページを開く
3. 画面上部に黒いバー「Query Monitor」が表示されているか確認
   - 表示されていない場合 → プラグインが無効化されている可能性

### STEP 2: 更新テスト実行
1. Menu エディタページで適当な内容を入力
2. F12キーでブラウザの開発者ツールを開く
3. 「更新」ボタンをクリック
4. 画面上部の Query Monitor バーをクリック

### STEP 3: Query Monitor での確認項目

#### A. AJAX タブで確認すべき項目
- `action: save_menu_post` のリクエストがあるか
- HTTP ステータスコード (200, 500, 404 など)
- レスポンス内容
- エラーメッセージ

#### B. PHP Errors タブで確認すべき項目
- Fatal Error の有無
- Warning の内容
- Notice の詳細

#### C. Hooks タブで確認すべき項目
- `wp_ajax_save_menu_post` フックが登録されているか
- `handle_save_menu_post` 関数が実行されているか

### STEP 4: レポート内容
以下の情報を確認してください:

**AJAX通信結果:**
- リクエストURL: 
- HTTPステータス: 
- レスポンス内容: 
- エラーメッセージ: 

**PHPエラー:**
- Fatal Error: 
- Warning: 
- Notice: 

**フック実行状況:**
- wp_ajax_save_menu_post: 
- handle_save_menu_post実行: 

## 🚨 もしQuery Monitorバーが見えない場合

### 確認方法1: プラグイン状態チェック
WordPress管理画面 → プラグイン → Query Monitor が「有効」になっているか

### 確認方法2: 管理者権限チェック
現在のユーザーに管理者権限があるか確認

### 確認方法3: 手動有効化
```php
// functions.php に一時的に追加（診断後は削除）
add_action('init', function() {
    if (current_user_can('administrator')) {
        echo '<div style="position:fixed;top:0;left:0;background:red;color:white;padding:10px;z-index:9999;">Query Monitor Status Check</div>';
    }
});
```