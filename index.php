

<?php
/**
 * テーマのメインとなるテンプレートファイル
 * MENU番号検索システム対応版
 */



// MENU番号検索システム
$forced_post_id = null;
$debug_info = [];

if (isset($_GET['menu_number']) && !empty($_GET['menu_number'])) {
    $menu_number = trim($_GET['menu_number']);
    $debug_info[] = "検索するMENU番号: {$menu_number}";
    
    // 複数のメタキー名で検索
    $meta_keys = ['menu_no', 'MENU番号', 'メニュー番号', '_menu_no'];
    
    foreach ($meta_keys as $key) {
        $posts = get_posts(array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'numberposts' => 1,
            'meta_query' => array(
                array(
                    'key' => $key,
                    'value' => $menu_number,
                    'compare' => '='
                )
            )
        ));
        
        if (!empty($posts)) {
            $forced_post_id = $posts[0]->ID;
            $debug_info[] = "メタキー '{$key}' で投稿ID {$forced_post_id} を発見";
            break;
        } else {
            $debug_info[] = "メタキー '{$key}' では見つからず";
        }
    }
    
    if (!$forced_post_id) {
        $debug_info[] = "❌ MENU番号 {$menu_number} に一致する投稿が見つかりません";
    }
}



get_header(); 

// 強制的に特定の投稿を表示
if ($forced_post_id) {
    $post = get_post($forced_post_id);
    if ($post) {
        setup_postdata($post);

        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="background: white; padding: 30px; margin: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <header class="entry-header">
                <?php the_title( '<h1 class="entry-title" style="color: #333; margin-bottom: 20px;">', '</h1>' ); ?>
                
                <!-- 投稿の基本情報を表示 -->
                <div style="background: #f8f9fa; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #0066cc;">
                    <p><strong>投稿ID:</strong> <?php the_ID(); ?></p>
                    <p><strong>投稿日:</strong> <?php echo get_the_date('Y年m月d日'); ?></p>
                    <p><strong>更新日:</strong> <?php echo get_the_modified_date('Y年m月d日'); ?></p>
                    <p><strong>カテゴリ:</strong> 
                        <?php 
                        $categories = get_the_category();
                        if ($categories) {
                            foreach ($categories as $category) {
                                echo esc_html($category->name) . ' ';
                            }
                        } else {
                            echo 'なし';
                        }
                        ?>
                    </p>
                </div>
                
                <!-- MENU番号などのカスタムフィールドを表示 -->
                <?php
                $meta_keys = ['menu_no', 'MENU番号', 'メニュー番号', '_menu_no'];
                echo '<div style="background: #e8f5e8; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #28a745;">';
                echo '<h4 style="margin-top: 0; color: #28a745;">📋 カスタムフィールド情報</h4>';
                
                $found_fields = false;
                foreach ($meta_keys as $key) {
                    $value = get_post_meta(get_the_ID(), $key, true);
                    if (!empty($value)) {
                        echo '<p><strong>' . esc_html($key) . ':</strong> ' . esc_html($value) . '</p>';
                        $found_fields = true;
                    }
                }
                
                if (!$found_fields) {
                    echo '<p>カスタムフィールドが設定されていません。</p>';
                }
                echo '</div>';
                ?>
            </header>
            
            <div class="entry-content" style="line-height: 1.8; color: #555;">
                <h3 style="color: #0066cc; border-bottom: 2px solid #0066cc; padding-bottom: 5px;">📄 投稿内容</h3>
                <?php 
                the_content(); 
                
                // 投稿内容が空の場合の表示
                $content = get_the_content();
                if (empty(trim(strip_tags($content)))) {
                    echo '<p style="color: #999; font-style: italic;">この投稿には本文がありません。</p>';
                }
                ?>
            </div>
            
            <!-- 投稿の抜粋があれば表示 -->
            <?php if (has_excerpt()) : ?>
            <div style="background: #fff3cd; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #ffc107;">
                <h4 style="margin-top: 0; color: #856404;">📝 投稿の抜粋</h4>
                <p><?php the_excerpt(); ?></p>
            </div>
            <?php endif; ?>
        </article>
        <?php
        wp_reset_postdata();
    }
} else {
    // 通常のWordPressループ
    if ( have_posts() ) : 
        while ( have_posts() ) : the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
            </header>
            <div class="entry-content">
                <?php the_content(); ?>
            </div>
        </article>
        <?php 
        endwhile; 
    else :
        echo '<p>お探しのコンテンツはありません。</p>';
    endif;
} 

// footer.php を呼び出し、ページの下半分を出力
get_footer(); 
?>

