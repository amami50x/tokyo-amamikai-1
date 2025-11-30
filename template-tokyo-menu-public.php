<?php
/*
* Template Name: 東京奄美会メニュー（公開用ーx）
*/

get_header();

// 管理者チェック（管理者または編集者権限があるユーザー）
$is_admin = current_user_can('manage_options') || current_user_can('edit_posts');
?>

<div class="tokyo-menu-list" style="max-width:1200px; margin:0 auto; padding:15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height:100vh;">

    <h1 style="background:linear-gradient(45deg, #a78bfa, #8b5cf6); color:white; padding:15px; text-align:center; border-radius:8px; margin-bottom:20px; font-size:24px; font-weight:bold; text-shadow:2px 2px 4px rgba(0,0,0,0.3); box-shadow:0 4px 15px rgba(0,0,0,0.2);">
        東京奄美会 メニュー一覧
    </h1>

<?php
// 検索パラメータ取得
$keyword     = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$author      = isset($_GET['author_name']) ? sanitize_text_field($_GET['author_name']) : '';
$start_year  = isset($_GET['start_year']) ? sanitize_text_field($_GET['start_year']) : '';
$end_year    = isset($_GET['menu_end_year']) ? sanitize_text_field($_GET['menu_end_year']) : '';
$show_menu_no = isset($_GET['show_menu_no']) ? sanitize_text_field($_GET['show_menu_no']) : 'no';
$category    = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';

// WP_Query用の基本引数
$args = array(
    'post_type' => 'post',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'ASC',
    'meta_key' => 'menu_no',
);

// キーワード検索
if (!empty($keyword)) $args['s'] = $keyword;

// 投稿者検索
if (!empty($author)) $args['author_name'] = $author;

// 投稿日検索
if (!empty($start_year)) {
    $args['date_query'][] = array('year' => intval($start_year));
}

// 掲載終了年検索
if (!empty($end_year)) {
    $args['meta_query'][] = array(
        'key' => 'menu_end',
        'value' => $end_year,
        'compare' => 'LIKE',
    );
}

// カテゴリー検索
if (!empty($category)) $args['cat'] = intval($category);

// 投稿取得
$query = new WP_Query($args);
$filtered_posts = array();
if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        $menu_no = get_post_meta($post_id, 'menu_no', true);
        if (!empty($menu_no)) $filtered_posts[] = $post_id;
    }
    wp_reset_postdata();
}

$final_count = count($filtered_posts);
?>

<!-- 検索フォーム -->
<form method="get" action="" style="margin-bottom:15px; background:linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding:12px; border-radius:10px; display:flex; flex-wrap:wrap; align-items:center; gap:6px; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
    <input type="hidden" name="page_id" value="<?php echo get_the_ID(); ?>">

    <input type="text" name="s" placeholder="🔍 キーワード検索" value="<?php echo esc_attr($keyword); ?>" style="padding:8px 10px; width:150px; background:rgba(255,255,255,0.9); border:2px solid #fff; border-radius:6px; font-size:13px; font-weight:500; box-shadow:0 2px 5px rgba(0,0,0,0.1);">

    <select name="author_name" style="padding:8px 10px; background:rgba(255,255,255,0.9); border:2px solid #fff; border-radius:6px; font-size:13px; font-weight:500;">
        <option value="">👤 投稿者を選択</option>
        <?php
        $users = get_users(['who' => 'authors']);
        foreach ($users as $user) {
            $selected = ($author === $user->user_nicename) ? 'selected' : '';
            echo '<option value="' . esc_attr($user->user_nicename) . '" ' . $selected . '>' . esc_html($user->display_name) . '</option>';
        }
        ?>
    </select>

    <select name="category" style="padding:8px 10px; background:rgba(255,255,255,0.9); border:2px solid #fff; border-radius:6px; font-size:13px; font-weight:500;">
        <option value="">📂 カテゴリを選択</option>
        <?php
        $categories = get_categories(['hide_empty' => true]);
        foreach ($categories as $cat) {
            $selected = ($category == $cat->term_id) ? 'selected' : '';
            echo '<option value="' . esc_attr($cat->term_id) . '" ' . $selected . '>' . esc_html($cat->name) . '</option>';
        }
        ?>
    </select>

    <select name="start_year" style="padding:8px 10px; background:rgba(255,255,255,0.9); border:2px solid #fff; border-radius:6px; font-size:13px; font-weight:500;">
        <option value="">📅 投稿日（年）</option>
        <?php
        $years = range(date('Y'), date('Y') - 10);
        foreach ($years as $y) {
            $selected = ($start_year == $y) ? 'selected' : '';
            echo "<option value=\"$y\" $selected>$y</option>";
        }
        ?>
    </select>

    <select name="menu_end_year" style="padding:8px 10px; background:rgba(255,255,255,0.9); border:2px solid #fff; border-radius:6px; font-size:13px; font-weight:500;">
        <option value="">⏰ 掲載終了年</option>
        <?php
        foreach ($years as $y) {
            $selected = ($end_year == $y) ? 'selected' : '';
            echo "<option value=\"$y\" $selected>$y</option>";
        }
        ?>
    </select>

    <select name="show_menu_no" style="padding:8px 10px; background:rgba(255,255,255,0.9); border:2px solid #fff; border-radius:6px; font-size:13px; font-weight:500;">
        <option value="no" <?php echo $show_menu_no === 'no' ? 'selected' : ''; ?>>🔢 MENU番号: 非表示</option>
        <option value="yes" <?php echo $show_menu_no === 'yes' ? 'selected' : ''; ?>>🔢 MENU番号: 表示</option>
    </select>

    <button type="submit" style="padding:8px 16px; background:linear-gradient(45deg, #4facfe, #00f2fe); color:white; border:none; border-radius:6px; font-size:13px; font-weight:bold; cursor:pointer;">検索</button>

    <span style="margin-left:8px; font-weight:bold; color:white; font-size:14px;">
        <?php echo $final_count; ?> 件取得
    </span>

    <?php if (!empty($keyword) || !empty($author) || !empty($category) || !empty($start_year) || !empty($end_year) || $show_menu_no === 'yes'): ?>
    <a href="<?php echo esc_url(get_permalink()); ?>" style="padding:8px 12px; background:rgba(255,255,255,0.2); color:white; text-decoration:none; border-radius:6px; margin-left:8px; font-weight:bold;">検索解除</a>
    <?php endif; ?>
