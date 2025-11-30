<?php
add_action('admin_notices', function() {
    global $pagenow;
    if ($pagenow === 'post.php' || $pagenow === 'post-new.php') {
        // 編集画面のURLにreturn_toパラメータがあれば、その値を戻り先に使う
        $return_to = isset($_GET['return_to']) ? urldecode($_GET['return_to']) : '';
        if ($return_to) {
            $menu_url = $return_to;
        } else {
            $menu_page = get_page_by_path('menu-pulic');
            $menu_url = $menu_page ? get_permalink($menu_page->ID) : '';
        }
        if ($menu_url) {
            // A4枠の上部・中央寄せで表示
            echo '<div style="max-width:210mm; margin:32px auto 0 auto; padding:0; text-align:center;">';
            echo '<a href="' . esc_url($menu_url) . '" style="display:inline-block; padding:8px 22px; background:#2196f3; color:#fff; border-radius:8px; font-weight:bold; text-decoration:none; font-size:1em; box-shadow:0 2px 8px rgba(33,150,243,0.15);">MENU一覧に戻る</a>';
            echo '</div>';
        }
    }
});
// 管理画面の投稿編集画面でA4縦サイズCSSを読み込む（クラシックエディタ用）
add_action('admin_enqueue_scripts', function($hook) {
    // 投稿・固定ページの編集画面のみ適用
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        $ver = filemtime(get_stylesheet_directory() . '/admin-editor-a4.css');
        wp_enqueue_style('admin-editor-a4', get_stylesheet_directory_uri() . '/admin-editor-a4.css', [], $ver);
    }
});

// Gutenberg（ブロックエディタ）用A4CSS
add_action('enqueue_block_editor_assets', function() {
    $css_path = get_stylesheet_directory() . '/admin-editor-a4.css';
    if (file_exists($css_path)) {
        wp_enqueue_style('admin-editor-a4-gutenberg', get_stylesheet_directory_uri() . '/admin-editor-a4.css', [], filemtime($css_path));
    }
});
/**
 * Amamikai Theme Functions
 * A4対応統合メニューエディター + 標準テーマ機能
 */

// テーマサポートの設定
function amamikai_theme_setup() {
    // 投稿とページにタイトルタグのサポートを追加
    add_theme_support('title-tag');
    // 投稿サムネイルのサポートを追加
    add_theme_support('post-thumbnails');
    // HTML5マークアップのサポートを追加
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    // フィードリンクのサポートを追加
    add_theme_support('automatic-feed-links');
    // 固定ページのテンプレートセレクターを有効化
    add_theme_support('page-attributes');
}
add_action('after_setup_theme', 'amamikai_theme_setup');

// スタイルとスクリプトの読み込み
function amamikai_enqueue_scripts() {
    // テーマのメインスタイル
    wp_enqueue_style('amamikai-style', get_stylesheet_uri());
    
    // テーマのメインスクリプト
    if (file_exists(get_template_directory() . '/theme.js')) {
        wp_enqueue_script('amamikai-theme', get_template_directory_uri() . '/theme.js', array(), '1.0.0', true);
    }
}
add_action('wp_enqueue_scripts', 'amamikai_enqueue_scripts');

// 管理画面用のスタイルとスクリプト
function amamikai_admin_enqueue_scripts() {
    // エディタースタイル
    if (file_exists(get_template_directory() . '/editor-styles.css')) {
        wp_enqueue_style('amamikai-editor-style', get_template_directory_uri() . '/editor-styles.css');
    }
}
add_action('admin_enqueue_scripts', 'amamikai_admin_enqueue_scripts');

// ==============================================
// A4対応統合メニューエディター機能
// ==============================================

// MENU編集用AJAX処理
add_action('wp_ajax_get_menu_post', 'handle_get_menu_post');
add_action('wp_ajax_nopriv_get_menu_post', 'handle_get_menu_post');
add_action('wp_ajax_save_menu_post', 'handle_save_menu_post');
add_action('wp_ajax_nopriv_save_menu_post', 'handle_save_menu_post');
add_action('wp_ajax_debug_menu_database', 'handle_debug_menu_database');
add_action('wp_ajax_nopriv_debug_menu_database', 'handle_debug_menu_database');

// データベース確認用AJAX処理
function handle_debug_menu_database() {
    global $wpdb;
    
    // menu_noを持つすべての投稿を取得
    $query = "
        SELECT p.ID, p.post_title, pm.meta_value as menu_no
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE pm.meta_key = 'menu_no'
        AND pm.meta_value != ''
        AND p.post_status IN ('publish', 'draft', 'private', 'pending', 'future')
        ORDER BY pm.meta_value
        LIMIT 50
    ";
    
    $results = $wpdb->get_results($query);
    
    $menus = array();
    foreach ($results as $row) {
        $menus[] = array(
            'post_id' => $row->ID,
            'menu_no' => $row->menu_no,
            'title' => $row->post_title
        );
    }
    
    wp_send_json_success(array(
        'menus' => $menus,
        'total' => count($menus)
    ));
}

