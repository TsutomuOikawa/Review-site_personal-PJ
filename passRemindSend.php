<?php
require('function.php');
// デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================

// 入力されているか確認
if (!empty($_POST)) {
  debug('=============================================');
  debug('POST送信あり・処理を開始します');

  // 送信内容を変数に格納
  $email = $_POST['email'];

  // 未入力チェック
  validRequired($email,'email');

  if (empty($err_msg)) {
    debug('未入力チェックOK');

    // メール形式・最大文字数チェック
    validEmail($email,'email');
    validMaxLen($email,'email',255);

    if (empty($err_msg)) {
      debug('バリデーションクリア');

      try {
        debug('DB接続処理を開始します');
        $dbh = dbConnect();
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        // SQL実行
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt -> fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
          debug('SQLにエラーが発生しました');
          $err_msg['common'] = MSG08;
        }
        if(array_shift($result) === '0'){
          debug('合致するメールアドレスがありませんでした');
          $err_msg['email'] = MSG15;

        }else{
          // 処理継続
          debug('メールアドレスが確認できました');

          // 認証コード発行
          debug('認証コードを発行します');
          $auth_key = makeRandkey(8);

          // メール送信準備
          $from = 'o.2106.basket@gmail.com';
          $to = $email;
          $subject = 'メール認証【Concent-rate】';
          $message = <<<EOT
{$email} 様

本メールアドレス宛にパスワード再発行のご依頼がありました。
下記URL先の画面にて認証コードをご入力いただくと
パスワードが再発行されます。

URL：http://localhost:8888/Concent-rate/passRemindRecieve.php
認証コード：{$auth_key}
※認証コードの有効期限は30分となります。

再度認証コードを発行される場合は、下記URLへのアクセスをお願いいたします。
http://localhost:8888/Concent-rate/passRemindSend.php

==============================
Concent-rate カスタマーセンター
Email:

~評価を参考に「集中できる場所」を探そう~
Concent-rate(コンセントレート、コントレ)
URL:https://concent-rate.com
==============================
EOT;
          sendMail($from, $to, $subject, $message);

          // セッション変数に必要な情報を格納
          $_SESSION['auth_email'] = $email;
          $_SESSION['auth_key'] = $auth_key;
          $_SESSION['auth_key_limit'] = time() + (60 * 30);
          $_SESSION['js-msg'] = JSMSG02;

          debug('認証コード入力ページへ遷移します');
          header('Location:passRemindRecieve.php');
          exit;
        }

      } catch (\Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG08;
      }
    }
  }
}
 ?>


<?php
// タイトルタグの設定
$p_title = 'メール認証';
// 共通headタグ呼び出し
require('head.php');
// 共通ヘッダー呼び出し
require('header.php');
?>

<!--　メインコンテンツ　-->
<main class="page-wrapper">
  <div class="container">
    <h1 class="container_title">パスワード再発行手続き</h1>
    <div class="container_body">
      <form method="post" class="module form container_body--form">
        <h2 class="module_title">メール認証</h2>
        <div class="module_body">
          <div class="form_errMsg">
            <?php echo showErrMsg('common'); ?>
          </div>
          <p class="form_notion" style="text-align:center;">ご登録のメールアドレスに<br>認証コードをお送りします</p>
          <p class="form_lastItem" style="text-align:center;">メール認証の完了後<br><span class="sp-delete">、</span>パスワードが再発行されます</p>
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
          <button type="submit" class="btn btn--submit">送信する</button>
          <p class="form_notion"><a href="login.php" class="--hoverLine">&gt ログイン画面へ戻る</a></p>
        </div>
      </form>
    </div>

  </div>
</main>

<!--　共通フッター呼び出し　-->
<?php require('footer.php'); ?>
