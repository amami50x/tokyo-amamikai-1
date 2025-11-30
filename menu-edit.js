jQuery(document).ready(function($){
    // MENU読み込み
    $('#load_menu').on('click', function(){
        var menu_no = $('#search_menu_no').val();
        $.post(menu_edit_ajax.ajax_url, {
            action:'amamikai_get_menu',
            nonce: menu_edit_ajax.nonce,
            menu_no: menu_no
        }, function(res){
            if(res.success){
                var data = res.data;
                $('#post_id').val(data.ID);
                $('#post_title').val(data.post_title);
                $('#menu_no').val(data.menu_no);
                $('#end_date').val(data.end_date);
                $('#editor_name').val(data.editor_name);
                $('#category').val(data.categories[0] || '');
                if(typeof(tinyMCE)!=='undefined'){
                    tinyMCE.get('post_content').setContent(data.post_content);
                } else {
                    $('#post_content').val(data.post_content);
                }
            } else {
                alert(res.data.message);
            }
        });
    });

    // 更新
    $('#update_menu').on('click', function(){
        var post_content = (typeof(tinyMCE)!=='undefined') ? tinyMCE.get('post_content').getContent() : $('#post_content').val();
        $.post(menu_edit_ajax.ajax_url, {
            action:'amamikai_update_menu',
            nonce: menu_edit_ajax.nonce,
            post_id: $('#post_id').val(),
            post_title: $('#post_title').val(),
            post_content: post_content,
            menu_no: $('#menu_no').val(),
            end_date: $('#end_date').val(),
            editor_name: $('#editor_name').val(),
            category: $('#category').val()
        }, function(res){
            if(res.success){
                alert('更新完了');
            } else {
                alert(res.data.message);
            }
        });
    });
});
