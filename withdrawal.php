<?php
require('function.php');
//ログイン認証
require('auth.php');
//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================

//ここから退会処理
//POST送信されたら
if (!empty($_POST)) {
  debug('退会ボタンが押されました');

  try {
    //データベース情報を用意
    $dbh = dbConnect();

    $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id =:u_id';
    $sql2 = 'UPDATE favorite SET delete_flg = 1 WHERE user_id =:u_id';

    $data = array(':u_id' => $_SESSION['user_id']);

    //SQLを発火
    $stmt1 = queryPost($dbh, $sql1, $data);
    $stmt2 = queryPost($dbh, $sql2, $data);

    if ($stmt1 && $stmt2) {
      debug('usersテーブルとfavoriteテーブルの更新が完了しました');

      session_destroy();
      debug('セッション情報の破棄が完了しました');
      debug('セッション変数の中身：'.print_r($_SESSION,true));

      debug('トップページへ遷移します');
      header("Location:index.php");
    }else {
      error_log('エラー発生：退会処理に失敗しました');
      $err_msg = MSG08;
    }
  } catch (\Exception $e) {
    error_log('エラー発生：'. $e ->getMessage());
    $err_msg = MSG08;
  }
}

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
    </div>
    <div class="mypage-inner">
      <section>
        <div class="h2_space">
          <h2>退会</h2>
        </div>
        <div class="align-center <?php if(!empty($err_msg)) echo 'err'; ?>">
          <span><?php if(!empty($err_msg)) echo $err_msg; ?></span>
        </div>
        <form class="wide" method="post">
          <div class ="regi-user">
            <p class="align-center">退会すると、次回のご利用時には再度の会員登録が必要になります</p>
            <input type="submit" value="退会する" name="submit">
          </div>
        </form>
        <p class="align-center">引き続きサービスをご利用される方は <a href="mypage.php">こちら</a></p>
      </section>
    </div>
  </main>
<?php
  require('sidebarRight.php');
?>

</div>


<!--　共通フッター呼び出し　-->
<?php
require('footer.php');
 ?>