// Main editor page function
function unified_menu_editor_page() {
    $ajax_url = admin_url('admin-ajax.php');
    $home_url = home_url();
    ?>
    <div class="wrap">
        <h1>📝 統合メニューエディター【A4サイズ対応・完全機能】</h1>
        
        <!-- Menu number input -->
        <div style="background: #f8f9fa; padding: 15px; border: 1px solid #e9ecef; border-radius: 6px; margin-bottom: 15px;">
            <label style="font-weight: bold; margin-right: 10px;">MENU番号:</label>
            <input type="text" id="menu_number_input" placeholder="例: 123" style="padding: 8px 12px; width: 150px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; margin-right: 10px;">
            <button onclick="loadMenuContent()" style="background: #0073aa; color: white; border: none; padding: 8px 20px; border-radius: 4px; cursor: pointer; font-weight: bold;">📖 読み込み</button>
            <span id="current_menu_display" style="color: #666; font-size: 14px; margin-left: 20px;"></span>
        </div>

        <!-- A4 size container (800px) -->
        <div style="max-width: 800px; margin: 0 auto; background: white; padding: 20px; border: 1px solid #ddd; border-radius: 6px;">
            <form id="edit_form">
                <input type="hidden" id="post_id">
                
                <!-- Title field -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; font-size: 16px;">📝 投稿タイトル</label>
                    <input type="text" id="post_title" style="width: 100%; padding: 12px; font-size: 18px; border: 2px solid #0073aa; border-radius: 4px;" placeholder="投稿のタイトルを入力してください">
                </div>
                
                <!-- Content editor -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; font-size: 16px;">📄 投稿本文</label>
                    
                    <!-- Toolbar (4 rows) -->
                    <div style="border: 2px solid #0073aa; border-bottom: none; background: #f8f9fa; padding: 10px; border-radius: 4px 4px 0 0;">
                        <!-- Row 1: Basic tools -->
                        <div style="margin-bottom: 8px;">
                            <button type="button" onclick="document.execCommand('undo')" style="padding: 6px 12px; margin-right: 4px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer;" title="元に戻す">↶</button>
                            <button type="button" onclick="document.execCommand('redo')" style="padding: 6px 12px; margin-right: 8px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer;" title="やり直し">↷</button>
                            
                            <input type="text" id="searchInput" placeholder="検索" style="padding: 6px; width: 100px; margin-right: 4px; border: 1px solid #ddd; border-radius: 3px;">
                            <button type="button" onclick="findText()" style="padding: 6px 12px; margin-right: 8px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer;">🔍</button>
                            
                            <select id="zoomLevel" onchange="changeZoom()" style="padding: 6px; margin-right: 4px; border: 1px solid #ddd; border-radius: 3px;">
                                <option value="0.75">75%</option>
                                <option value="1.0" selected>100%</option>
                                <option value="1.25">125%</option>
                                <option value="1.5">150%</option>
                            </select>
                        </div>
                        
                        <!-- Row 2: Formatting -->
                        <div style="margin-bottom: 8px;">
                            <select id="fontFamily" onchange="changeFontFamily()" style="padding: 6px; margin-right: 4px; border: 1px solid #ddd; border-radius: 3px;">
                                <option value="'Yu Gothic', sans-serif" selected>游ゴシック</option>
                                <option value="Arial, sans-serif">Arial</option>
                                <option value="'MS PGothic', sans-serif">MS Pゴシック</option>
                            </select>
                            
                            <select id="fontSize" onchange="changeFontSize()" style="padding: 6px; margin-right: 8px; border: 1px solid #ddd; border-radius: 3px;">
                                <option value="12px">12px</option>
                                <option value="14px">14px</option>
                                <option value="16px" selected>16px</option>
                                <option value="18px">18px</option>
                                <option value="20px">20px</option>
                                <option value="24px">24px</option>
                            </select>
                            
                            <button type="button" onclick="document.execCommand('bold')" style="padding: 6px 12px; margin-right: 4px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer; font-weight: bold;">B</button>
                            <button type="button" onclick="document.execCommand('italic')" style="padding: 6px 12px; margin-right: 4px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer; font-style: italic;">I</button>
                            <button type="button" onclick="document.execCommand('underline')" style="padding: 6px 12px; margin-right: 8px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer; text-decoration: underline;">U</button>
                            
                            <input type="color" id="textColor" value="#000000" onchange="changeTextColor()" style="width: 32px; height: 32px; margin-right: 4px; border: 1px solid #ddd; border-radius: 3px;">
                            <input type="color" id="backgroundColor" value="#ffff00" onchange="changeBackgroundColor()" style="width: 32px; height: 32px; margin-right: 4px; border: 1px solid #ddd; border-radius: 3px;">
                        </div>
                        
                        <!-- Row 3: Alignment and lists -->
                        <div style="margin-bottom: 8px;">
                            <select id="formatBlock" onchange="changeFormat()" style="padding: 6px; margin-right: 8px; border: 1px solid #ddd; border-radius: 3px;">
                                <option value="p">段落</option>
                                <option value="h1">見出し1</option>
                                <option value="h2">見出し2</option>
                                <option value="h3">見出し3</option>
                                <option value="h4">見出し4</option>
                            </select>
                            
                            <button type="button" onclick="alignText('justifyLeft')" id="alignLeft" style="padding: 6px 12px; margin-right: 4px; border: 1px solid #ddd; background: #0073aa; color: white; border-radius: 3px; cursor: pointer;">⬅</button>
                            <button type="button" onclick="alignText('justifyCenter')" id="alignCenter" style="padding: 6px 12px; margin-right: 4px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer;">🎯</button>
                            <button type="button" onclick="alignText('justifyRight')" id="alignRight" style="padding: 6px 12px; margin-right: 8px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer;">➡</button>
                            
                            <button type="button" onclick="document.execCommand('insertOrderedList')" style="padding: 6px 12px; margin-right: 4px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer;">1.</button>
                            <button type="button" onclick="document.execCommand('insertUnorderedList')" style="padding: 6px 12px; margin-right: 4px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer;">•</button>
                            <button type="button" onclick="document.execCommand('indent')" style="padding: 6px 12px; margin-right: 4px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer;">⟶</button>
                            <button type="button" onclick="document.execCommand('outdent')" style="padding: 6px 12px; margin-right: 4px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer;">⟵</button>
                        </div>
                        
                        <!-- Row 4: Insert and special -->
                        <div>
                            <button type="button" onclick="insertLink()" style="padding: 6px 12px; margin-right: 4px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer;">🔗</button>
                            <button type="button" onclick="document.execCommand('unlink')" style="padding: 6px 12px; margin-right: 8px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer;">🔗⃠</button>
                            
                            <button type="button" onclick="insertImage()" style="padding: 6px 12px; margin-right: 4px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer;">🖼️</button>
                            <button type="button" onclick="insertTable()" style="padding: 6px 12px; margin-right: 4px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer;">📊</button>
                            <button type="button" onclick="document.execCommand('insertHorizontalRule')" style="padding: 6px 12px; margin-right: 8px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer;">—</button>
                            
                            <button type="button" onclick="document.execCommand('removeFormat')" style="padding: 6px 12px; margin-right: 4px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer;">T✗</button>
                            <button type="button" onclick="toggleSourceView()" id="sourceBtn" style="padding: 6px 12px; margin-right: 4px; border: 1px solid #ddd; background: white; border-radius: 3px; cursor: pointer;">< ></button>
                        </div>
                    </div>
                    
                    <!-- Editor area -->
                    <div id="editor" contenteditable="true" style="min-height: 400px; border: 2px solid #0073aa; border-top: none; border-radius: 0 0 4px 4px; padding: 15px; font-size: 16px; line-height: 1.6; background: white; outline: none;">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
                
                <!-- Message area -->
                <div id="message_area" style="margin-bottom: 20px;"></div>
                
                <!-- Action buttons -->
                <div style="text-align: center; padding: 20px; background: #f9f9f9; border-radius: 4px; margin-bottom: 30px;">
                    <button type="button" onclick="savePost()" style="background: #0073aa; color: white; border: none; padding: 12px 25px; border-radius: 4px; font-size: 16px; cursor: pointer; margin: 0 8px;">💾 更新</button>
                    <button type="button" onclick="viewPost()" style="background: #00a32a; color: white; border: none; padding: 12px 25px; border-radius: 4px; font-size: 16px; cursor: pointer; margin: 0 8px;">👁️ プレビュー</button>
                    <button type="button" onclick="clearForm()" style="background: #666; color: white; border: none; padding: 12px 25px; border-radius: 4px; font-size: 16px; cursor: pointer; margin: 0 8px;">🗑️ クリア</button>
                </div>
                
                <!-- Meta information section (3 items) -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 6px;">
                    <h3 style="margin-top: 0; color: #0073aa;">📊 投稿情報・ツール</h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                        <div style="background: white; padding: 15px; border-radius: 4px; border: 1px solid #ddd;">
                            <label style="font-weight: bold; display: block; margin-bottom: 8px;">📌 投稿状態</label>
                            <select id="post_status_meta" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                                <option value="publish">公開</option>
                                <option value="draft">下書き</option>
                                <option value="private">非公開</option>
                            </select>
                        </div>
                        
                        <div style="background: white; padding: 15px; border-radius: 4px; border: 1px solid #ddd;">
                            <label style="font-weight: bold; display: block; margin-bottom: 8px;">📅 掲載終了日</label>
                            <input type="date" id="end_date_meta" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                        </div>
                        
                        <div style="background: white; padding: 15px; border-radius: 4px; border: 1px solid #ddd;">
                            <label style="font-weight: bold; display: block; margin-bottom: 8px;">📁 カテゴリー</label>
                            <select id="category_meta" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                                <option value="1">未分類</option>
                                <option value="2">お知らせ</option>
                                <option value="3">イベント</option>
                                <option value="4">活動報告</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Tool buttons -->
                    <div style="background: white; padding: 15px; border-radius: 4px; border: 1px solid #ddd;">
                        <h4 style="margin-top: 0; margin-bottom: 10px;">🔧 編集ツール</h4>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            <button type="button" onclick="insertCurrentDate()" style="padding: 6px 12px; background: #f0f0f1; border: 1px solid #c3c4c7; border-radius: 3px; cursor: pointer; font-size: 12px;">📅 今日の日付</button>
                            <button type="button" onclick="insertTimeStamp()" style="padding: 6px 12px; background: #f0f0f1; border: 1px solid #c3c4c7; border-radius: 3px; cursor: pointer; font-size: 12px;">🕐 タイムスタンプ</button>
                            <button type="button" onclick="insertEventTemplate()" style="padding: 6px 12px; background: #f0f0f1; border: 1px solid #c3c4c7; border-radius: 3px; cursor: pointer; font-size: 12px;">📋 イベント告知</button>
                            <button type="button" onclick="insertReportTemplate()" style="padding: 6px 12px; background: #f0f0f1; border: 1px solid #c3c4c7; border-radius: 3px; cursor: pointer; font-size: 12px;">📝 活動報告</button>
                            <button type="button" onclick="insertNoticeTemplate()" style="padding: 6px 12px; background: #f0f0f1; border: 1px solid #c3c4c7; border-radius: 3px; cursor: pointer; font-size: 12px;">📢 お知らせ</button>
                            <button type="button" onclick="wordCount()" style="padding: 6px 12px; background: #f0f0f1; border: 1px solid #c3c4c7; border-radius: 3px; cursor: pointer; font-size: 12px;">📊 文字数カウント</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script>
    var isSourceMode = false;
    
    function loadMenuContent() {
        const menuNumber = document.getElementById('menu_number_input').value.trim();
        if (!menuNumber) {
            alert('MENU番号を入力してください');
            return;
        }
        
        document.getElementById('current_menu_display').innerHTML = '読み込み中...';
        
        fetch('<?php echo $ajax_url; ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get_menu_post&menu_no=' + encodeURIComponent(menuNumber)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('post_id').value = data.data.ID || '';
                document.getElementById('post_title').value = data.data.post_title || '';
                document.getElementById('editor').innerHTML = data.data.post_content || '';
                document.getElementById('current_menu_display').innerHTML = 'MENU ' + menuNumber + ' 読み込み完了';
                showMessage('MENU番号 ' + menuNumber + ' の内容を読み込みました', 'success');
            } else {
                alert('データの読み込みに失敗しました: ' + (data.data || '不明なエラー'));
                document.getElementById('current_menu_display').innerHTML = '';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('通信エラーが発生しました');
            document.getElementById('current_menu_display').innerHTML = '';
        });
    }
    
    function savePost() {
        const postId = document.getElementById('post_id').value;
        if (!postId) {
            alert('投稿が選択されていません');
            return;
        }
        
        const title = document.getElementById('post_title').value;
        const content = document.getElementById('editor').innerHTML;
        
        const formData = new FormData();
        formData.append('action', 'save_menu_post');
        formData.append('post_id', postId);
        formData.append('post_title', title);
        formData.append('post_content', content);
        
        showMessage('更新処理中...', 'info');
        
        fetch('<?php echo $ajax_url; ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('投稿が正常に更新されました！', 'success');
            } else {
                showMessage('投稿の更新に失敗しました: ' + (data.data || '不明なエラー'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('通信エラーが発生しました', 'error');
        });
    }
    
    function viewPost() {
        const postId = document.getElementById('post_id').value;
        if (postId) {
            window.open('<?php echo $home_url; ?>/?p=' + postId, '_blank');
        }
    }
    
    function clearForm() {
        document.getElementById('menu_number_input').value = '';
        document.getElementById('current_menu_display').innerHTML = '';
        document.getElementById('post_id').value = '';
        document.getElementById('post_title').value = '';
        document.getElementById('editor').innerHTML = '';
        document.getElementById('message_area').innerHTML = '';
    }
    
    function showMessage(text, type) {
        const messageArea = document.getElementById('message_area');
        let bgColor, textColor;
        
        switch(type) {
            case 'success':
                bgColor = '#d4edda';
                textColor = '#155724';
                break;
            case 'error':
                bgColor = '#f8d7da';
                textColor = '#721c24';
                break;
            case 'info':
                bgColor = '#d1ecf1';
                textColor = '#0c5460';
                break;
        }
        
        messageArea.innerHTML = '<div style="background: ' + bgColor + '; color: ' + textColor + '; padding: 10px; border-radius: 4px;">' + text + '</div>';
        if (type !== 'info') {
            setTimeout(() => messageArea.innerHTML = '', 5000);
        }
    }
    
    function alignText(alignment) {
        document.execCommand(alignment);
        document.querySelectorAll('[id^="align"]').forEach(btn => {
            btn.style.background = 'white';
            btn.style.color = 'black';
        });
        const activeBtn = document.getElementById(alignment === 'justifyLeft' ? 'alignLeft' : alignment === 'justifyCenter' ? 'alignCenter' : 'alignRight');
        activeBtn.style.background = '#0073aa';
        activeBtn.style.color = 'white';
    }
    
    function changeFontFamily() {
        document.execCommand('fontName', false, document.getElementById('fontFamily').value);
    }
    
    function changeFontSize() {
        const fontSize = document.getElementById('fontSize').value;
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            const span = document.createElement('span');
            span.style.fontSize = fontSize;
            try {
                range.surroundContents(span);
            } catch (e) {
                span.appendChild(range.extractContents());
                range.insertNode(span);
            }
        }
    }
    
    function changeFormat() {
        document.execCommand('formatBlock', false, document.getElementById('formatBlock').value);
    }
    
    function changeTextColor() {
        document.execCommand('foreColor', false, document.getElementById('textColor').value);
    }
    
    function changeBackgroundColor() {
        document.execCommand('backColor', false, document.getElementById('backgroundColor').value);
    }
    
    function insertLink() {
        const url = prompt('リンクURLを入力してください:');
        if (url) document.execCommand('createLink', false, url);
    }
    
    function insertImage() {
        const url = prompt('画像URLを入力してください:');
        if (url) document.execCommand('insertImage', false, url);
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
        
        insertHtmlAtCursor(tableHTML);
    }
    
    function findText() {
        const searchTerm = document.getElementById('searchInput').value;
        if (searchTerm) {
            window.find(searchTerm, false, false, true, false, true, false);
        }
    }
    
    function changeZoom() {
        document.getElementById('editor').style.zoom = document.getElementById('zoomLevel').value;
    }
    
    function toggleSourceView() {
        const editor = document.getElementById('editor');
        const sourceBtn = document.getElementById('sourceBtn');
        
        if (!isSourceMode) {
            const content = editor.innerHTML;
            editor.innerHTML = '<textarea style="width:100%;height:400px;font-family:monospace;padding:15px;border:none;resize:none;outline:none;">' + 
                content.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</textarea>';
            editor.contentEditable = 'false';
            sourceBtn.style.background = '#0073aa';
            sourceBtn.style.color = 'white';
            isSourceMode = true;
        } else {
            const textarea = editor.querySelector('textarea');
            if (textarea) {
                editor.innerHTML = textarea.value.replace(/&lt;/g, '<').replace(/&gt;/g, '>');
            }
            editor.contentEditable = 'true';
            sourceBtn.style.background = 'white';
            sourceBtn.style.color = 'black';
            isSourceMode = false;
        }
    }
    
    function insertCurrentDate() {
        const today = new Date();
        const dateStr = today.getFullYear() + '年' + (today.getMonth() + 1) + '月' + today.getDate() + '日';
        insertHtmlAtCursor(dateStr);
    }
    
    function insertTimeStamp() {
        const now = new Date();
        const timeStr = '[' + now.toLocaleString('ja-JP') + '] ';
        insertHtmlAtCursor(timeStr);
    }
    
    function insertEventTemplate() {
        const template = '<h3>📅 イベントのお知らせ</h3><p><strong>日時：</strong>令和○年○月○日（○）○時○分～○時○分</p><p><strong>場所：</strong></p><p><strong>内容：</strong></p><p><strong>参加費：</strong></p><p><strong>定員：</strong></p><p><strong>申込方法：</strong></p><p><strong>問い合わせ：</strong></p>';
        insertHtmlAtCursor(template);
    }
    
    function insertReportTemplate() {
        const template = '<h3>📝 活動報告</h3><p><strong>実施日：</strong>令和○年○月○日（○）</p><p><strong>場所：</strong></p><p><strong>参加者数：</strong>○名</p><p><strong>活動内容：</strong></p><p></p><p><strong>成果・感想：</strong></p><p></p>';
        insertHtmlAtCursor(template);
    }
    
    function insertNoticeTemplate() {
        const template = '<h3>📢 お知らせ</h3><p></p><p><strong>詳細：</strong></p><p></p><p><strong>お問い合わせ：</strong></p>';
        insertHtmlAtCursor(template);
    }
    
    function wordCount() {
        const editor = document.getElementById('editor');
        const text = editor.textContent || editor.innerText || '';
        const charCount = text.length;
        const wordCount = text.trim() ? text.trim().split(/\s+/).length : 0;
        alert('文字数: ' + charCount + '\n単語数: ' + wordCount);
    }
    
    function insertHtmlAtCursor(html) {
        const editor = document.getElementById('editor');
        editor.focus();
        
        if (window.getSelection) {
            const selection = window.getSelection();
            if (selection.getRangeAt && selection.rangeCount) {
                const range = selection.getRangeAt(0);
                range.deleteContents();
                
                const el = document.createElement("div");
                el.innerHTML = html;
                const frag = document.createDocumentFragment();
                let lastNode;
                while (el.firstChild) {
                    lastNode = frag.appendChild(el.firstChild);
                }
                range.insertNode(frag);
                
                if (lastNode) {
                    range = range.cloneRange();
                    range.setStartAfter(lastNode);
                    range.collapse(true);
                    selection.removeAllRanges();
                    selection.addRange(range);
                }
            }
        }
    }
    
    console.log('✅ A4対応統合メニューエディター初期化完了');
    </script>
    <?php
}

