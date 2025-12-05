<?php
/*
* Template Name: 東京奄美会メニュー（公開用最新）
*/
// FontAwesome CDNをheadに追加(test)
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous">';
get_header();
?>

<style>
body, .tokyo-menu-list {
    font-family: "Yu Gothic", "Hiragino Kaku Gothic ProN", Meiryo, sans-serif;
    color: #222;
}
.tokyo-menu-list {
    max-width:1200px; margin:0 auto; padding:18px 8px 30px 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height:100vh;
}
.tokyo-menu-list h1 {
    background:linear-gradient(45deg, #a78bfa, #8b5cf6);
    color: white;
    text-align:center;
    padding:18px 0;
    margin:0 0 18px 0;
    border-radius:12px;
    font-size:1.8em;
    text-shadow:1px 1px 3px rgba(0,0,0,0.3);
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
}

.history-mode-buttons {
    text-align:center;
    margin-bottom:20px;
}
.history-mode-buttons a {
    display:inline-block;
    padding:10px 22px;
    margin:0 5px;
    background:#555;
    color:#fff;
    border-radius:8px;
    font-weight:bold;
    text-decoration:none;
    font-size:15px;
    box-shadow:0 3px 8px rgba(0,0,0,0.2);
    transition:all 0.3s ease;
}
.history-mode-buttons a:hover {
    opacity:0.9;
    transform:translateY(-2px);
}
.history-mode-buttons a.active {
    background: #ff6b6b;
}
.history-mode-buttons {
    transform: translateY(-2px);
}
.history-list {
    background: rgba(255,255,255,0.95);
    border-radius: 10px;
    padding: 20px;
    margin: 15px 0;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.history-list h3 {
    color: #333;
    border-bottom: 2px solid #4facfe;
    padding-bottom: 8px;
    margin-bottom: 15px;
}
.history-list ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.history-list li {
    padding: 10px 15px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.history-list li:last-child {
    border-bottom: none;
}
.history-list li a {
    color: #1e90ff;
    text-decoration: none;
    font-weight: bold;
}
.history-list li .date {
    color: #666;
    font-size: 0.9em;
}
</style>

<?php
// モード判定（履歴表示モードかどうか）
$mode = isset($_GET['mode']) ? sanitize_text_field($_GET['mode']) : 'latest';

// menu_noパラメータがあれば詳細画面として表示
if (isset($_GET['menu_no']) && $_GET['menu_no'] !== '') {
    $menu_no = sanitize_text_field($_GET['menu_no']);
    
    // 履歴表示モードの場合
    if ($mode === 'history') {
        $args = [
            'post_type' => 'post',
            'meta_key' => 'menu_no',
            'meta_value' => $menu_no,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => -1
        ];
        $query = new WP_Query($args);
        
        if ($query->have_posts()) {
            // 遷移元(return_to)があれば戻る。なければボタン非表示。
            $list_url = isset($_GET['return_to']) ? urldecode($_GET['return_to']) : '';
            
            echo '<div style="max-width:900px; margin:40px auto; background:#fff; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,0.12); padding:0;">';
            echo '<div style="padding:24px 32px 0 32px;">';
            // 履歴モード用のナビゲーション
            echo '<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">';
            if ($list_url) {
                echo '<a href="' . esc_url($list_url) . '" style="padding:8px 22px; background:#2196f3; color:#fff; border-radius:8px; font-weight:bold; text-decoration:none; font-size:1em; box-shadow:0 2px 8px rgba(33,150,243,0.15);">一覧に戻る</a>';
            }
            echo '<a href="' . esc_url(add_query_arg('mode', 'latest', $_SERVER['REQUEST_URI'])) . '" style="padding:8px 22px; background:#4caf50; color:#fff; border-radius:8px; font-weight:bold; text-decoration:none; font-size:1em; box-shadow:0 2px 8px rgba(76,175,80,0.15);">最新版を表示</a>';
            echo '</div>';
            
            echo '<div class="history-list">';
            echo '<h3><i class="fas fa-history"></i> MENU番号「' . esc_html($menu_no) . '」の履歴一覧</h3>';
            echo '<ul>';
            
            while ($query->have_posts()) : $query->the_post();
                $post_id = get_the_ID();
                $post_title = get_the_title();
                $post_date = get_the_date('Y-m-d');
                $editor_name = get_post_meta($post_id, 'editor_name', true);
                $end_date = get_post_meta($post_id, 'keisai_end_date', true);
                
                echo '<li>';
                echo '<div>';
                echo '<a href="' . get_permalink() . '">' . esc_html($post_title) . '</a>';
                echo '<div style="font-size:12px; color:#666; margin-top:4px;">';
                echo '投稿日: ' . esc_html($post_date);
                if (!empty($editor_name)) {
                    echo ' | 編集者: ' . esc_html($editor_name);
                }
                if (!empty($end_date)) {
                    echo ' | 掲載最終日: ' . esc_html($end_date);
                }
                echo '</div>';
                echo '</div>';
                echo '<span class="date">' . esc_html($post_date) . '</span>';
                echo '</li>';
            endwhile;
            
            echo '</ul>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            
            wp_reset_postdata();
        } else {
            echo '<div style="text-align:center; padding:60px; color:#e74c3c; font-size:20px;">該当するMENUが見つかりません</div>';
        }
        
        get_footer();
        return;
    }
    
    // 通常モード（最新1件のみ表示）
    $args = [
        'post_type' => 'post',
        'meta_key' => 'menu_no',
        'meta_value' => $menu_no,
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'orderby' => 'date',
        'order' => 'DESC'
    ];
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        $post_title = get_the_title();
        $post_content = get_the_content();
        $post_author = get_the_author();
        
        // 掲載最終日フィールドを「keisai_end_date」に変更
        $end_date = get_post_meta($post_id, 'keisai_end_date', true);
        $end_date_display = '';
        if ($end_date !== '' && $end_date !== false) {
            $end_date_display = esc_html($end_date);
        }
        
        $editor_name = get_post_meta($post_id, 'editor_name', true);
        $post_date = get_the_date('Y-m-d');
        // 遷移元(return_to)があれば戻る。なければボタン非表示。
        $list_url = isset($_GET['return_to']) ? urldecode($_GET['return_to']) : '';
        
        echo '<div style="max-width:900px; margin:40px auto; background:#fff; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,0.12); padding:0;">';
        echo '<div style="padding:24px 32px 0 32px;">';
        
        // 履歴表示ボタンを追加
        echo '<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">';
        if ($list_url) {
            echo '<a href="' . esc_url($list_url) . '" style="padding:8px 22px; background:#2196f3; color:#fff; border-radius:8px; font-weight:bold; text-decoration:none; font-size:1em; box-shadow:0 2px 8px rgba(33,150,243,0.15);">一覧に戻る</a>';
        }
        echo '<a href="' . esc_url(add_query_arg('mode', 'history', $_SERVER['REQUEST_URI'])) . '" style="padding:8px 22px; background:#ff9800; color:#fff; border-radius:8px; font-weight:bold; text-decoration:none; font-size:1em; box-shadow:0 2px 8px rgba(255,152,0,0.15);"><i class="fas fa-history"></i> 履歴を表示</a>';
        echo '</div>';
        
        echo '<div style="font-size:13px; color:#2196f3; font-weight:bold; margin-bottom:10px; display:flex; gap:18px; flex-wrap:wrap;">';
        echo '<span>投稿日: ' . esc_html($post_date) . '</span>';
        echo '<span>MENU番号: ' . esc_html($menu_no) . '</span>';
        echo '<span>投稿者: ' . esc_html($post_author) . '</span>';
        echo '<span>編集者: ' . esc_html($editor_name) . '</span>';
        echo '<span>掲載最終日: ' . $end_date_display . '</span>';
        echo '</div>';
        
        echo '<h1 style="font-size:2.1em; font-weight:bold; margin:0 0 18px 0; color:#0d235a; background:#ffe0b2; padding:8px 0; text-align:center; border-radius:10px; letter-spacing:0.04em;">' . esc_html($post_title) . '</h1>';
        
        // 投稿本文内の特定キーワードでアイコンクラスを自動付与
        $content_with_icons = $post_content;
        // 例: 「制定」行にカレンダー、「改訂」行にペン
        $content_with_icons = preg_replace('/<p>(\s*制定)/u', '<p class="calendar-icon">$1', $content_with_icons);
        $content_with_icons = preg_replace('/<p>(\s*改訂)/u', '<p class="pen-icon">$1', $content_with_icons);
        
        echo '<div class="tokyo-menu-a4-content">';
        echo apply_filters('the_content', $content_with_icons);
        echo '</div>';
        echo '</div>';
        echo '</div>';
        
        wp_reset_postdata();
    } else {
        echo '<div style="text-align:center; padding:60px; color:#e74c3c; font-size:20px;">該当するMENUが見つかりません</div>';
    }
    
    get_footer();
    return;
}

// ===== 一覧画面ロジック（menu_noが無い場合） =====

// 一覧画面タイトル（1行目）
echo '<div class="menu-header-row">';
    echo '<h1>東京奄美会メニュー一覧</h1>'; // 元のコードを復元
echo '</div>';

// 履歴表示モード切り替えボタン（一覧画面用）
echo '<div class="history-mode-buttons">';
if ($mode === 'history') {
    echo '<a href="' . esc_url(remove_query_arg('mode')) . '" class="active">履歴表示モード</a>';
    echo '<a href="' . esc_url(remove_query_arg('mode')) . '">最新のみ表示</a>';
    echo '<p style="color:#fff; margin:8px 0 0 0; font-size:14px;">同じMENU番号の全投稿を表示しています</p>';
} else {
    echo '<a href="' . esc_url(add_query_arg('mode', 'history')) . '">履歴表示モード</a>';
    echo '<a href="' . esc_url(remove_query_arg('mode')) . '" class="active">最新のみ表示</a>';
    echo '<p style="color:#fff; margin:8px 0 0 0; font-size:14px;">各MENU番号の最新投稿のみを表示しています</p>';
}
echo '</div>';

// 検索パラメータを取得
$keyword     = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$author      = isset($_GET['author_name']) ? sanitize_text_field($_GET['author_name']) : '';
$start_year  = isset($_GET['start_year']) ? sanitize_text_field($_GET['start_year']) : '';
$end_year    = isset($_GET['menu_end_year']) ? sanitize_text_field($_GET['menu_end_year']) : '';
$show_menu_no = isset($_GET['show_menu_no']) ? sanitize_text_field($_GET['show_menu_no']) : 'no';
$category    = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';

// 検索条件の基本設定
$args = array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'meta_key' => 'menu_no',
);

// 履歴表示モードの場合は全件表示
if ($mode === 'history') {
    $args['posts_per_page'] = -1;
} else {
    // 通常モードでは最新のみ表示するための特別処理
    // まず全投稿を取得してからMENU番号ごとに最新のみを選別
    $args['posts_per_page'] = -1;
}

// キーワード検索（タイトル、内容、カスタムフィールド）
if (!empty($keyword)) {
    $args['s'] = $keyword;
    // カスタムフィールドも検索対象にする
    add_filter('posts_where', function($where) use ($keyword) {
        global $wpdb;
        if (!empty($keyword)) {
            $like = '%' . $wpdb->esc_like($keyword) . '%';
            $where .= " OR (";
            $where .= $wpdb->prepare("{$wpdb->postmeta}.meta_value LIKE %s", $like);
            $where .= ")";
        }
        return $where;
    });
    
    add_filter('posts_join', function($join) {
        global $wpdb;
        $join .= " LEFT JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id ";
        return $join;
    });
    
    add_filter('posts_distinct', function($distinct) {
        return 'DISTINCT';
    });
}

// 投稿者フィルター
if (!empty($author)) {
    $args['author_name'] = $author;
}

// 投稿年フィルター
if (!empty($start_year)) {
    $args['date_query'] = array(
        array(
            'year' => intval($start_year)
        )
    );
}

// 掲載終了年フィルター
if (!empty($end_year)) {
    if (!isset($args['meta_query'])) {
        $args['meta_query'] = array();
    }
    $args['meta_query'][] = array(
        'key' => 'keisai_end_date',
        'value' => $end_year,
        'compare' => 'LIKE'
    );
}

// カテゴリフィルター
if (!empty($category)) {
    $args['category_name'] = $category;
}

// 検索実行
$query = new WP_Query($args);

// 履歴表示モードでない場合、MENU番号ごとに最新のみを選別
if ($mode !== 'history') {
    $latest_posts_by_menu = array();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $menu_no = get_post_meta($post_id, 'menu_no', true);
            
            // MENU番号が空の場合は個別に扱う
            if (empty($menu_no)) {
                $latest_posts_by_menu['no_menu_' . $post_id] = $post_id;
            } else {
                // 同じMENU番号の投稿がある場合、日付が新しい方を保持
                if (!isset($latest_posts_by_menu[$menu_no]) || 
                    strtotime(get_the_date('Y-m-d H:i:s')) > strtotime(get_the_date('Y-m-d H:i:s', $latest_posts_by_menu[$menu_no]))) {
                    $latest_posts_by_menu[$menu_no] = $post_id;
                }
            }
        }
        wp_reset_postdata();
    }
    $filtered_posts = array_values($latest_posts_by_menu);
} else {
    // 履歴モードの場合はそのまま
    $filtered_posts = array();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $filtered_posts[] = get_the_ID();
        }
        wp_reset_postdata();
    }
}

