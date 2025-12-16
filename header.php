<?php
$categories = get_categories(['hide_empty' => true]);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?> 
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <div style="padding: 10px 0 0 15px;">
        <button onclick="history.back()" 
                style="
                    padding: 8px 20px;
                    background-color: #ffffff; 
                    color: #0073aa; 
                    border: 2px solid #0073aa;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 0.9em;
                    font-weight: bold;
                    text-decoration: none;
                    transition: background-color 0.3s;
                    display: inline-block; 
                    line-height: 1;
                    margin-bottom: 10px;
                "
                onmouseover="this.style.backgroundColor='#e6f7ff'"
                onmouseout="this.style.backgroundColor='#ffffff'">
            ← 前のページに戻る
        </button>
    </div>
    <?php
    // $categories は先頭で get_categories(['hide_empty' => true]) で取得済み
    // 表形式で表示（2列テーブル）
    // カテゴリ名と説明文を表形式で横並びに表示
    // 不要なカテゴリ名・説明文・カテゴリループ部分を完全削除（構文エラー修正）