// 管理画面メニューに統合エディターを追加（安全な方法）
function amamikai_add_admin_menu() {
    // 管理者またはエディター権限をチェック
    if (current_user_can('edit_others_posts') || current_user_can('manage_options')) {
        add_submenu_page(
            'edit.php',  // 投稿メニューの下に追加
            '統合メニューエディター',
            '📝 A4エディター',
            'edit_posts',
            'unified-menu-editor',
            'unified_menu_editor_page'
        );
        
        // 新しい標準エディター対応メニュー編集を追加
        add_submenu_page(
            'edit.php',  // 投稿メニューの下に追加
            '標準エディター メニュー編集',
            '✨ 標準エディター',
            'edit_posts',
            'standard-menu-editor',
            'standard_menu_editor_page'
        );
    }
}

// 標準エディター対応ページ関数
function standard_menu_editor_page() {
    // 管理画面用テンプレートをinclude
    $template_path = get_template_directory() . '/admin-menu-editor-standard.php';
    if (file_exists($template_path)) {
        // WordPressの管理画面スタイルを適用
        echo '<div class="wrap">';
        include $template_path;
        echo '</div>';
    } else {
        echo '<div class="wrap"><h1>標準エディター メニュー編集</h1><p>テンプレートファイルが見つかりません: ' . $template_path . '</p></div>';
    }
}

