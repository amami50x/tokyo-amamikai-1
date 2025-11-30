<?php
/**
 * Template Name: メニュー編集(管理者用-1)
 * 標準投稿編集画面をベースにしたMENU編集画面
 */

// 権限チェック
if (!is_user_logged_in() || (!current_user_can('edit_posts') && !current_user_can('manage_options'))) {
    wp_die('このページにアクセスする権限がありません。');
}

get_header(); 
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メニュー編集（修正）</title>
    
    <!-- WordPressのスタイルとスクリプトを読み込み -->
    <?php wp_enqueue_script('editor'); ?>
    <?php wp_enqueue_style('editor-buttons'); ?>
    
    <style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        margin: 0;
        padding: 20px;
        background: #f1f1f1;
    }
    
    .admin-container {
        max-width: 1200px;
        margin: 0 auto;
        background: white;
        border: 1px solid #ccd0d4;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
    }
    
    .admin-header {
        background: #0073aa;
        color: white;
        padding: 20px;
        border-bottom: 1px solid #005177;
    }
    
    .admin-header h1 {
        margin: 0;
        font-size: 23px;
        font-weight: 400;
        line-height: 1.3;
    }
    
    .search-section {
        background: #f7f7f7;
        padding: 20px;
        border-bottom: 1px solid #ddd;
    }
    
    .search-row {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }
    
    .search-label {
        font-weight: 600;
        color: #23282d;
        font-size: 14px;
        white-space: nowrap;
    }
    
    .search-input {
        padding: 8px 12px;
        border: 1px solid #7e8993;
        border-radius: 4px;
        font-size: 14px;
        width: 200px;
    }
    
    .wp-core-ui .button {
        padding: 8px 16px;
        height: auto;
        font-size: 14px;
    }
    
    .button-primary {
        background: #0073aa;
        border-color: #0073aa;
    }
    
    .button-success {
        background: #46b450;
        border-color: #46b450;
        color: white;
    }
    
    .button-debug {
        background: #ff6b6b;
        border-color: #ff6b6b;
        color: white;
    }
    
    .status-display {
        padding: 12px;
        border-radius: 4px;
        margin: 15px 0;
        font-size: 13px;
        display: none;
    }
    
    .status-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .status-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .status-info {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }
    
    .edit-form-container {
        padding: 20px;
    }
    
    .meta-fields-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
        background: #f9f9f9;
        padding: 15px;
        border: 1px solid #e1e1e1;
        border-radius: 4px;
    }
    
    .meta-field {
        display: flex;
        flex-direction: column;
    }
    
    .meta-label {
        font-weight: 600;
        font-size: 12px;
        color: #23282d;
        margin-bottom: 5px;
    }
    
    .meta-input {
        padding: 8px;
        border: 1px solid #7e8993;
        border-radius: 4px;
        font-size: 13px;
        width: 100%;
        box-sizing: border-box;
    }
    
    .post-title-section {
        margin-bottom: 20px;
    }
    
    .post-title-input {
        width: 100%;
        padding: 12px;
        font-size: 1.7em;
        line-height: 1.2;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }
    
    .editor-section {
        margin-bottom: 20px;
    }
    
    .editor-toolbar {
        background: #f5f5f5;
        border: 1px solid #ddd;
        border-bottom: none;
        padding: 15px;
        border-radius: 4px 4px 0 0;
    }
    
    .editor-toolbar .toolbar-row {
        margin-bottom: 10px;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        align-items: center;
    }
    
    .editor-toolbar button,
    .editor-toolbar select,
    .editor-toolbar input[type="color"] {
        padding: 6px 12px;
        border: 1px solid #ddd;
        background: white;
        border-radius: 3px;
        cursor: pointer;
        font-size: 13px;
    }
    
    .editor-toolbar button:hover {
        background: #f0f0f0;
    }
    
    .editor-main {
        border: 1px solid #ddd;
        border-radius: 0 0 4px 4px;
        /* min-height: 400px; 削除 */
        padding: 20px;
        font-size: 16px;
        line-height: 1.6;
        outline: none;
        width: 210mm;
        height: 297mm;
        min-height: 297mm;
        max-width: 100%;
        background: #fff;
        box-sizing: border-box;
        margin: 0 auto;
        overflow: auto;
    }
    
    .editor-actions {
        background: #f9f9f9;
        padding: 15px;
        border-top: 1px solid #ddd;
        text-align: right;
    }
    
    .post-meta-section {
        background: #f9f9f9;
        padding: 20px;
        border: 1px solid #e1e1e1;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    
    .post-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .form-table th {
        text-align: left;
        padding: 10px 10px 10px 0;
        width: 150px;
        font-weight: 600;
        font-size: 13px;
    }
    
    .form-table td {
        padding: 10px 10px;
    }
    

    
    @media (max-width: 768px) {
        .meta-fields-row {
            grid-template-columns: 1fr 1fr;
        }
        
        .post-meta-grid {
            grid-template-columns: 1fr;
        }
    }
    </style>
</head>
<body>

<div class="admin-container">
    <!-- ⓪ 画面タイトル -->
    <div class="admin-header">
        <h1>メニュー編集（修正）</h1>
    </div>
    
    <!-- ① MENU-NO入力 + ② 検索・更新ボタン -->
    <div class="search-section">
        <div class="search-row">
            <span class="search-label">MENU-NO:</span>
            <input type="text" id="menu_number_input" class="search-input" placeholder="例: 01-01-01" />
            <button type="button" onclick="loadMenuContent()" class="button button-primary">検索</button>
            <button type="button" onclick="updateMenuContent()" class="button button-success">更新</button>
            <button type="button" onclick="debugMenuNos()" class="button button-debug">デバッグ</button>
        </div>
    </div>
    
    <!-- ステータス表示 -->
    <div id="current_menu_display" class="status-display"></div>
    
    <!-- 編集フォーム -->
    <div class="edit-form-container">
        <form id="editor_form">
            <input type="hidden" id="post_id" name="post_id" />
            
            <!-- ③ タイトル -->
            <div class="post-title-section">
                <input type="text" id="post_title" name="post_title" class="post-title-input" placeholder="タイトルを入力" />
            </div>
            
            <!-- ⑤⑥⑦➇ メタ情報（同一行） -->
            <div class="meta-fields-row">
                <div class="meta-field">
                    <label class="meta-label">⑤ MENU-NO</label>
                    <input type="text" id="menu_no_meta" name="menu_no_meta" class="meta-input" readonly />
                </div>
                <div class="meta-field">
                    <label class="meta-label">⑥ 投稿者</label>
                    <input type="text" id="post_author" name="post_author" class="meta-input" readonly />
                </div>
                <div class="meta-field">
                    <label class="meta-label">⑦ 投稿日</label>
                    <input type="text" id="post_date" name="post_date" class="meta-input" readonly />
                </div>
                <div class="meta-field">
                    <label class="meta-label">➇ 掲載最終日</label>
                    <input type="date" id="end_date" name="end_date" class="meta-input" />
                </div>
            </div>
            
            <!-- ④ 投稿本文 + 編集ツール -->
            <div class="editor-section">
                <label style="font-weight: 600; margin-bottom: 10px; display: block;">④ 投稿本文</label>
                <div class="editor-toolbar">
                    <!-- 第1行: 元に戻す/やり直し、検索、ズーム -->
                    <div class="toolbar-row">
                        <button type="button" onclick="formatText('undo')" title="元に戻す">↶</button>
                        <button type="button" onclick="formatText('redo')" title="やり直し">↷</button>
                        <span style="margin: 0 10px; border-left: 1px solid #ddd;"></span>
                        
                        <input type="text" id="searchInput" placeholder="検索" style="width: 150px; padding: 6px;">
                        <button type="button" onclick="findText()">🔍</button>
                        <span style="margin: 0 10px; border-left: 1px solid #ddd;"></span>
                        
                        <select id="zoomLevel" onchange="changeZoom()">
                            <option value="0.75">75%</option>
                            <option value="1" selected>100%</option>
                            <option value="1.25">125%</option>
                            <option value="1.5">150%</option>
                        </select>
                    </div>
                    
                    <!-- 第2行: フォント、サイズ、太字/斜体/下線、色 -->
                    <div class="toolbar-row">
                        <select id="fontFamily" onchange="changeFontFamily()">
                            <option value="'Yu Gothic', sans-serif" selected>游ゴシック</option>
                            <option value="'Hiragino Kaku Gothic ProN', sans-serif">ヒラギノ角ゴ</option>
                            <option value="'MS PGothic', sans-serif">MS Pゴシック</option>
                            <option value="'MS Gothic', monospace">MS ゴシック</option>
                            <option value="'Meiryo', sans-serif">メイリオ</option>
                            <option value="Arial, sans-serif">Arial</option>
                            <option value="'Times New Roman', serif">Times New Roman</option>
                        </select>
                        
                        <select id="fontSize" onchange="changeFontSize()">
                            <option value="10px">10px</option>
                            <option value="12px">12px</option>
                            <option value="14px">14px</option>
                            <option value="16px" selected>16px</option>
                            <option value="18px">18px</option>
                            <option value="20px">20px</option>
                            <option value="24px">24px</option>
                            <option value="28px">28px</option>
                            <option value="32px">32px</option>
                            <option value="36px">36px</option>
                        </select>
                        <span style="margin: 0 10px; border-left: 1px solid #ddd;"></span>
                        
                        <button type="button" onclick="formatText('bold')" style="font-weight: bold;">B</button>
                        <button type="button" onclick="formatText('italic')" style="font-style: italic;">I</button>
                        <button type="button" onclick="formatText('underline')" style="text-decoration: underline;">U</button>
                        <button type="button" onclick="formatText('strikeThrough')" style="text-decoration: line-through;">S</button>
                        <span style="margin: 0 10px; border-left: 1px solid #ddd;"></span>
                        
                        <label style="display: inline-flex; align-items: center; gap: 5px;">
                            文字色: <input type="color" id="textColor" value="#000000" onchange="changeTextColor()" style="width: 40px; height: 30px;">
                        </label>
                        <label style="display: inline-flex; align-items: center; gap: 5px;">
                            背景色: <input type="color" id="bgColor" value="#ffff00" onchange="changeBackgroundColor()" style="width: 40px; height: 30px;">
                        </label>
                    </div>
                    
                    <!-- 第3行: 見出し、配置、リスト、インデント -->
                    <div class="toolbar-row">
                        <select id="formatBlock" onchange="changeFormat()">
                            <option value="p">通常段落</option>
                            <option value="h1">見出し1（大）</option>
                            <option value="h2">見出し2（中）</option>
                            <option value="h3">見出し3（小）</option>
                            <option value="h4">見出し4</option>
                            <option value="h5">見出し5</option>
                            <option value="h6">見出し6</option>
                        </select>
                        <span style="margin: 0 10px; border-left: 1px solid #ddd;"></span>
                        
                        <button type="button" onclick="alignText('justifyLeft')" title="左揃え">⬅</button>
                        <button type="button" onclick="alignText('justifyCenter')" title="中央揃え">🎯</button>
                        <button type="button" onclick="alignText('justifyRight')" title="右揃え">➡</button>
                        <button type="button" onclick="alignText('justifyFull')" title="両端揃え">⬌</button>
                        <span style="margin: 0 10px; border-left: 1px solid #ddd;"></span>
                        
                        <button type="button" onclick="formatText('insertOrderedList')" title="番号付きリスト">1️⃣</button>
                        <button type="button" onclick="formatText('insertUnorderedList')" title="箇条書き">•</button>
                        <button type="button" onclick="formatText('indent')" title="インデント">⟶</button>
                        <button type="button" onclick="formatText('outdent')" title="アウトデント">⟵</button>
                    </div>
                    
                    <!-- 第4行: リンク、画像、表、書式クリア、HTML -->
                    <div class="toolbar-row">
                        <button type="button" onclick="insertLink()">🔗 リンク</button>
                        <button type="button" onclick="formatText('unlink')">🔗⃠ リンク解除</button>
                        <span style="margin: 0 10px; border-left: 1px solid #ddd;"></span>
                        
                        <button type="button" onclick="insertImage()">🖼️ 画像</button>
                        <button type="button" onclick="insertTable()">📊 表</button>
                        <button type="button" onclick="formatText('insertHorizontalRule')">— 区切り線</button>
                        <span style="margin: 0 10px; border-left: 1px solid #ddd;"></span>
                        
                        <button type="button" onclick="formatText('removeFormat')">T✗ 書式クリア</button>
                        <button type="button" onclick="toggleSourceView()">< > HTML</button>
                    </div>
                </div>
                <div id="editor" class="editor-main" contenteditable="true">
                    <p style="color: #666; font-style: italic; padding: 20px;">MENU-NOを入力して「検索」をクリックすると、既存の投稿内容が読み込まれます。</p>
                </div>
            </div>
            
            <!-- ⑨⑩ 投稿メタ情報（カテゴリUI完全削除） -->
            <div class="post-meta-section">
                <div class="post-meta-grid">
                    <div>
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th><label for="editor_name">⑨ 編集者</label></th>
                                    <td>
                                        <input type="text" id="editor_name" name="editor_name" class="meta-input" />
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="post_status">⑩ 投稿状態</label></th>
                                    <td>
                                        <select id="post_status" name="post_status" class="meta-input">
                                            <option value="publish">公開</option>
                                            <option value="draft">下書き</option>
                                            <option value="private">非公開</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- 更新ボタン -->
            <div class="editor-actions">
                <button type="button" onclick="updateMenuContent()" class="button button-success button-large">更新を保存</button>
            </div>
        </form>
    </div>
</div>

<script>
// グローバル変数の初期化
var ajaxUrl = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
var menuEditorNonce = '<?php echo wp_create_nonce("menu_editor_nonce"); ?>';

// 初期化確認ログ
console.log('=== MENU編集システム初期化 ===');
console.log('Ajax URL:', ajaxUrl);
console.log('Nonce:', menuEditorNonce);
console.log('現在時刻:', new Date().toLocaleString('ja-JP'));

// デバッグ機能：すべてのmenu_noを表示
function debugMenuNos() {
    console.log('🔧 デバッグ情報を取得中...');
    console.log('Ajax URL:', ajaxUrl);
    console.log('Nonce:', menuEditorNonce);
    
    showStatus('🔧 データベース内のMENU-NO一覧を取得中...', 'info');
    
    const formData = new FormData();
    formData.append('action', 'debug_menu_nos');
    formData.append('nonce', menuEditorNonce);
    
    console.log('FormData内容:');
    for (let pair of formData.entries()) {
        console.log('  ' + pair[0] + ': ' + pair[1]);
    }
    
    fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('📡 レスポンスステータス:', response.status);
        console.log('📡 レスポンスヘッダー:', response.headers);
        return response.text();
    })
    .then(text => {
        console.log('📄 生レスポンス:', text);
        const data = JSON.parse(text);
        console.log('📊 パース済みデバッグ結果:', data);
        
        if (data.success) {
            const debugInfo = data.data;
            let message = `📊 データベース内のMENU-NO一覧\n\n`;
            message += `全${debugInfo.total_count}件の投稿でmenu_noカスタムフィールドが見つかりました\n\n`;
            
            if (debugInfo.menu_nos.length > 0) {
                debugInfo.menu_nos.forEach(item => {
                    message += `🔸 MENU-NO: ${item.menu_no}\n`;
                    message += `   投稿ID: ${item.post_id} | タイトル: ${item.post_title}\n`;
                    message += `   ステータス: ${item.post_status}\n\n`;
                });
                
                // 利用可能なMENU-NOを簡単にコピーできるように表示
                message += `📋 利用可能なMENU-NO:\n`;
                const menuNos = debugInfo.menu_nos.map(item => item.menu_no).join(', ');
                message += menuNos;
            } else {
                message += '❌ menu_noカスタムフィールドを持つ投稿がありません\n\n';
                message += '考えられる原因:\n';
                message += '1. カスタムフィールド名が「menu_no」ではない\n';
                message += '2. まだ投稿にmenu_noが設定されていない\n';
                message += '3. データベースに問題がある\n';
            }
            
            // ポップアップで表示
            alert(message);
            
            // ステータス表示も更新
            if (debugInfo.menu_nos.length > 0) {
                showStatus('✅ デバッグ完了: ' + debugInfo.total_count + '件のMENU-NOが見つかりました', 'success');
            } else {
                showStatus('❌ デバッグ: menu_noカスタムフィールドが見つかりません', 'error');
            }
            
        } else {
            const errorMsg = data.data || 'デバッグ情報の取得に失敗しました';
            console.error('❌ デバッグエラー:', errorMsg);
            alert('❌ ' + errorMsg);
            showStatus('❌ ' + errorMsg, 'error');
        }
    })
    .catch(error => {
        console.error('❌ デバッグエラー:', error);
        alert('❌ デバッグエラー: ' + error.message);
        showStatus('❌ デバッグエラー: ' + error.message, 'error');
    });
}

