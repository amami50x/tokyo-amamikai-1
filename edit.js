/**
 * メニューエディター用JavaScript - テスト版
 */

console.log('🔴 TEST: edit.js 読み込み開始');

jQuery(document).ready(function($){
    console.log('🔴 TEST: jQuery ready完了');
    
    // 読み込みボタン
    $('#load_menu').on('click', function(e){
        e.preventDefault();
        console.log('🔴 TEST: 読み込みボタンクリック！');
        alert('✅ 読み込みボタン動作確認！');
    });
    
    // 更新ボタン
    $('#update_menu').on('click', function(e){
        e.preventDefault();
        console.log('🔴 TEST: 更新ボタンクリック！');
        alert('✅ 更新ボタン動作確認！');
    });
    
    // すべてのボタンを監視
    $('button').on('click', function(){
        console.log('🔴 TEST: ボタンクリック:', $(this).text());
    });
    
    console.log('🔴 TEST: イベント登録完了');
});

console.log('🔴 TEST: edit.js 読み込み完了');