<!--　共通フッター　-->

 <footer>
   <small class="copyright">Copyright © <a href="index.php">Concent-rate</a> All Rights Reserved</small>
 </footer>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
 <script type="text/javascript">
$(function(){
/////////////////////////////
  //画面遷移後のメッセージ表示
  var $jsShowMsg = $('#js_show_msg');
  var msg = $jsShowMsg.text();
  if (msg.replace(/^[\s ]+|[\s ]+$/g,"").length) {
    $jsShowMsg.slideToggle('slow');
    setTimeout(function(){$jsShowMsg.slideToggle('slow');},5000);
  }

/////////////////////////////
  //施設詳細ページ画像切り替え
  var $imgMain1 = $('#js-img-main1'),
      $imgMain2 = $('#js-img-main2'),
      $imgSub = $('.js-img-sub');
  $imgSub.on('click', function(e){
    $imgMain1.attr('src', $imgMain2.attr('src'));
    $imgMain2.attr('src', $(this).attr('src'));
  });


});
</script>

   </body>
 </html>