// MENU内容読み込み
function loadMenuContent() {
    const menuNumber = document.getElementById('menu_number_input').value.trim();
    if (!menuNumber) {
        showStatus('MENU-NOを入力してください', 'error');
        return;
    }
    
    console.log('🔍 MENU-NO検索開始:', menuNumber);
    console.log('📡 Ajax URL:', ajaxUrl);
    console.log('🔑 Nonce:', menuEditorNonce);
    showStatus('🔍 MENU-NO「' + menuNumber + '」で検索中...', 'info');
    
    const formData = new FormData();
    formData.append('action', 'get_menu_post');
    formData.append('menu_no', menuNumber);
    formData.append('nonce', menuEditorNonce);
    
    fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('📡 レスポンスステータス:', response.status);
        console.log('📡 レスポンスヘッダー:', response.headers);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text(); // まずテキストで取得
    })
    .then(text => {
        console.log('📄 生レスポンス:', text);
        
        // JSONとしてパース
        try {
            const data = JSON.parse(text);
            console.log('📊 パース済み検索結果:', data);
            
            if (data.success && data.data) {
                const postData = data.data;
                
                // 基本情報設定
                document.getElementById('post_id').value = postData.ID || '';
                document.getElementById('post_title').value = postData.post_title || '';
                
                // 投稿本文を設定（空の場合はデフォルトメッセージを表示）
                const editorElement = document.getElementById('editor');
                if (postData.post_content && postData.post_content.trim() !== '') {
                    editorElement.innerHTML = postData.post_content;
                } else {
                    editorElement.innerHTML = '<p style="color: #666; font-style: italic; padding: 20px;">コンテンツがありません</p>';
                }
                
                // メタ情報設定
                document.getElementById('menu_no_meta').value = postData.menu_no || menuNumber;
                document.getElementById('post_author').value = postData.post_author || '不明';
                document.getElementById('post_date').value = postData.post_date ? formatDate(postData.post_date) : '不明';
                document.getElementById('end_date').value = postData.end_date || '';
                document.getElementById('editor_name').value = postData.editor_name || '';
                document.getElementById('post_status').value = postData.post_status || 'publish';
                
                // カテゴリ設定
                if (postData.categories && postData.categories.length > 0) {
                    loadCategories(postData.categories);
                } else {
                    loadCategories([]);
                }
                
                console.log('✅ 読み込み完了:', {
                    ID: postData.ID,
                    title: postData.post_title,
                    menu_no: postData.menu_no,
                    content_length: postData.post_content ? postData.post_content.length : 0
                });
                
                showStatus('✅ MENU-NO「' + menuNumber + '」の読み込み完了（投稿ID: ' + postData.ID + '、本文: ' + (postData.post_content ? postData.post_content.length : 0) + ' バイト）', 'success');
                
            } else {
                const errorMsg = data.data || '投稿が見つかりませんでした';
                console.error('❌ 検索エラー:', errorMsg);
                showStatus('❌ ' + errorMsg, 'error');
                
                // エラー時にデバッグ情報を提案
                setTimeout(() => {
                    if (confirm('データが見つかりませんでした。データベース内のMENU-NO一覧を確認しますか？')) {
                        debugMenuNos();
                    }
                }, 1000);
            }
        } catch (parseError) {
            console.error('❌ JSONパースエラー:', parseError);
            console.error('パースできなかったテキスト:', text);
            showStatus('❌ JSONパースエラー: ' + parseError.message + ' | レスポンス: ' + text.substring(0, 100), 'error');
        }
    })
    .catch(error => {
        console.error('❌ 検索エラー:', error);
        showStatus('❌ 検索エラー: ' + error.message, 'error');
    });
}