</form>

<?php
if (!empty($filtered_posts)) {
    // カテゴリーごとに投稿をグループ化
    $categories_with_posts = [];
    foreach ($filtered_posts as $post_id) {
        $post_categories = get_the_category($post_id);
        if (empty($post_categories)) {
            $categories_with_posts['uncategorized']['category'] = (object)['name' => '未分類'];
            $categories_with_posts['uncategorized']['posts'][] = $post_id;
        } else {
            foreach ($post_categories as $cat) {
                $categories_with_posts[$cat->term_id]['category'] = $cat;
                $categories_with_posts[$cat->term_id]['posts'][] = $post_id;
            }
        }
    }

    // 投稿表示
    foreach ($categories_with_posts as $cat_data) {
        $category = $cat_data['category'];
        $post_ids = $cat_data['posts'];

        echo '<section style="margin-bottom:20px;">';
        echo '<h2 style="text-align:center; background:linear-gradient(45deg,#4facfe,#00f2fe); color:white; padding:10px 20px; border-radius:20px; display:inline-block;">' . esc_html($category->name) . '</h2>';

        // MENU番号でソート
        $posts_with_menu_no = [];
        foreach ($post_ids as $post_id) {
            $menu_no = get_post_meta($post_id, 'menu_no', true);
            $posts_with_menu_no[] = ['post_id'=>$post_id,'menu_no'=>$menu_no];
        }
        usort($posts_with_menu_no, function($a,$b){
            $a_parts = explode('-',$a['menu_no']);
            $b_parts = explode('-',$b['menu_no']);
            for($i=0;$i<min(count($a_parts),count($b_parts));$i++){
                $diff = intval($a_parts[$i]) - intval($b_parts[$i]);
                if($diff!==0) return $diff;
            }
            return 0;
        });

        echo '<ul style="display:flex; flex-wrap:wrap; gap:0.6%; justify-content:center; padding:0; margin:0;">';
        foreach($posts_with_menu_no as $post_data){
            $post_id = $post_data['post_id'];
            $menu_no = $post_data['menu_no'];
            $title = get_the_title($post_id);
            $content = get_post_field('post_content',$post_id);
            $return_to = urlencode((is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            $menu_link = home_url("/menu-detail-public/?menu_no=".$menu_no."&return_to=".$return_to);
            $border_color = empty(trim(strip_tags($content))) ? '#ff69b4' : '#87cefa';
            $display_title = ($show_menu_no==='yes' && $menu_no) ? '<span style="font-size:11px; color:#666; margin-right:4px;">'.$menu_no.'</span>'.$title : $title;

            echo '<li style="list-style:none; margin:4px 0; width:24%; text-align:center;">';
            echo '<a href="'.esc_url($menu_link).'" style="display:block; padding:6px 4px; background:#fff; color:#1e90ff; border-radius:8px; border:2px solid '.$border_color.'; text-decoration:none; font-weight:bold;">'.$display_title.'</a>';

            // 管理者だけに表示する編集ボタン（元のシンプルな形）
            if($is_admin){
                $edit_link = get_edit_post_link($post_id);
                echo '<br>';
                echo '<a href="'.esc_url($edit_link).'" style="display:inline-block; margin-top:4px; padding:4px 8px; background-color:#4facfe; color:#ffffff; font-weight:bold; border-radius:4px; text-decoration:none; font-size:12px;">編集</a>';
            }

            echo '</li>';
        }
        echo '</ul>';
        echo '</section>';
    }

} else {
    echo '<div style="text-align:center; padding:40px; background:rgba(255,255,255,0.9); border-radius:12px; margin:15px 0;">';
    echo '<p style="color:#e74c3c; font-weight:bold; font-size:20px;">投稿がありません</p>';
    if($keyword) echo '<p>キーワード「'.esc_html($keyword).'」に一致する投稿が見つかりませんでした。</p>';
    elseif($author || $category || $start_year || $end_year) echo '<p>検索条件に一致する投稿が見つかりませんでした。</p>';
    else echo '<p>表示する投稿がありません。</p>';
    echo '</div>';
}
?>

</div>
<?php get_footer(); ?>