add_action('admin_menu', 'amamikai_add_admin_menu');

// デバッグメニューは削除しました（権限問題回避のため）

// エディター用のHTMLタグを許可
function amamikai_allow_editor_html_tags($allowed_tags, $context) {
    if ($context === 'post') {
        $allowed_tags['span']['style'] = true;
        $allowed_tags['div']['style'] = true;
        $allowed_tags['p']['style'] = true;
        $allowed_tags['h1']['style'] = true;
        $allowed_tags['h2']['style'] = true;
        $allowed_tags['h3']['style'] = true;
        $allowed_tags['h4']['style'] = true;
        $allowed_tags['table']['style'] = true;
        $allowed_tags['table']['border'] = true;
        $allowed_tags['td']['style'] = true;
        $allowed_tags['tr']['style'] = true;
        $allowed_tags['th']['style'] = true;
    }
    return $allowed_tags;
}
add_filter('wp_kses_allowed_html', 'amamikai_allow_editor_html_tags', 10, 2);

// セキュリティと互換性のためのフィルター
function amamikai_secure_ajax_actions() {
    // AJAX リクエストの検証
    if (defined('DOING_AJAX') && DOING_AJAX) {
        // 権限チェック（manage_optionsまたはedit_posts権限）
        if (isset($_POST['action']) && in_array($_POST['action'], ['get_menu_post', 'save_menu_post'])) {
            if (!current_user_can('manage_options') && !current_user_can('edit_posts')) {
                wp_die('権限がありません');
            }
        }
    }
}
add_action('init', 'amamikai_secure_ajax_actions');