// MENU内容更新
function updateMenuContent() {
    const menuNumber = document.getElementById('menu_number_input').value.trim();
    const postTitle = document.getElementById('post_title').value.trim();
    
    if (!menuNumber) {
        showStatus('MENU-NOを入力してください', 'error');
        return;
    }
    
    if (!postTitle) {
        showStatus('タイトルを入力してください', 'error');
        return;
    }
    
    // エディター内容を取得
    const editorElement = document.getElementById('editor');
    const postContent = editorElement.innerHTML;
    
    // カテゴリ関連処理削除
    console.log('💾 更新開始:', {
        menu_no: menuNumber,
        title: postTitle,
        content_length: postContent.length
    });
    showStatus('💾 更新中...', 'info');
    const formData = new FormData();
    formData.append('action', 'save_menu_post');
    formData.append('menu_no', menuNumber);
    formData.append('post_title', postTitle);
    formData.append('post_content', postContent);
    formData.append('post_id', document.getElementById('post_id').value);
    formData.append('end_date', document.getElementById('end_date').value);
    formData.append('editor_name', document.getElementById('editor_name').value);
    formData.append('post_status', document.getElementById('post_status').value);
    formData.append('nonce', menuEditorNonce);
    
    fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        console.log('📊 更新結果:', data);
        
        if (data.success) {
            if (data.data && data.data.ID) {
                document.getElementById('post_id').value = data.data.ID;
            }
            showStatus('✅ 更新完了', 'success');
        } else {
            const errorMsg = data.data || '更新に失敗しました';
            console.error('❌ 更新エラー:', errorMsg);
            showStatus('❌ ' + errorMsg, 'error');
        }
    })
    .catch(error => {
        console.error('❌ 更新エラー:', error);
        showStatus('❌ 更新エラー: ' + error.message, 'error');
    });
}