// 検索フォーム
echo '<form method="get" action="' . esc_url($_SERVER['REQUEST_URI']) . '" style="margin-bottom:15px; background:linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding:12px; border-radius:10px; display:flex; flex-wrap:wrap; align-items:center; gap:6px; box-shadow:0 4px 12px rgba(0,0,0,0.15);">';
echo '<input type="hidden" name="page_id" value="' . get_the_ID() . '">';
if ($mode === 'history') {
    echo '<input type="hidden" name="mode" value="history">';
}
echo '<input type="text" name="s" placeholder="🔍 キーワード検索" value="' . esc_attr($keyword) . '" style="padding:8px 10px; width:180px; background:rgba(255,255,255,0.9); border:2px solid #fff; border-radius:6px; font-size:13px; font-weight:500; box-shadow:0 2px 5px rgba(0,0,0,0.1);">';

echo '<select name="author_name" style="padding:8px 10px; background:rgba(255,255,255,0.9); border:2px solid #fff; border-radius:6px; font-size:13px; font-weight:500; box-shadow:0 2px 5px rgba(0,0,0,0.1);">';
echo '<option value="">👤 投稿者を選択</option>';
$users = get_users(['who' => 'authors']);
foreach ($users as $user) {
    $selected = ($author === $user->user_nicename) ? 'selected' : '';
    echo '<option value="' . esc_attr($user->user_nicename) . '" ' . $selected . '>' . esc_html($user->display_name) . '</option>';
}
echo '</select>';