// 既存テーマとの互換性を保つためのヘルパー関数
function amamikai_is_menu_editor_page() {
    return isset($_GET['page']) && $_GET['page'] === 'unified-menu-editor';
}




// ==================================================
// MENU投稿でHTMLタグ除去を無効化（装飾保持） - 修正版
// ==================================================
function amamikai_allow_full_html_for_menu($content) {
    global $post;
    
    // MENU番号を持つ投稿の場合、HTMLフィルターを無効化
    if (isset($post->ID)) {
        $menu_no = get_post_meta($post->ID, 'menu_no', true);
        if (!empty($menu_no) && $menu_no !== '0' && $menu_no !== 'none') {
            // MENU投稿と判定された場合、管理者ならHTMLフィルターを解除
            if (current_user_can('administrator') || current_user_can('edit_posts')) {
                remove_filter('content_save_pre', 'wp_filter_post_kses');
                remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
                error_log('MENU投稿用HTMLフィルター無効化: ' . $menu_no);
            }
        }
    }
    
    return $content;
}
add_filter('content_save_pre', 'amamikai_allow_full_html_for_menu', 0);
add_filter('content_filtered_save_pre', 'amamikai_allow_full_html_for_menu', 0);

// ==================================================
// 管理者用：全投稿でHTMLフィルタリング緩和（追加対策）
// ==================================================
function amamikai_disable_html_filtering_for_admins() {
    // 管理者の場合、より多くのHTMLタグとスタイル属性を許可
    if (current_user_can('administrator') || current_user_can('edit_posts')) {
        // kses（HTMLフィルタリング）の設定を緩和
        add_filter('wp_kses_allowed_html', 'amamikai_expand_allowed_html_tags', 10, 2);
        
        // 管理者の場合はより多くのHTMLを許可
        if (current_user_can('administrator')) {
            // 一時的にフィルターを無効化（管理者のみ）
            remove_filter('content_save_pre', 'wp_filter_post_kses');
            remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
            remove_filter('excerpt_save_pre', 'wp_filter_post_kses');
        }
    }
}
add_action('init', 'amamikai_disable_html_filtering_for_admins');