// 日付フォーマット
function formatDate(dateString) {
    try {
        const date = new Date(dateString);
        return date.toISOString().split('T')[0];
    } catch (e) {
        return dateString;
    }
}

// ステータス表示
function showStatus(message, type) {
    const statusDiv = document.getElementById('current_menu_display');
    statusDiv.innerHTML = message;
    statusDiv.className = 'status-display status-' + type;
    statusDiv.style.display = 'block';
}

// フォームリセット
function resetForm() {
    document.getElementById('post_id').value = '';
    document.getElementById('post_title').value = '';
    document.getElementById('editor').innerHTML = '<p style="color: #666; font-style: italic; padding: 20px;">MENU-NOを入力して「検索」をクリックすると、既存の投稿内容が読み込まれます。</p>';
    document.getElementById('menu_no_meta').value = '';
    document.getElementById('post_author').value = '';
    document.getElementById('post_date').value = '';
    document.getElementById('end_date').value = '';
    document.getElementById('editor_name').value = '';
    document.getElementById('post_status').value = 'publish';
    // カテゴリUI削除済み
}

// 編集ツール関数
function formatText(command) {
    if (command === 'undo' || command === 'redo') {
        document.execCommand(command, false, null);
    } else {
        document.execCommand(command, false, null);
    }
    document.getElementById('editor').focus();
}

