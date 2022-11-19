<?php
require('function.php');
//ログイン認証
require('auth.php');
//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================

//POST送信されたら
if (!empty($_POST)) {
  debug('=============================================');
  debug('POST送信あり・処理を開始します');

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
 // ページタイトルタグの設定
 $p_title = '退会';
 // 共通headタグ呼び出し
 require('head.php');
 // 共通ヘッダー呼び出し
 require('header.php');
 ?>

<!--　メインコンテンツ　-->
<div class="page-wrapper">

  <div class="container">
    <h1 class="container_title">マイページ</h1>
    <div class="container_body container_body--divide">
      <main class="container_mainBody">
        <form method="post" class="module">
          <h2 class="module_title module_title--surround">退会</h2>
          <div class="module_body form--multi form--wide">

            <div class="area-msg">
              <?php echo showErrMsg('common'); ?>
            </div>

            <p class="form_notion form_lastItem" style="text-align:center;">次回のご利用時には<br>再度の会員登録が必要になります</p>
            <button type="submit" class="btn btn--submit">退会する</button>
            <p class="form_notion"><a href="mypage.php" class="--hoverLine">&gt マイページに戻る</a></p>
          </div>
        </form>
      </main>
      <?php require('sidebarRight.php'); ?>

    </div>
  </div>
</div>

<!--　共通フッター呼び出し　-->
<?php require('footer.php'); ?>
