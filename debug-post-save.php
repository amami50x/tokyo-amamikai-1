<?php
/*
=================================================================
WordPress投稿更新デバッグ用コード
投稿更新が正常に動作しない問題を特定するための一時的なデバッグコード
=================================================================
*/

// 投稿保存のデバッグログ機能
function amamikai_debug_post_save($post_id, $post, $update) {
    // デバッグログを有効化
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log("Post Save Debug - ID: $post_id, Title: " . $post->post_title . ", Update: " . ($update ? 'true' : 'false'));
        error_log("Post Content Length: " . strlen($post->post_content));
        error_log("Post Status: " . $post->post_status);
    }
}
add_action('wp_insert_post', 'amamikai_debug_post_save', 10, 3);

// 投稿更新フィルターのデバッグ
function amamikai_debug_post_filters($data, $postarr) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log("Post Data Filter - Original Content Length: " . strlen($postarr['post_content']));
        error_log("Post Data Filter - Filtered Content Length: " . strlen($data['post_content']));
    }
    return $data;
}
add_filter('wp_insert_post_data', 'amamikai_debug_post_filters', 10, 2);

// content_save_preフィルターのデバッグ
function amamikai_debug_content_save($content) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log("Content Save Pre - Content Length: " . strlen($content));
    }
    return $content;
}
add_filter('content_save_pre', 'amamikai_debug_content_save', 10);

?>