function changeFontFamily() {
    const font = document.getElementById('fontFamily').value;
    document.execCommand('fontName', false, font);
    document.getElementById('editor').focus();
}

function changeFontSize() {
    const size = document.getElementById('fontSize').value;
    // 選択範囲にフォントサイズを適用
    const selection = window.getSelection();
    if (selection.rangeCount > 0) {
        const range = selection.getRangeAt(0);
        const span = document.createElement('span');
        span.style.fontSize = size;
        try {
            range.surroundContents(span);
        } catch (e) {
            // 複雑な選択の場合
            const fragment = range.extractContents();
            span.appendChild(fragment);
            range.insertNode(span);
        }
    }
    document.getElementById('editor').focus();
}

function changeFormat() {
    const format = document.getElementById('formatBlock').value;
    document.execCommand('formatBlock', false, format);
    document.getElementById('editor').focus();
}

function changeTextColor() {
    const color = document.getElementById('textColor').value;
    document.execCommand('foreColor', false, color);
    document.getElementById('editor').focus();
}

function changeBackgroundColor() {
    const color = document.getElementById('bgColor').value;
    document.execCommand('backColor', false, color);
    document.getElementById('editor').focus();
}

function alignText(alignment) {
    document.execCommand(alignment, false, null);
    document.getElementById('editor').focus();
}

