jQuery(document).ready(function($) {
    
    // 1. メニュー全体の開閉機能
    $('#menuToggle').on('click', function() {
        // メニューラッパーを滑らかに表示/非表示
        $('#corporateMenu').slideToggle(300); 
        
        // ボタンのテキストを「MENU」と「閉じる」で切り替える
        var currentText = $(this).text();
        if (currentText.trim() === 'MENU') {
            $(this).text('閉じる');
        } else {
            $(this).text('MENU');
        }
    });

    // 2. 階層の展開/折りたたみ機能
    // xxレベルとyyレベルのタイトル要素を取得
    var $clickableTitles = $('.menu-level-xx > li > .menu-title-xx, .menu-level-yy > li > .menu-title-yy');

    $clickableTitles.each(function() {
        var $title = $(this);
        var $childList = $title.next('ul'); // 直後の子ul要素

        // 子リストが存在する場合のみ処理
        if ($childList.length) {
            $title.addClass('clickable-title').append('<span class="toggle-icon">+</span>');
            $childList.hide(); // 初期状態では子リストを閉じる
            
            // クリックイベントの設定
            $title.on('click', function(e) {
                e.stopPropagation(); // 誤動作防止
                
                // 子リストの開閉
                $childList.slideToggle(200);
                
                // アイコンの切り替え
                var $icon = $title.find('.toggle-icon');
                if ($childList.is(':visible')) {
                    $icon.text('−'); // 開いた状態
                } else {
                    $icon.text('+'); // 閉じた状態
                }
            });
        }
    });
});