
<?php
$categories = get_categories(['hide_empty' => true]);
?>
<!DOCTYPE html>
<!-- テスト用コメント -->
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?> 
    </head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>





    <!-- カテゴリ選択フォーム（検索欄のカテゴリ部分のみ） -->
    <?php
    // $categories は先頭で get_categories(['hide_empty' => true]) で取得済み
    // 表形式で表示（2列テーブル）
    // カテゴリ名と説明文を表形式で横並びに表示
    // 不要なカテゴリ名・説明文・カテゴリループ部分を完全削除（構文エラー修正）