function findText() {
    const searchTerm = document.getElementById('searchInput').value;
    if (searchTerm) {
        window.find(searchTerm, false, false, true, false, true, false);
    }
}

function changeZoom() {
    const zoom = document.getElementById('zoomLevel').value;
    document.getElementById('editor').style.zoom = zoom;
}

function insertLink() {
    const url = prompt('リンクURLを入力してください:');
    if (url) {
        document.execCommand('createLink', false, url);
        document.getElementById('editor').focus();
    }
}

function insertImage() {
    const url = prompt('画像URLを入力してください:');
    if (url) {
        document.execCommand('insertImage', false, url);
        document.getElementById('editor').focus();
    }
}

function insertTable() {
    const rows = parseInt(prompt('行数を入力してください:', '3')) || 3;
    const cols = parseInt(prompt('列数を入力してください:', '3')) || 3;
    
    let tableHTML = '<table border="1" style="border-collapse: collapse; width: 100%; margin: 10px 0;">';
    for (let i = 0; i < rows; i++) {
        tableHTML += '<tr>';
        for (let j = 0; j < cols; j++) {
            tableHTML += '<td style="padding: 8px; border: 1px solid #ddd; min-width: 50px;">&nbsp;</td>';
        }
        tableHTML += '</tr>';
    }
    tableHTML += '</table><p></p>';
    
    document.execCommand('insertHTML', false, tableHTML);
    document.getElementById('editor').focus();
}

