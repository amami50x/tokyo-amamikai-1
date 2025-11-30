<?php
/**
 * デバッグ用 - 現在のメニュー構成確認
 */

// WordPressが読み込まれているか確認
if (!defined('ABSPATH')) {
    echo "WordPress環境が読み込まれていません";
    exit;
}

echo "<h2>現在のメニュー構成確認</h2>";

// 管理メニューの確認
global $menu, $submenu;

echo "<h3>メインメニュー</h3>";
if (isset($menu)) {
    foreach ($menu as $item) {
        if (isset($item[0]) && $item[0]) {
            echo "- " . strip_tags($item[0]) . " (スラッグ: " . $item[2] . ")<br>";
        }
    }
}

echo "<h3>サブメニュー</h3>";
if (isset($submenu)) {
    foreach ($submenu as $parent => $items) {
        echo "<strong>親: $parent</strong><br>";
        foreach ($items as $item) {
            if (isset($item[0]) && $item[0]) {
                echo "&nbsp;&nbsp;- " . strip_tags($item[0]) . " (スラッグ: " . $item[2] . ")<br>";
            }
        }
        echo "<br>";
    }
}

// メニュー関数の存在確認
echo "<h3>関数の存在確認</h3>";
echo "amamikai_menu_editor_page: " . (function_exists('amamikai_menu_editor_page') ? '✅ 存在' : '❌ 不存在') . "<br>";
echo "amamikai_add_admin_menu: " . (function_exists('amamikai_add_admin_menu') ? '✅ 存在' : '❌ 不存在') . "<br>";

// 直接アクセステスト
echo "<h3>直接アクセステスト</h3>";
echo '<a href="' . admin_url('admin.php?page=amamikai-menu-editor') . '">MENU編集ページに直接アクセス</a><br>';

?>