<?php
require('function.php');

//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================
//入力されているか確認
if (!empty($_POST)) {
  debug('=============================================');
  debug('POST送信がありました。処理を開始します');

  //送信内容を変数に格納
  $email = $_POST['email'];

  //未入力チェック
  validRequired($email,'email');

  if (empty($err_msg)) {
    debug('未入力チェックOK');

    //メール形式・最大文字数チェック
    validEmail($email,'email');
    validMaxLen($email,'email',255);

    if (empty($err_msg)) {
      debug('バリデーションクリア');

      try {
        debug('DB接続処理を開始します');
        $dbh = dbConnect();
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        //SQL実行
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
          //処理継続
          debug('メールアドレスが確認できました');

          //認証キー発行
          debug('認証キーを発行します');
          $auth_key = makeRandkey(8);

          //メール送信準備
          $from = '';
          $to = $email;
          $subject = 'メール認証【Concent-rate】';
          $message = <<<EOT
{$email} 様

本メールアドレス宛にパスワード再発行のご依頼がありました。
下記URLにて認証キーをご入力いただくと
パスワードが再発行されます。

URL：http://localhost:8888/Concent-rate/passRemindRecieve.php
認証キー：
{$auth_key}
※認証キーの有効期限は30分となります。

再度認証キーを発行される場合は、下記URLへのアクセスをお願いいたします。
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

          //セッション変数に必要な情報を格納
          $_SESSION['auth_email'] = $email;
          $_SESSION['auth_key'] = $auth_key;
          $_SESSION['auth_key_limit'] = time() + (60 * 30);
          $_SESSION['js-msg'] = JSMSG02;

          debug('認証キー入力ページへ遷移します');
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
//CSSファイルとタイトルタグの設定
$css_title = basename(__FILE__,".php");
$p_title = 'メール認証';
//共通headタグ呼び出し
require('head.php');

//共通ヘッダー呼び出し
require('header.php');
?>

<!--　メインコンテンツ　-->
<main class="wrap">
  <div class="h1-narrow">
    <h1>メール認証</h1>
  </div>
  <div class="container">
    <form method="post" class="narrow">
      <p style="padding:0 0 50px;">ご登録のeメールアドレスに<br>パスワード再設定用のメールをお送りします</p>
      <div class="<?php if (!empty($err_msg['common'])) echo 'err'; ?>">
        <span><?php echo showErrMsg('common'); ?></span>
      </div>
      <div class ="regi-user">
        <div class="email-form">
          <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">eメール
            <span ><?php echo showErrMsg('email'); ?></span>
            <input type="text" name="email" placeholder="example@test.com" value="<?php echo getFormData('email') ?>">
          </label>
        </div>
        <input type="submit" value="送信する">
      </div>
    </form>
    <p>登録済みの方は <a href="login.php">ログイン画面へ戻る</a></p>
  </div>
</main>

<!--　共通フッター呼び出し　-->
<?php
require('footer.php');
 ?>