var isSourceMode = false;
function toggleSourceView() {
    const editor = document.getElementById('editor');
    
    if (!isSourceMode) {
        const content = editor.innerHTML;
        editor.innerHTML = '<textarea style="width:100%;height:400px;font-family:monospace;padding:15px;border:none;resize:none;outline:none;">' + 
            content.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</textarea>';
        editor.contentEditable = 'false';
        isSourceMode = true;
    } else {
        const textarea = editor.querySelector('textarea');
        if (textarea) {
            editor.innerHTML = textarea.value.replace(/&lt;/g, '<').replace(/&gt;/g, '>');
        }
        editor.contentEditable = 'true';
        isSourceMode = false;
    }
}

function clearFormatting() {
    document.execCommand('removeFormat', false, null);
    document.getElementById('editor').focus();
}

// Enterキーで検索
document.getElementById('menu_number_input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        loadMenuContent();
    }
});

// 初期化
document.addEventListener('DOMContentLoaded', function() {
    // カテゴリUI削除済み
    // menu_noクエリがあれば自動セット＆自動検索
    const urlParams = new URLSearchParams(window.location.search);
    const menuNo = urlParams.get('menu_no');
    if (menuNo) {
        document.getElementById('menu_number_input').value = menuNo;
        setTimeout(loadMenuContent, 200); // DOM安定後に自動検索
    }
    console.log('✅ メニュー編集システム 初期化完了');
    console.log('🔧 デバッグ機能を使用するには「デバッグ」ボタンをクリックしてください');
});
</script>

</body>
</html>

<?php get_footer(); ?>