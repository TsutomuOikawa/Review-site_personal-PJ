<!--　共通フッター　-->

 <footer id="footer">
   <small class="copyright">Copyright © <a href="index.php">Concent-rate</a> All Rights Reserved</small>
 </footer>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
 <script type="text/javascript">
$(function(){
/////////////////////////////
  // フッターのCSSを変更
  // var $footer = $('#fooer');
  // if ( $footer.attr('class') ) {
  //   $footer.attr('style', );
  // }


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
  var $imgMain1 = $('#js-img-main1'),
      $imgMain2 = $('#js-img-main2'),
      $imgSub = $('.js-img-sub');
  $imgSub.on('click', function(e){
    $imgMain1.attr('src', $imgMain2.attr('src'));
    $imgMain2.attr('src', $(this).attr('src'));
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
    var $icon = $('.js-favorite');
    $icon.on('click',function(){
      var inst_id = $(this).data('instid');
      $(this).toggleClass('active');
      $(this).toggleClass('nonactive')

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



});
</script>

   </body>
 </html>