echo '<select name="category" style="padding:8px 10px; background:rgba(255,255,255,0.9); border:2px solid #fff; border-radius:6px; font-size:13px; font-weight:500; box-shadow:0 2px 5px rgba(0,0,0,0.1);">';
echo '<option value="">📂 カテゴリを選択</option>';
$categories = get_categories(['hide_empty' => true]);
foreach ($categories as $cat) {
    $selected = ($category == $cat->slug) ? 'selected' : '';
    echo '<option value="' . esc_attr($cat->slug) . '" ' . $selected . '>' . esc_html($cat->name) . '</option>';
}
echo '</select>';

$years = range(date('Y'), date('Y') - 10);
echo '<select name="start_year" style="padding:8px 10px; background:rgba(255,255,255,0.9); border:2px solid #fff; border-radius:6px; font-size:13px; font-weight:500; box-shadow:0 2px 5px rgba(0,0,0,0.1);">';
echo '<option value="">📅 投稿日（年）</option>';
foreach ($years as $y) {
    $selected = ($start_year == $y) ? 'selected' : '';
    echo "<option value=\"$y\" $selected>$y</option>";
}
echo '</select>';

echo '<select name="menu_end_year" style="padding:8px 10px; background:rgba(255,255,255,0.9); border:2px solid #fff; border-radius:6px; font-size:13px; font-weight:500; box-shadow:0 2px 5px rgba(0,0,0,0.1);">';
echo '<option value="">⏰ 掲載終了年</option>';
foreach ($years as $y) {
    $selected = ($end_year == $y) ? 'selected' : '';
    echo "<option value=\"$y\" $selected>$y</option>";
}
echo '</select>';

