$(function(){

/////////////////////////////
 // トップページのヘッダーカラー変更
 var targetHeight = $('.js-change-header-target').height();
 $(window).on('scroll', function(){
   $('.js-change-header').toggleClass('active', $(this).scrollTop() < targetHeight);
 });

 /////////////////////////////
  // ハンバーガーメニュー
  var trigger = $('.js-show-menu');
  trigger.on('click', function(){
    $('.js-show-menu-target').toggleClass('active');
    $(this).toggleClass('active');
  });

/////////////////////////////
 // 画面遷移後のメッセージ表示
 var $jsShowMsg = $('#js_show_msg');
 var msg = $jsShowMsg.text();

 if (msg.replace(/^[\s ]+|[\s ]+$/g,"").length) {
   $jsShowMsg.slideToggle('slow');
   setTimeout(function(){$jsShowMsg.slideToggle('slow');},5000);
 }

/////////////////////////////
 // 施設詳細ページ画像切り替え
 var $imgMain = $('#js-img-main'),
     $imgSub = $('.js-img-sub');

 $imgSub.on('click', function(e){
   $imgMain.attr('src', $(this).attr('src'));
 });

/////////////////////////////
 // 文字数カウント
 var $textCount1 = $('.js-text-count1'),
     $textview1 = $('.js-text-count-view1');

 $textCount1.on('keyup', function(e){
   $textview1.html($(this).val().length);
 });

 // 文字数カウント
 var $textCount2 = $('.js-text-count2'),
     $textview2 = $('.js-text-count-view2');

 $textCount2.on('keyup', function(e){
   $textview2.html($(this).val().length); //$textViewのhtmlを()内に変更 thisのvalueを取得し、長さを計る
 });

/////////////////////////////
 // お気に入り機能
 $('.js-favorite').on('click',function(){
   var $pushIcon = $(this);
   var $movingIcon = $pushIcon.next('.js-favorite-animation');
   var inst_id = $pushIcon.data('instid');
   $pushIcon.toggleClass('active');
   $movingIcon.toggleClass('active');

   $.ajax({
     type: 'POST',
     url: 'ajaxFavorite.php',
     data: { inst_id : inst_id}
   }).done(function(){
       console.log('成功');
     })
     .fail(function(){
       console.log('失敗');
     });
 });

/////////////////////////////
 // 画像プレビュー機能
 var $dropArea = $('.dropPic_area'),
     $inputFile = $('.dropPic_inputFile');

 // ドラッグオーバーで枠線表示
 $dropArea.on('click', function(e){
   e.stopPropagation();
   $(this).css('border', 'dashed 2px rgba(210, 210, 210, 1)');
 });

 // ドロップされたら枠線非表示
 $dropArea.on('dragleave', function(e){
   e.stopPropagation();
   e.preventDefault();
   $(this).css('border', 'none');
 });

 // インプットタグに変化があったら（セットされたら）読み込み
 $inputFile.on('change', function(e){
   $dropArea.css('border', 'none');

   var file = this.files[0],
       $img = $(this).siblings('.js-img-preview'),
       fileReader = new FileReader();

   // 読み込みが完了したらsrc属性を変更
   fileReader.onload = function(event){
     $img.attr('src', event.target.result).show();
   };

   // 読み込み
   fileReader.readAsDataURL(file);
 });


/////////////////////////////
 // アコーディオンパネル機能
 var $accordSwitch = $('.js-accordion-switch');
 var $accordSwitch2 = $('.js-accordion-switch2');

 $accordSwitch.on('click', function(e){
   var $accordArea = $(this).next('.js-accordion-area');
   $accordArea.slideToggle(500);
   $accordSwitch.toggleClass('close');
 });

 $accordSwitch2.on('click', function(e){
   var $accordArea = $(this).next('.js-accordion-area');
   $accordArea.slideToggle(500);
   $accordSwitch2.toggleClass('close');

 });

});
