<?php
require('function.php');
//ログイン認証
require('auth.php');
//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================

//POST送信されているか確認
if (!empty($_POST)) {
  debug('=============================================');
  debug('POST送信あり・処理を開始します');

  //送信内容を変数に格納
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $login_check = (!empty($_POST['login_check'])) ? true : false;

  //バリデーション①未入力チェック
  validRequired($email,'email');
  validRequired($pass,'pass');

  //エラーがなければ次のバリデーションへ
  if (empty($err_msg)) {
    debug('未入力項目なし');

    //バリデーション②メール・パスワード形式チェック
    validMaxLen($email,'email',255);
    validEmail($email,'email');
    validPass ($pass,'pass');

//=========================================
//ここからDB接続
//メール情報をもとに登録パスワードを取得

    if (empty($err_msg)) {
      debug('バリデーションOK');
      debug('DB接続に移行します');

      try {
      //データベース接続情報を用意
      $dbh = dbConnect();
      $sql = 'SELECT password,id FROM users WHERE email = :email AND delete_flg = 0';
      $data = array(':email' => $email);

        //SQLを実行してデータを取得
        $stmt = queryPost($dbh,$sql,$data);
        //データを$resultに格納（配列形式）
        $result = $stmt -> fetch(PDO::FETCH_ASSOC);
        debug('メールアドレスの検索が完了しました');

        //その後入力パスワードと照合
        if (!empty($result) && password_verify($pass,array_shift($result))) {
          debug("メールアドレス、パスワードが登録情報に一致しました");

          //セッション時間のデフォルトを1時間に設定
          $sesLimit = 60 *60;
          //最終ログイン日時を現在時間に更新
          $_SESSION['login_date'] = time();
          //パスワードがマッチしたらチェックボックスを確認
          if ($login_check) {
            //チェックされていたら期限を30日に更新
            debug('ログイン延長のチェックあり・セッション期限を延長します');
            $_SESSION['login_limit'] = $sesLimit *24*3;
          }else {
            //チェックされていないのでセッションはデフォルトに設定
            debug('ログイン延長のチェックはありません');
            $_SESSION['login_limit'] = $sesLimit;
          }
          //セッション情報にidを持たせてページ遷移
          $_SESSION['user_id'] = $result['id'];

          debug('マイページへ遷移します');
          header('Location:mypage.php');
          exit;

        }else {
          //パスワードがマッチしていない
          $err_msg['email'] = MSG09;
          debug('メールアドレスまたはパスワードに誤りがありました');
        }

      //データベース接続エラー
      } catch (\Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG08;
      }
    }
  }
  debug('処理を終了します');
}
 ?>

 <?php
 //タイトルタグの設定
 $p_title = 'ログイン';
 //共通headタグ呼び出し
 require('head.php');
 //共通ヘッダー呼び出し
 require('header.php');
 ?>

 <div id="js_show_msg" style="display:none;" class="js_msg_window" >
   <p><?php echo getSessionMsg('js-msg'); ?></p>
 </div>
<!--　メインコンテンツ　-->
<main id="login" class="page-wrapper">
  <div class="container">

    <h1 class="container_title">ログイン</h1>
    <div class="container_body container_body--form">

      <form method="post" class="module form">
        <h2 class="module_title">入力フォーム</h2>
        <div class ="module_body">
          <div class="form_errMsg">
            <?php echo showErrMsg('common'); ?>
          </div>

          <label>
            <div class="form_name">
              <span class="form_label form_label--required">必須</span>
              メールアドレス
            </div>
            <input type="text" name="email" class="form_input <?php if (!empty($err_msg['email'])) echo 'err'; ?>" value="<?php echo getFormData('email'); ?>" placeholder="example@test.com">
          </label>
          <div class="form_errMsg">
            <?php echo showErrMsg('email'); ?>
          </div>

          <label>
            <div class="form_name">
              <span class="form_label form_label--required">必須</span>
              パスワード
              <span class="font-sizeS">（半角英数字6文字以上）</span>
            </div>
            <input type="password" name="pass" class="form_input  <?php if (!empty($err_msg['pass'])) echo 'err'; ?>" value="<?php echo getFormData('pass'); ?>">
          </label>
          <div class="form_errMsg">
            <?php echo showErrMsg('pass'); ?>
          </div>

          <div class="form_lastItem">
            <label class="form_name">
              <input type="checkbox" name="login_check">ログイン状態を保存する
            </label>
          </div>

          <button type="submit" class="btn btn--submit">ログイン</button>
          <p class="form_notion"><a href="passRemindSend.php" class="--hoverLine">&gt パスワードをお忘れの方はこちら</a></p>
          <p class="form_notion"><a href="registration.php" class="--hoverLine">&gt 会員登録がまだの方はこちら</a></p>

        </div>
      </form>
    </div>
  </div>
</main>

<!--　共通フッター呼び出し　-->
<?php require('footer.php'); ?>
