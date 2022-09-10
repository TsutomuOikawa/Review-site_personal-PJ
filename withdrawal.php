<?php

require('function.php');

//ログイン認証
require('auth.php');

//ここから退会処理



//POST送信されたら


//ユーザーIDを元にdeleteflgを変更

 ?>

 <?php
 $css_title = basename(__FILE__,".php");
 //
 $p_title = '退会';
 //共通headタグ呼び出し
 require('head.php');

 //共通ヘッダー呼び出し
 require('header.php');
 ?>

<!--　メインコンテンツ　-->
<div class="wrap">
  <main>
    <div class="h1-wide">
      <h1>マイページ</h1>
      <?php// echo $_SESSION['login_date'] ?>
    </div>
    <div class="mypage-inner">
      <section>
        <div class="h2_space">
          <h2>退会</h2>
        </div>
        <form class="wide" method="post">
          <div class ="regi-user">
            <p class="align-center">退会すると、次回のご利用時には再度の会員登録が必要になります</p>
            <input type="submit" value="退会する">
          </div>
        </form>
        <p class="align-center">引き続きサービスをご利用される方は <a href="mypage.php">こちら</a></p>
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