// 許可するHTMLタグとスタイル属性を大幅拡張
function amamikai_expand_allowed_html_tags($allowed, $context) {
    if ($context === 'post') {
        // 基本的なタグにstyle属性を追加
        $basic_tags = ['p', 'br', 'strong', 'em', 'u', 'span', 'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
        
        foreach ($basic_tags as $tag) {
            $allowed[$tag]['style'] = true;
            $allowed[$tag]['class'] = true;
            $allowed[$tag]['id'] = true;
        }
        
        // font タグも許可
        $allowed['font'] = array(
            'color' => true,
            'face' => true,
            'size' => true,
            'style' => true
        );
        
        error_log('HTML許可タグ拡張完了');
    }
    
    return $allowed;
}

// ==================================================
// 究極の対策：テーマレベルでのHTMLフィルタリング完全無効化
// ==================================================
function amamikai_ultimate_html_preservation() {
    // 管理者の場合、すべてのHTMLフィルタリングを無効化
    if (is_admin() && (current_user_can('administrator') || current_user_can('edit_posts'))) {
        
        // WordPressの主要なHTMLフィルターを除去
        $filters_to_remove = [
            'content_save_pre' => ['wp_filter_post_kses', 'wp_filter_nohtml_kses'],
            'content_filtered_save_pre' => ['wp_filter_post_kses', 'wp_filter_nohtml_kses'],
            'excerpt_save_pre' => ['wp_filter_post_kses'],
            'the_content' => ['wpautop', 'wptexturize'] // 自動整形も無効化
        ];
        
        foreach ($filters_to_remove as $hook => $functions) {
            foreach ($functions as $function) {
                remove_filter($hook, $function);
            }
        }
        
        error_log('究極のHTMLフィルタリング無効化完了');
        
        // unfiltered_htmlを強制的に許可
        add_filter('user_has_cap', function($caps, $cap, $args) {
            if (in_array('unfiltered_html', $cap)) {
                $caps['unfiltered_html'] = true;
            }
            return $caps;
        }, 10, 3);
    }
}
add_action('admin_init', 'amamikai_ultimate_html_preservation');
add_action('init', 'amamikai_ultimate_html_preservation');

// ==================================================
// MENU投稿専用のHTMLフィルタリング完全回避システム
// ==================================================
function amamikai_bypass_menu_html_filtering($data, $postarr) {
    // MENU投稿（menu_noがある投稿）の場合
    if (isset($postarr['menu_no']) || (isset($postarr['ID']) && get_post_meta($postarr['ID'], 'menu_no', true))) {
        
        error_log('MENU投稿のHTMLフィルタリングを回避: ' . ($postarr['ID'] ?? 'new'));
        
        // HTMLフィルタリングを一時的に完全無効化
        remove_filter('content_save_pre', 'wp_filter_post_kses');
        remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
        remove_filter('content_save_pre', 'wp_filter_nohtml_kses');
        
        // post_contentをそのまま通す
        if (isset($data['post_content'])) {
            // HTMLエンティティのデコード
            $data['post_content'] = html_entity_decode($data['post_content'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
    }
    
    return $data;
}
add_filter('wp_insert_post_data', 'amamikai_bypass_menu_html_filtering', 1, 2);

// 投稿保存後にフィルターを復元（他の投稿への影響を防ぐ）
function amamikai_restore_html_filtering_after_save($post_id) {
    if (get_post_meta($post_id, 'menu_no', true)) {
        // フィルターを復元
        add_filter('content_save_pre', 'wp_filter_post_kses');
        add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
        add_filter('content_save_pre', 'wp_filter_nohtml_kses');
        
        error_log('MENU投稿保存後、HTMLフィルタリングを復元: ' . $post_id);
    }
}
add_action('save_post', 'amamikai_restore_html_filtering_after_save');

// ==================================================
// シンプル・標準設定：管理者のみHTMLフィルタリングを緩和
// ==================================================
function amamikai_simple_html_filter_for_admin() {
    // 管理者のみに限定してHTMLフィルタリングを緩和
    if (current_user_can('administrator')) {
        // 管理者にunfiltered_html権限を付与
        add_filter('user_has_cap', function($caps, $cap, $args) {
            if (in_array('unfiltered_html', $cap) && current_user_can('administrator')) {
                $caps['unfiltered_html'] = true;
            }
            return $caps;
        }, 10, 3);
    }
}

// ==================================================
// 最小限で確実なHTML保持設定
// ==================================================

// 管理者権限の強化（WordPress起動後に実行）
add_action('wp_loaded', 'amamikai_enable_admin_html', 999);
function amamikai_enable_admin_html() {
    // 管理者ロールにunfiltered_html権限を付与
    $role = get_role('administrator');
    if ($role && !$role->has_cap('unfiltered_html')) {
        $role->add_cap('unfiltered_html');
    }
}

// 管理者の権限チェックを強制
add_filter('user_has_cap', 'amamikai_force_admin_html_cap', 10, 4);
function amamikai_force_admin_html_cap($allcaps, $caps, $args, $user) {
    // 管理者の場合、unfiltered_html権限を強制的に付与
    if (isset($allcaps['administrator']) && $allcaps['administrator']) {
        $allcaps['unfiltered_html'] = true;
    }
    return $allcaps;
}

// ==================================================
// 【一時的に無効化】エラー回避のため削除
// ==================================================

// ==================================================
// 【エラー回避のため一時的に削除】
// ==================================================

// ==================================================
// 【削除済み】複雑なHTMLフィルタリング設定は削除しました
// 標準のWordPress設定を使用します
// ==================================================

// ==============================================
// 【削除済み】複雑なHTMLフィルタリング設定は削除しました
// WordPress標準設定に戻りました
// ==============================================

// ==============================================
// 【削除済み】権限設定も標準に戻しました
// ==============================================

// ==============================================
// 【削除済み】TinyMCE設定も標準に戻しました
// WordPressの標準エディター設定を使用します
// ==============================================

// MENU編集用AJAX処理
add_action('wp_ajax_get_menu_post', 'handle_get_menu_post');
add_action('wp_ajax_save_menu_post', 'handle_save_menu_post');
add_action('wp_ajax_test_db_connection', 'handle_test_db_connection');

// データベース接続テスト
function handle_test_db_connection() {
    global $wpdb;
    
    $test_data = array(
        'wordpress_loaded' => true,
        'db_connected' => false,
        'table_prefix' => $wpdb->prefix,
        'posts_count' => 0,
        'postmeta_count' => 0,
        'menu_posts_count' => 0
    );
    
    // データベース接続確認
    if ($wpdb->dbh) {
        $test_data['db_connected'] = true;
        
        // 投稿数を取得
        $posts_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status IN ('publish', 'draft', 'private', 'pending')");
        $test_data['posts_count'] = intval($posts_count);
        
        // メタデータ数を取得
        $meta_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->postmeta}");
        $test_data['postmeta_count'] = intval($meta_count);
        
        // MENU投稿数を取得
        $menu_count = $wpdb->get_var("
            SELECT COUNT(DISTINCT p.ID)
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE pm.meta_key = 'menu_no'
            AND pm.meta_value != ''
            AND p.post_status IN ('publish', 'draft', 'private', 'pending')
        ");
        $test_data['menu_posts_count'] = intval($menu_count);
    }
    
    wp_send_json_success($test_data);
}

// MENU投稿取得処理
function handle_get_menu_post() {
    $menu_no = isset($_POST['menu_no']) ? sanitize_text_field($_POST['menu_no']) : '';
    if (empty($menu_no)) {
        wp_send_json_error('MENU番号が指定されていません');
        return;
    }
    $menu_no_normalized = trim(mb_convert_kana($menu_no, 'as'));
    global $wpdb;
    // 全てのmenu_noを正規化して比較
    $all_posts = $wpdb->get_results("
        SELECT p.* , pm.meta_value as menu_no
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE pm.meta_key = 'menu_no'
        AND pm.meta_value != ''
        AND p.post_status IN ('publish', 'draft', 'private', 'pending', 'future')
    ");
    $found = null;
    foreach ($all_posts as $post) {
        $db_menu_no = trim(mb_convert_kana($post->menu_no, 'as'));
        if ($db_menu_no === $menu_no_normalized) {
            $found = $post;
            break;
        }
    }
    if (!$found) {
        // 部分一致も試す
        foreach ($all_posts as $post) {
            $db_menu_no = trim(mb_convert_kana($post->menu_no, 'as'));
            if (strpos($db_menu_no, $menu_no_normalized) !== false) {
                $found = $post;
                break;
            }
        }
    }
    if (!$found) {
        $existing_menus = array_map(function($p){ return trim(mb_convert_kana($p->menu_no, 'as')); }, $all_posts);
        $menu_list = array_slice($existing_menus, 0, 10);
        wp_send_json_error('MENU番号「' . $menu_no . '」の投稿が見つかりません。<br>データベース内の最初の10件: ' . implode(', ', $menu_list));
        return;
    }
    $menu_no_meta = get_post_meta($found->ID, 'menu_no', true);
    $end_date = get_post_meta($found->ID, 'end_date', true);
    $editor_name = get_post_meta($found->ID, 'editor_name', true);
    $response_data = array(
        'ID' => $found->ID,
        'post_title' => $found->post_title,
        'post_content' => $found->post_content,
        'post_status' => $found->post_status,
        'menu_no' => $menu_no_meta,
        'end_date' => $end_date,
        'editor_name' => $editor_name
    );
    wp_send_json_success($response_data);
}

// MENU投稿保存処理
function handle_save_menu_post() {
    if (!wp_verify_nonce($_POST['nonce'], 'menu_editor_nonce')) {
        wp_send_json_error('セキュリティチェックに失敗しました');
        return;
    }
    
    $post_id = intval($_POST['post_id']);
    $menu_no = sanitize_text_field($_POST['menu_no']);
    $post_title = sanitize_text_field($_POST['post_title']);
    $post_content = $_POST['post_content'];
    $post_status = sanitize_text_field($_POST['post_status']);
    $end_date = sanitize_text_field($_POST['end_date']);
    $editor_name = sanitize_text_field($_POST['editor_name']);

    
    // 投稿データ準備
    $post_data = array(
        'post_title' => $post_title,
        'post_content' => $post_content,
        'post_status' => $post_status
    );
    
    if ($post_id > 0) {
        $post_data['ID'] = $post_id;
        $result = wp_update_post($post_data);
    } else {
        $post_data['post_type'] = 'post';
        $result = wp_insert_post($post_data);
    }
    
    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
        return;
    }
    
    // メタデータ保存
    update_post_meta($result, 'menu_no', $menu_no);
    update_post_meta($result, 'end_date', $end_date);
    update_post_meta($result, 'editor_name', $editor_name);
    

    
    wp_send_json_success(array(
        'ID' => $result,
        'message' => '保存完了'
    ));
}
// jQuery Migrateの問題を回避
function disable_jquery_migrate($scripts) {
    if (!is_admin() && !empty($scripts->registered['jquery'])) {
        $scripts->registered['jquery']->deps = array_diff(
            $scripts->registered['jquery']->deps,
            ['jquery-migrate']
        );
    }
}
add_action('wp_default_scripts', 'disable_jquery_migrate');
/**
 * 管理画面のヘッダーにカスタムCSSを挿入し、非表示になっているメニュー名を強制的に表示する
 * - メニュー幅の制御はWordPress本体に任せ、テキスト表示に特化して競合を避ける
 */
function custom_admin_menu_text_fix() {
    echo '
    <style type="text/css">
        /* アイコンのみ表示される問題 (メニュー名が消える問題) を解決するための最終CSS */
        
        /* 1. テキスト要素自体を強制的に表示状態にする */
        #adminmenu .wp-menu-text,
        #adminmenu .menu-title {
            display: inline !important;         /* テキストをインライン表示 */
            visibility: visible !important;     /* 可視性を強制 */
            opacity: 1 !important;              /* 透明度を強制 */
            
            /* 2. テキストを画面外に隠す設定をすべて無効化 (最も重要) */
            text-indent: 0 !important;          /* テキストのオフスクリーン移動を無効化 */
            white-space: normal !important;     /* テキストが途中で切れないようにする */
            overflow: visible !important;       /* オーバーフロー隠しを無効化 */
            width: auto !important;             /* 幅を自動調整に戻す */
            height: auto !important;            /* 高さを自動調整に戻す */

            /* 3. テキストの色の問題に対処 */
            color: inherit !important;          /* 親要素から色を継承 (または #f0f0f0 !important も可) */
        }

        /* 4. メニューアイテムのリンク全体に対して、テキストを表示できるだけの十分な幅を確保 */
        #adminmenu li.menu-top a {
            padding-right: 12px; /* アイコンとテキストの間に少しスペースを確保 */
        }

    </style>';
}
add_action('admin_head', 'custom_admin_menu_text_fix');
/***************************************
 * MENU番号から投稿を取得（Ajax）
 ***************************************/
add_action('wp_ajax_get_menu_post', 'get_menu_post_callback');
add_action('wp_ajax_nopriv_get_menu_post', 'get_menu_post_callback');

function get_menu_post_callback() {

    header('Content-Type: application/json; charset=utf-8');

    // MENU番号受取
    $menu_no = isset($_POST['menu_no']) ? sanitize_text_field($_POST['menu_no']) : '';

    if (!$menu_no) {
        echo json_encode([
            'success' => false,
            'message' => 'MENU番号が受信できませんでした'
        ]);
        wp_die();
    }

    // 🔍 デバッグ表示
    error_log("AJAX受信 → menu_no = {$menu_no}");

    // ACF フィールド menu_no を検索
    $args = [
        'post_type' => 'post',
        'meta_query' => [
            [
                'key' => 'menu_no',
                'value' => $menu_no,
                'compare' => '='
            ],
        ],
        'posts_per_page' => 1
    ];

    $posts = get_posts($args);

    if (!$posts) {
        error_log("該当投稿なし → {$menu_no}");
        echo json_encode([
            'success' => false,
            'message' => "MENU番号「{$menu_no}」の投稿が見つかりません"
        ]);
        wp_die();
    }

    $post = $posts[0];

    // 正常時レスポンス
    echo json_encode([
        'success' => true,
        'data' => [
            'ID'           => $post->ID,
            'post_title'   => $post->post_title,
            'post_content' => $post->post_content,
            'menu_no'      => get_field('menu_no', $post->ID),
            'end_date'     => get_field('end_date', $post->ID),
        ]
    ]);

    wp_die();
}


/****************************************
 * MENU内容更新（Ajax）
 ****************************************/
add_action('wp_ajax_update_menu_post', 'update_menu_post_callback');

function update_menu_post_callback() {

    header('Content-Type: application/json; charset=utf-8');

    $post_id = intval($_POST['post_id']);

    $title   = sanitize_text_field($_POST['post_title']);
    $content = wp_kses_post($_POST['post_content']);
    $menu_no = sanitize_text_field($_POST['menu_no']);
    // menu_noを保存時に正規化
    $menu_no = trim(mb_convert_kana($menu_no, 'as'));
    $end_date = sanitize_text_field($_POST['end_date']);

    if (!$post_id) {
        echo json_encode(['success' => false, 'message' => 'post_id がありません']);
        wp_die();
    }

    // 投稿更新
    wp_update_post([
        'ID'           => $post_id,
        'post_title'   => $title,
        'post_content' => $content,
    ]);

    // ACF 更新
    update_field('menu_no', $menu_no, $post_id);
    update_field('end_date', $end_date, $post_id);

    echo json_encode(['success' => true, 'message' => '更新完了']);
    wp_die();
}

function menu_edit_enqueue() {
    wp_enqueue_script(
        'menu-edit-js',
        get_template_directory_uri() . '/js/menu-edit.js',
        array('jquery'),
        null,
        true
    );

    wp_localize_script(
        'menu-edit-js',
        'menu_edit_ajax',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('menu_edit_nonce')
        )
    );
}
add_action('wp_enqueue_scripts', 'menu_edit_enqueue');

// --- A4メニューエディター用 Ajax ハンドラ ---

add_action( 'wp_ajax_amamikai_get_menu', 'amamikai_get_menu' );
add_action( 'wp_ajax_nopriv_amamikai_get_menu', 'amamikai_get_menu' ); // 必要なら許可

function amamikai_get_menu() {
    // nonce チェック
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'amamikai_menu_edit' ) ) {
        wp_send_json_error( array( 'message' => 'セキュリティチェックに失敗しました' ), 403 );
    }

    $menu_no = isset($_POST['menu_no']) ? sanitize_text_field( $_POST['menu_no'] ) : '';
    if ( empty( $menu_no ) ) {
        wp_send_json_error( array( 'message' => 'MENU番号が指定されていません' ) );
    }

    // menu_no メタで投稿を検索（投稿タイプ: post を想定）
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 1,
        'meta_query'     => array(
            array(
                'key'     => 'menu_no',
                'value'   => $menu_no,
                'compare' => '='
            )
        )
    );
    $q = new WP_Query( $args );

    if ( $q->have_posts() ) {
        $post = $q->posts[0];

        $data = array(
            'ID'           => $post->ID,
            'post_title'   => $post->post_title,
            'post_content' => $post->post_content,
            'post_status'  => $post->post_status,
            'menu_no'      => get_post_meta( $post->ID, 'menu_no', true ),
            'editor_name'  => get_post_meta( $post->ID, 'editor_name', true ),
            'end_date'     => get_post_meta( $post->ID, 'end_date', true ),
            'categories'   => wp_get_post_categories( $post->ID )
        );

        wp_send_json_success( $data );
    } else {
        wp_send_json_error( array( 'message' => '該当データが見つかりません' ) );
    }
}