echo '<select name="show_menu_no" style="padding:8px 10px; background:rgba(255,255,255,0.9); border:2px solid #fff; border-radius:6px; font-size:13px; font-weight:500; box-shadow:0 2px 5px rgba(0,0,0,0.1);">';
echo '<option value="no"' . ($show_menu_no === 'no' ? ' selected' : '') . '>🔢 MENU番号: 非表示</option>';
echo '<option value="yes"' . ($show_menu_no === 'yes' ? ' selected' : '') . '>🔢 MENU番号: 表示</option>';
echo '</select>';

echo '<button type="submit" style="padding:8px 16px; background:linear-gradient(45deg, #4facfe, #00f2fe); color:white; border:none; border-radius:6px; font-size:13px; font-weight:bold; cursor:pointer; box-shadow:0 3px 8px rgba(0,0,0,0.2); transition:all 0.3s ease;">検索</button>';
echo '<span style="margin-left:8px; font-weight:bold; color:white; text-shadow:1px 1px 2px rgba(0,0,0,0.5); font-size:14px;">' . count($filtered_posts) . ' 件取得</span>';

if (!empty($keyword) || !empty($author) || !empty($category) || !empty($start_year) || !empty($end_year) || $show_menu_no === 'yes' || $mode === 'history') {
    echo '<a href="' . esc_url(get_permalink()) . '" style="padding:8px 12px; background:rgba(255,255,255,0.2); color:white; text-decoration:none; border-radius:6px; margin-left:8px; font-weight:bold; border:1px solid rgba(255,255,255,0.3); transition:all 0.3s ease;">検索解除</a>';
}
echo '</form>';

