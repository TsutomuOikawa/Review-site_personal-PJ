<?php
require('function.php');

//ログイン認証
require('auth.php');

 ?>

<?php
$css_title = basename(__FILE__,".php");
$p_title = 'パスワード変更';
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
    </div>
    <div class="mypage-inner">
      <section>
        <div class="h2_space">
          <h2>パスワード変更</h2>
        </div>
        <div class="for-space">
          <form class="wide" method="post">
            <div class ="regi-user">
              <div class="current-pass" style="margin-bottom:30px;">
                <label>現在のパスワード
                  <span class="err_msg"><?php if (!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
                </label>
                <input type="text" name="email" placeholder="半角英数字6文字以上" value="">
              </div>
              <div class="new-pass">
                <label>新しいパスワード
                  <span class="err_msg"><?php if (!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
                </label>
                <input type="text" name="email" placeholder="半角英数字6文字以上" value="">
              </div>
              <div class="email-form">
                <label>確認用
                  <span class="err_msg"><?php if (!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
                </label>
                <input type="text" name="email" placeholder="" value="">
              </div>

              <input type="submit" value="更新する">
            </div>
          </form>
        </div>
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
