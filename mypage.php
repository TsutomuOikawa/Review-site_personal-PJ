<?php
require('function.php');

//ログイン認証
require('auth.php');

 ?>

<?php
$css_title = basename(__FILE__,".php");
$p_title = 'マイページ';
//共通headタグ呼び出し
require('head.php');

//共通ヘッダー呼び出し
require('header.php');
?>

<!--　メインコンテンツ　-->
<div class="wrap">
  <main>
    <div class="h1-wide">
      <h1>マイページトップ</h1>
    </div>
    <div class="mypage-inner">
      <section>
        <div class="h2_space">
          <h2>お気に入りの施設</h2>
        </div>
        <ul class="align-center">
          <li class="inline"><a href="#"><img src="http://dummyimage.com/180x180/acc/fff.gif&text=画像" alt=""></a></li>
          <li class="inline"><a href="#"><img src="http://dummyimage.com/180x180/acc/fff.gif&text=画像" alt=""></a></li>
          <li class="inline"><a href="#"><img src="http://dummyimage.com/180x180/acc/fff.gif&text=画像" alt=""></a></li>
          <li class="inline"><a href="#"><img src="http://dummyimage.com/180x180/acc/fff.gif&text=画像" alt=""></a></li>
          <li class="inline"><a href="#"><img src="http://dummyimage.com/180x180/acc/fff.gif&text=画像" alt=""></a></li>
          <li class="inline"><a href="#"><img src="http://dummyimage.com/180x180/acc/fff.gif&text=画像" alt=""></a></li>
        </ul>
      </section>
      <section>
        <div class="h2_space">
          <h2>投稿済みのクチコミ</h2>
        </div>
        <ul class="align-center">
          <li class="inline"><a href="#"><img src="http://dummyimage.com/180x180/acc/fff.gif&text=画像" alt=""></a></li>
          <li class="inline"><a href="#"><img src="http://dummyimage.com/180x180/acc/fff.gif&text=画像" alt=""></a></li>
          <li class="inline"><a href="#"><img src="http://dummyimage.com/180x180/acc/fff.gif&text=画像" alt=""></a></li>
          <li class="inline"><a href="#"><img src="http://dummyimage.com/180x180/acc/fff.gif&text=画像" alt=""></a></li>
          <li class="inline"><a href="#"><img src="http://dummyimage.com/180x180/acc/fff.gif&text=画像" alt=""></a></li>
          <li class="inline"><a href="#"><img src="http://dummyimage.com/180x180/acc/fff.gif&text=画像" alt=""></a></li>
        </ul>
      </section>
      <section>
        <div class="h2_space">
          <h2>あなたが追加した施設</h2>
        </div>
        <ul class="align-center">
          <li class="inline"><a href="#"><img src="http://dummyimage.com/180x180/acc/fff.gif&text=画像" alt=""></a></li>
          <li class="inline"><a href="#"><img src="http://dummyimage.com/180x180/acc/fff.gif&text=画像" alt=""></a></li>
          <li class="inline"><a href="#"><img src="http://dummyimage.com/180x180/acc/fff.gif&text=画像" alt=""></a></li>
          <li class="inline"><a href="#"><img src="http://dummyimage.com/180x180/acc/fff.gif&text=画像" alt=""></a></li>
          <li class="inline"><a href="#"><img src="http://dummyimage.com/180x180/acc/fff.gif&text=画像" alt=""></a></li>
          <li class="inline"><a href="#"><img src="http://dummyimage.com/180x180/acc/fff.gif&text=画像" alt=""></a></li>
        </ul>
      </section>

    </div>
  </main>

  <aside id="right-sidebar">
    <div class="sidebar-inner align-center">
      <div class="sidebar-title"><p>マイページメニュー</p></div>
      <ul>
        <li><a href="mypage.php">マイページトップ</a></li>
        <li><a href="profileEdit.php">プロフィール編集</a></li>
        <li><a href="passChange.php">パスワード変更</a></li>
        <li><a href="withdrawal.php">退会</a></li>
      </ul>
    </div>
  </aside>
</div>

<!--　共通フッター呼び出し　-->
<?php
require('footer.php');
 ?>
