<!--　共通フッター　-->

 <footer>
   <small class="copyright">Copyright © Concent-rate All Rights Reserved</small>
 </footer>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
 <script type="text/javascript">
$(function(){
  //メッセージ表示
  var $jsShowMsg = $('#js_show_msg');
  var msg = $jsShowMsg.text();
  if (msg.replace(/^[\s ]+|[\s ]+$/g,"").length) {
    $jsShowMsg.slideToggle('slow');
    setTimeout(function(){$jsShowMsg.slideToggle('slow');},5000);
  }
});
</script>

   </body>
 </html>