// --- 更新用ハンドラ（既存投稿のみ更新） ---
add_action( 'wp_ajax_amamikai_update_menu', 'amamikai_update_menu' );
add_action( 'wp_ajax_nopriv_amamikai_update_menu', 'amamikai_update_menu' ); // 必要なら許可

function amamikai_update_menu() {
    // nonce チェック
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'amamikai_menu_edit' ) ) {
        wp_send_json_error( array( 'message' => 'セキュリティチェックに失敗しました' ), 403 );
    }

    // 権限チェック（必要なら）
    if ( ! current_user_can( 'edit_posts' ) ) {
        // ログイン済み編集者のみ更新させたい場合はここで制限
        // wp_send_json_error( array( 'message' => '権限がありません' ), 403 );
        // ここでは制限せずに処理する（必要なら上のコメントを有効化）
    }

    $post_id     = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
    $post_title  = isset( $_POST['post_title'] ) ? sanitize_text_field( $_POST['post_title'] ) : '';
    $post_content= isset( $_POST['post_content'] ) ? wp_kses_post( $_POST['post_content'] ) : '';
    $menu_no     = isset( $_POST['menu_no'] ) ? sanitize_text_field( $_POST['menu_no'] ) : '';
    // menu_noを保存時に正規化
    $menu_no = trim(mb_convert_kana($menu_no, 'as'));
    $end_date    = isset( $_POST['end_date'] ) ? sanitize_text_field( $_POST['end_date'] ) : '';
    $editor_name = isset( $_POST['editor_name'] ) ? sanitize_text_field( $_POST['editor_name'] ) : '';
    $category    = isset( $_POST['category'] ) ? intval( $_POST['category'] ) : 0;

    if ( ! $post_id ) {
        wp_send_json_error( array( 'message' => '更新対象の投稿IDがありません' ) );
    }

    // まず投稿が実在するか
    $post = get_post( $post_id );
    if ( ! $post ) {
        wp_send_json_error( array( 'message' => '対象の投稿が見つかりません（ID:' . $post_id . '）' ) );
    }

    // 投稿の meta menu_no を取得して入力キーと整合性をチェック（必須条件）
    $current_menu_no = get_post_meta( $post_id, 'menu_no', true );
    if ( $current_menu_no !== $menu_no && ! empty( $current_menu_no ) ) {
        // 既存の menu_no と一致しない場合は更新しない（セーフティ）
        wp_send_json_error( array( 'message' => 'menu_no が一致しません（DB: ' . $current_menu_no . ' / 入力: ' . $menu_no . '）' ) );
    }

    // 実際の更新
    $update_args = array(
        'ID'           => $post_id,
        'post_title'   => $post_title,
        'post_content' => $post_content,
    );

    $updated_id = wp_update_post( $update_args, true );

    if ( is_wp_error( $updated_id ) ) {
        wp_send_json_error( array( 'message' => '投稿更新エラー: ' . $updated_id->get_error_message() ) );
    }

    // メタデータ更新
    update_post_meta( $post_id, 'menu_no', $menu_no );
    update_post_meta( $post_id, 'end_date', $end_date );
    update_post_meta( $post_id, 'editor_name', $editor_name );

    // カテゴリ更新（単一カテゴリ指定）
    if ( $category ) {
        wp_set_post_categories( $post_id, array( $category ) );
    }

    // 最終的なレスポンス
    $resp = array( 'ID' => $post_id );
    wp_send_json_success( $resp );
}
function tokyo_amamikai_enqueue_assets() {
    // メインのスタイルシート
    wp_enqueue_style('tokyo-style', get_template_directory_uri() . '/style.css');

    // 追加のCSS
    wp_enqueue_style('tokyo-editor-style', get_template_directory_uri() . '/editor-styles.css');
    wp_enqueue_style('tokyo-admin-editor-a4', get_template_directory_uri() . '/admin-editor-a4.css');

    // JSファイルの読み込み（依存関係に jQuery を指定）
    wp_enqueue_script('tokyo-edit', get_template_directory_uri() . '/edit.js', array('jquery'), null, true);
    wp_enqueue_script('tokyo-menu-edit', get_template_directory_uri() . '/menu-edit.js', array('jquery'), null, true);
    wp_enqueue_script('tokyo-menu-list', get_template_directory_uri() . '/menu-list.js', array('jquery'), null, true);
    wp_enqueue_script('tokyo-theme', get_template_directory_uri() . '/theme.js', array('jquery'), null, true);
}




