
<?php
/*
* Template Name: 東京奄美会メニュー（公開用）
*/

// FontAwesome CDNをheadに追加
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
    color:#fff; padding:18px 0; text-align:center; border-radius:10px;
    margin-bottom:28px; font-size:2.1em; font-weight:700;
    text-shadow:2px 2px 6px rgba(0,0,0,0.18);
    box-shadow:0 4px 15px rgba(0,0,0,0.13);
    letter-spacing:0.04em;
}
.tokyo-menu-list form {
    margin-bottom:18px; background:linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    padding:14px 10px; border-radius:12px; display:flex; flex-wrap:wrap; align-items:center; gap:8px;
    box-shadow:0 4px 12px rgba(0,0,0,0.13);
}
/* ...（以降のCSSはそのまま）... */
/* 一覧画面タイトル用デザイン改善 */
.menu-header-row {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 28px;
}
.menu-header-row h1 {
    margin: 0;
    padding: 18px 0;
    background: linear-gradient(45deg, #a78bfa, #8b5cf6);
    color: #fff;
    border-radius: 12px;
    font-size: 2.2em;
    font-weight: 900;
    letter-spacing: 0.04em;
    text-shadow: 2px 2px 8px rgba(0,0,0,0.18);
    box-shadow: 0 4px 18px rgba(0,0,0,0.15);
    text-align: center;
    min-width: 0;
    border: 3px solid #fff;
    width: 100%;
    max-width: 700px;
}
body, .tokyo-menu-list {
    font-family: "Yu Gothic", "Hiragino Kaku Gothic ProN", Meiryo, sans-serif;
    color: #222;
    font-weight: 600;
}
.tokyo-menu-list, .menu-category, .tokyo-menu-items, .tokyo-menu-list form, .tokyo-menu-list h2, .tokyo-menu-list label, .tokyo-menu-list input, .tokyo-menu-list select, .tokyo-menu-list button {
    font-weight: 600;
    color: #222;
}
.tokyo-menu-list a, .tokyo-menu-items a {
    font-weight: 700;
    color: #1e90ff;
}
</style>

<style>
/* A4エリア内の段落やブロックに背景色と角丸を付与 */
.tokyo-menu-a4-content p,
.tokyo-menu-a4-content ul,
.tokyo-menu-a4-content ol,
.tokyo-menu-a4-content pre,
.tokyo-menu-a4-content blockquote,
.tokyo-menu-a4-content table {
    background: #e3f0ff;
    border-radius: 8px;
    padding: 6px 12px;
    margin-bottom: 10px;
    box-sizing: border-box;
}
.tokyo-menu-a4-content p {
    margin-top: 0;
    margin-bottom: 10px;
    position: relative;
}
.tokyo-menu-a4-content p.pen-icon::before {
    content: "\f304 ";
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    color: #2196f3;
    margin-right: 8px;
    font-size: 1em;
    position: relative;
    top: 0.5px;
}
.tokyo-menu-a4-content p.calendar-icon::before {
    content: "\f133 ";
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    color: #2196f3;
    margin-right: 8px;
    font-size: 1em;
    position: relative;
    top: 0.5px;
}
/* A4厳密固定・中央寄せ・余白・背景・角丸・縦方向無制限 */
.tokyo-menu-a4-content {
        width: 210mm;
        min-width: 210mm;
        max-width: 210mm;
        min-height: 0;
        height: auto !important;
        max-height: none !important;
        display: block;
        margin-left: auto;
        margin-right: auto;
        background: #f8fafc;
        border: 1px solid #b6d6f6;
        border-radius: 8px;
        box-sizing: border-box;
        padding: 32px 32px 56px 32px;
        font-size: 16px;
        line-height: 1.5;
        font-weight: 600;
        color: #222;
        font-family: Yu Gothic, Hiragino Kaku Gothic ProN, Meiryo, sans-serif;
        overflow: visible;
        box-shadow: 0 4px 18px rgba(0,0,0,0.07);
}
@media print {
    .tokyo-menu-a4-content {
        width: 210mm !important;
        min-width: 210mm !important;
        max-width: 210mm !important;
        min-height: 297mm !important;
        max-height: none !important;
        height: auto !important;
        margin: 0 auto !important;
        page-break-after: always;
        background: #fff !important;
        box-shadow: none !important;
    }
}
</style>

<?php
// ここからPHPロジック
// menu_noパラメータがあれば詳細画面として表示
if (isset($_GET['menu_no']) && $_GET['menu_no'] !== '') {
    $menu_no = sanitize_text_field($_GET['menu_no']);
    $args = [
        'post_type' => 'post',
        'meta_key' => 'menu_no',
        'meta_value' => $menu_no,
        'post_status' => 'publish',
        'posts_per_page' => 1
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
        $list_url = get_permalink(get_queried_object_id());
        echo '<div style="max-width:900px; margin:40px auto; background:#fff; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,0.12); padding:0;">';
        echo '<div style="padding:24px 32px 0 32px;">';
        echo '<div style="display:flex; justify-content:flex-end; margin-bottom:10px;">';
        echo '<a href="' . esc_url($list_url) . '" style="padding:8px 22px; background:#2196f3; color:#fff; border-radius:8px; font-weight:bold; text-decoration:none; font-size:1em; box-shadow:0 2px 8px rgba(33,150,243,0.15);">一覧に戻る</a>';
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
echo '<h1>東京奄美会メニュー一覧</h1>';
echo '</div>';

// 検索パラメータを取得
$keyword     = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$author      = isset($_GET['author_name']) ? sanitize_text_field($_GET['author_name']) : '';
$start_year  = isset($_GET['start_year']) ? sanitize_text_field($_GET['start_year']) : '';
$end_year    = isset($_GET['menu_end_year']) ? sanitize_text_field($_GET['menu_end_year']) : '';
$show_menu_no = isset($_GET['show_menu_no']) ? sanitize_text_field($_GET['show_menu_no']) : 'no';
$category    = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';

$filtered_posts = array();

if (!empty($keyword)) {
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        's' => $keyword,
        'orderby' => 'date',
        'order' => 'ASC',
    );
    if (!empty($author)) {
        $args['author_name'] = $author;
    }
    if (!empty($start_year)) {
        $args['date_query'] = array(array('year' => intval($start_year)));
    }
    if (!empty($category)) {
        $args['cat'] = intval($category);
    }
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $menu_no = get_post_meta($post_id, 'menu_no', true);
            if (!empty($menu_no)) {
                if (!empty($end_year)) {
                    $menu_end = get_post_meta($post_id, 'menu_end', true);
                    if ($menu_end && strpos($menu_end, $end_year) !== false) {
                        $filtered_posts[] = $post_id;
                    }
                } else {
                    $filtered_posts[] = $post_id;
                }
            }
        }
        wp_reset_postdata();
    }
} else {
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'ASC',
        'meta_key' => 'menu_no',
    );
    if (!empty($author)) {
        $args['author_name'] = $author;
    }
    if (!empty($start_year)) {
        $args['date_query'] = array(array('year' => intval($start_year)));
    }
    if (!empty($end_year)) {
        $args['meta_query'] = array(array('key' => 'menu_end', 'value' => $end_year, 'compare' => 'LIKE'));
    }
    if (!empty($category)) {
        $args['cat'] = intval($category);
    }
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $filtered_posts[] = get_the_ID();
        }
        wp_reset_postdata();
    }
}
$final_count = count($filtered_posts);

    // 検索フォーム
    echo '<form method="get" action="" style="margin-bottom:15px; background:linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding:12px; border-radius:10px; display:flex; flex-wrap:wrap; align-items:center; gap:6px; box-shadow:0 4px 12px rgba(0,0,0,0.15);">';
    echo '<input type="hidden" name="page_id" value="' . get_the_ID() . '">';
    echo '<input type="text" name="s" placeholder="🔍 キーワード検索" value="' . esc_attr($keyword) . '" style="padding:8px 10px; width:150px; background:rgba(255,255,255,0.9); border:2px solid #fff; border-radius:6px; font-size:13px; font-weight:500; box-shadow:0 2px 5px rgba(0,0,0,0.1);">';
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
        $selected = ($category == $cat->term_id) ? 'selected' : '';
        echo '<option value="' . esc_attr($cat->term_id) . '" ' . $selected . '>' . esc_html($cat->name) . '</option>';
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
    echo '<span style="margin-left:8px; font-weight:bold; color:white; text-shadow:1px 1px 2px rgba(0,0,0,0.5); font-size:14px;">' . $final_count . ' 件取得</span>';
    if (!empty($keyword) || !empty($author) || !empty($category) || !empty($start_year) || !empty($end_year) || $show_menu_no === 'yes') {
        echo '<a href="' . esc_url(get_permalink()) . '" style="padding:8px 12px; background:rgba(255,255,255,0.2); color:white; text-decoration:none; border-radius:6px; margin-left:8px; font-weight:bold; border:1px solid rgba(255,255,255,0.3); transition:all 0.3s ease;">検索解除</a>';
    }
    echo '</form>';

    if (!empty($filtered_posts)) {
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
                $menu_link = add_query_arg('menu_no', $menu_no, get_permalink());
                $is_content_empty = empty(trim(strip_tags($post_content)));
                $border_color = $is_content_empty ? '#ff69b4' : '#87cefa';
                if ($show_menu_no === 'yes' && !empty($menu_no)) {
                    $display_title = '<span style="font-size:11px; color:#666; margin-right:4px;">' . esc_html($menu_no) . '</span>' . esc_html($post_title);
                } else {
                    $display_title = esc_html($post_title);
                }
                $edit_button = '';
                if (current_user_can('edit_post', $post_id)) {
                    $edit_link = get_edit_post_link($post_id);
                    $edit_button = '<a href="' . esc_url($edit_link) . '" style="margin-left:6px; 
                    padding:2px 10px; background:#ff9800; color:#fff; border-radius:6px; font-size:12px; 
                    font-weight:bold; text-decoration:none; vertical-align:middle; display:inline-block;"
                    >編集</a>';
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
    } else {
        echo '<div style="text-align:center; padding:40px; background:rgba(255,255,255,0.9); border-radius:12px; margin:15px 0; box-shadow:0 5px 15px rgba(0,0,0,0.1);">';
        echo '<p style="color:#e74c3c; font-weight:bold; font-size:20px; margin:0 0 12px 0;">投稿がありません</p>';
        if (!empty($keyword)) {
            echo '<p style="color:#7f8c8d; margin-top:8px; font-size:14px;">キーワード「' . esc_html($keyword) . '」に一致する投稿が見つかりませんでした。</p>';
        } elseif (!empty($author) || !empty($category) || !empty($start_year) || !empty($end_year)) {
            echo '<p style="color:#7f8c8d; margin-top:8px; font-size:14px;">検索条件に一致する投稿が見つかりませんでした。</p>';
        } else {
            echo '<p style="color:#7f8c8d; margin-top:8px; font-size:14px;">表示する投稿がありません。</p>';
        }
        echo '</div>';
    }
    echo '</div>';
    get_footer();
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
            $menu_link = add_query_arg('menu_no', $menu_no, get_permalink());
            $is_content_empty = empty(trim(strip_tags($post_content)));
            $border_color = $is_content_empty ? '#ff69b4' : '#87cefa';
            if ($show_menu_no === 'yes' && !empty($menu_no)) {
                $display_title = '<span style="font-size:11px; color:#666; margin-right:4px;">' . esc_html($menu_no) . '</span>' . esc_html($post_title);
            } else {
                $display_title = esc_html($post_title);
            }
            echo '<li style="list-style:none; margin:4px 0; width:24%; text-align:center;">';
            echo '<a href="' . esc_url($menu_link) . '" style="display:block; padding:6px 4px; background:#ffffff; color:#1e90ff; text-decoration:none; border-radius:8px; border:2px solid ' . $border_color . '; cursor:pointer; transition:all 0.3s ease; font-weight:bold; font-size:15px; text-align:center; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; min-height:24px; line-height:18px; box-shadow:0 2px 6px rgba(0,0,0,0.08);">';
            echo $display_title;
            echo '</a>';
            echo '</li>';
        }
        echo '</ul>';
        echo '</section>';
    }
// ...existing code...
echo '</div>';
get_footer();