if (!empty($filtered_posts)) {
    // 履歴表示モードの場合の特別処理
    if ($mode === 'history') {
        // MENU番号ごとにグループ化
        $menu_map = array();
        foreach ($filtered_posts as $post_id) {
            $menu_no = get_post_meta($post_id, 'menu_no', true);
            if (!empty($menu_no)) {
                $menu_map[$menu_no] = true;
            }
        }
        ksort($menu_map, SORT_NATURAL);
        echo '<div class="history-list">';
        echo '<h3><i class="fas fa-list"></i> MENU番号一覧（履歴表示モード）</h3>';
        echo '<ul>';
        // 履歴画面の現在のURL（パラメータ付き）を取得
        $current_history_url = esc_url($_SERVER['REQUEST_URI']);
        foreach (array_keys($menu_map) as $menu_no) {
            // MENU番号リンクに履歴画面のURLをreturn_toとして付与
            $url = add_query_arg(array('menu_no' => $menu_no, 'mode' => 'history', 'return_to' => urlencode($current_history_url)), get_permalink());
            echo '<li>';
            echo '<a href="' . esc_url($url) . '" style="font-weight:bold; font-size:1.1em;">MENU番号: ' . esc_html($menu_no) . '</a>';
            echo '</li>';
        }
        echo '</ul>';
        echo '</div>';
    } else {
        // 通常モード（カテゴリ別表示）
        $categories_with_posts = array();
        foreach ($filtered_posts as $post_id) {
            $post_categories = get_the_category($post_id);
            if (empty($post_categories)) {
                if (!isset($categories_with_posts['uncategorized'])) {
                    $categories_with_posts['uncategorized'] = [
                        'category' => (object)['name' => '未分類'],
                        'posts' => array()
                    ];
                }
                $categories_with_posts['uncategorized']['posts'][] = $post_id;
            } else {
                foreach ($post_categories as $cat) {
                    $cat_id = $cat->term_id;
                    if (!isset($categories_with_posts[$cat_id])) {
                        $categories_with_posts[$cat_id] = [
                            'category' => $cat,
                            'posts' => array()
                        ];
                    }
                    $categories_with_posts[$cat_id]['posts'][] = $post_id;
                }
            }
        }
        
        foreach ($categories_with_posts as $cat_data) {
            $category = $cat_data['category'];
            $post_ids = $cat_data['posts'];
            
            echo '<section class="menu-category" style="margin-bottom:20px;">';
            echo '<div style="text-align:center; margin:15px 0 10px 0;">';
            echo '<h2 style="background:linear-gradient(45deg, #4facfe, #00f2fe); color:white; padding:10px 20px; border-radius:20px; font-size:18px; font-weight:bold; text-align:center; display:inline-block; min-width:120px; margin:0; box-shadow:0 4px 10px rgba(0,0,0,0.15); text-shadow:1px 1px 2px rgba(0,0,0,0.3);">' . esc_html($category->name) . '</h2>';
            echo '</div>';
            
            $posts_with_menu_no = array();
            foreach ($post_ids as $post_id) {
                $menu_no = get_post_meta($post_id, 'menu_no', true);
                $posts_with_menu_no[] = array('post_id' => $post_id, 'menu_no' => $menu_no);
            }
            
            usort($posts_with_menu_no, function($a, $b) {
                $a_parts = explode('-', $a['menu_no']);
                $b_parts = explode('-', $b['menu_no']);
                for ($i = 0; $i < min(count($a_parts), count($b_parts)); $i++) {
                    $a_num = intval($a_parts[$i]);
                    $b_num = intval($b_parts[$i]);
                    if ($a_num != $b_num) {
                        return $a_num - $b_num;
                    }
                }
                return 0;
            });
            
            echo '<ul class="tokyo-menu-items" style="margin:0; padding:0; display:flex; flex-wrap:wrap; gap:0.6%; justify-content:center;">';
            foreach ($posts_with_menu_no as $post_data) {
                $post_id = $post_data['post_id'];
                $menu_no = $post_data['menu_no'];
                $post_title = get_the_title($post_id);
                $post_content = get_post_field('post_content', $post_id);
                // 履歴画面の現在のURL（パラメータ付き）を取得
                $current_history_url = esc_url($_SERVER['REQUEST_URI']);
                // MENU番号リンクに履歴画面のURLをreturn_toとして付与
               $menu_link = add_query_arg(
                    array('menu_no' => $menu_no, 'return_to' => urlencode($current_history_url)),
                    get_permalink()
                );

                $is_content_empty = empty(trim(strip_tags($post_content)));
                $border_color = $is_content_empty ? '#ff69b4' : '#87cefa';

                if ($show_menu_no === 'yes' && !empty($menu_no)) {
                    $display_title = '<span style="font-size:11px; color:#666; margin-right:4px;">' . esc_html($menu_no) . '</span>' . esc_html($post_title);
                } else {
                    $display_title = esc_html($post_title);
                }

                $edit_button = '';
                if (current_user_can('edit_post', $post_id)) {
                    // 編集画面にも履歴画面のURLをreturn_toとして付与
                    $edit_link = add_query_arg('return_to', urlencode($current_history_url), get_edit_post_link($post_id));
                    $edit_button = '<a href="' . esc_url($edit_link) . '" style="margin-left:6px; padding:2px 10px; background:#ff9800; color:#fff; border-radius:6px; font-size:12px; font-weight:bold; text-decoration:none; vertical-align:middle; display:inline-block;">編集</a>';
                }

                echo '<li style="list-style:none; margin:4px 0; width:24%; text-align:center;">';
                echo '<div style="display:flex; align-items:center; justify-content:center; gap:4px;">';
                echo '<a href="' . esc_url($menu_link) . '" style="flex:1; display:block; padding:6px 4px; background:#ffffff; color:#1e90ff; text-decoration:none; border-radius:8px; border:2px solid ' . $border_color . '; cursor:pointer; transition:all 0.3s ease; font-weight:bold; font-size:15px; text-align:center; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; min-height:24px; line-height:18px; box-shadow:0 2px 6px rgba(0,0,0,0.08);">';
                echo $display_title;
                echo '</a>';
                echo $edit_button;
                echo '</div>';
                echo '</li>';
            }
            echo '</ul>';
            echo '</section>';
        }
    }
} else {
    // 検索フォーム直下にエラーメッセージを表示
    echo '<div class="search-error-message" style="color:red; margin:18px 0 0 0; text-align:center; font-weight:bold; font-size:16px;">お探しのコンテンツはありません。</div>';
}

get_footer();
?>