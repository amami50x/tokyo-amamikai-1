
jQuery(function($){
    $(document).on('click', '.mls-menu-item', function(e){
        var menuNo = $(this).data('menu-no');
        // ただのリンク動作は残したい場合はコメントアウトを調整（下行をコメントアウトするとページ遷移しない）
        e.preventDefault();

        var $result = $('#mls-result');
        $result.html('<p>読み込み中...</p>');

        $.post( mls_ajax.ajax_url, {
            action: 'mls_fetch_posts',
            nonce: mls_ajax.nonce,
            menu_no: menuNo
        }, function(resp){
            if ( resp.success ) {
                $result.html( resp.data.html );
                // 履歴を pushState しておく（任意）
                if ( history && history.pushState ) {
                    var newUrl = new URL(window.location.href);
                    newUrl.searchParams.set('menu_no', menuNo);
                    history.pushState({menu_no:menuNo}, '', newUrl.toString());
                }
            } else {
                $result.html('<p>エラーが発生しました。</p>');
                console.log(resp);
            }
        });
    });

    // popstate で戻る対応（任意）
    window.addEventListener('popstate', function(e){
        var menu_no = (e.state && e.state.menu_no) ? e.state.menu_no : null;
        if ( menu_no ) {
            $('.mls-menu-item[data-menu-no="'+menu_no+'"]').trigger('click');
        } else {
            $('#mls-result').html('<p>表示する MENU を選んでください。</p>');
        }
    });
});