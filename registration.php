<?php
require('function.php');

//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================
//ここからバリデーションチェック

//POST送信されているか確認
if (!empty($_POST)) {
  debug('POST送信あり・バリデーションチェックに移行します');

  //送信内容を変数に格納
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_re = $_POST['pass_re'];

  //バリデーション①未入力チェック
  validRequired($email,'email');
  validRequired($pass,'pass');
  validRequired($pass_re,'pass_re');

  //エラーがなければ次のバリデーションへ
  if (empty($err_msg)) {
    debug('未入力項目なし');

    //バリデーション②メール・パスワード形式チェック
    validHalf($email,'email');
    validPass($pass,'pass');

    //エラーがなければ次のバリデーションへ
    if (empty($err_msg)){
      debug('メール・パスワード形式OK');

      //メール・パスワード文字数チェック
      validMaxLen($email,'email',255);
      validMaxLen($pass,'pass',255);
      validMinLen($pass,'pass',6);

      //エラーがなければ次のバリデーションへ
      if (empty($err_msg)) {
        debug('文字数OK');

        //メール重複、パスワード合致チェック
        validMatch($pass,$pass_re,'pass_re');
        validEmailDup($email);

//=========================================
//バリデーションオールクリア
//ここからDB接続

        if (empty($err_msg)) {
          debug('メール重複なし、パスワード合致');
          debug('バリデーションクリア・DBに登録します');

          try {
            //SQLに必要な情報を用意
            $dbh = dbConnect();
            $sql = 'INSERT INTO users (email,password,login_time,create_date)
                    VALUES(:email,:password,:login_time,:create_date)';
            $data = array(':email'=>$email,':password'=>password_hash($pass,PASSWORD_DEFAULT),
                          ':login_time'=>date('Y-m-d H:i:s'),':create_date'=>date('Y-m-d H:i:s'));

            //クエリ実行
            $stmt = queryPost($dbh,$sql,$data);

            if ($stmt) {
              debug('DBへの登録が完了しました');

              //マイページにログイン認証があるため、セッション情報を持たせる
              $sesLimit = 60*60;
              $_SESSION['login_date'] = time();
              $_SESSION['login_limit'] = $sesLimit;
              //ユーザーIDを格納
              $_SESSION['user_id'] = $dbh->lastInsertId();

              //問題がなければマイページへ
              debug('セッション情報の設定完了：'.print_r($_SESSION,true));
              debug('マイページへ遷移します');
              header('Location:mypage.php');
              exit;

            }else {
              error_log('クエリに失敗しました');
              $err_msg['common'] = MSG08;
            }
          } catch (\Exception $e) {
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG08;
          }
        }
      }
    }
  }
  debug('処理を終了します');
}
?>


<?php
//CSSファイルとタイトルタグの設定
$css_title = basename(__FILE__,".php");
$p_title = '会員登録';
//共通headタグ呼び出し
require('head.php');

//共通ヘッダー呼び出し
require('header.php');
?>

<!--　メインコンテンツ　-->
<main class="wrap">
  <div class="h1-narrow">
    <h1>会員登録（無料）</h1>
  </div>
  <div class="container">
    <form method="post" class="narrow">
      <div class ="regi-user">

        <div class="<?php if (!empty($err_msg['common'])) echo 'err'; ?>">
          <span><?php echo showErrMsg('common'); ?></span>
        </div>

        <div class="email-form">
          <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">eメール
            <span ><?php echo showErrMsg('email'); ?></span>
            <input type="text" name="email" placeholder="example@test.com" value="<?php if (!empty($_POST['email'])) echo $_POST['email']; ?>">
          </label>
        </div>

        <div class="pass-form">
          <label class="<?php if (!empty($err_msg['pass'])) echo 'err'; ?>">パスワード
            <span><?php  echo showErrMsg('pass'); ?></span>
            <input type="password" name="pass" placeholder="半角英数字6文字以上" value="<?php if (!empty($_POST['pass'])) echo $_POST['pass']; ?>">
          </label>
        </div>

        <div class="repass-form">
          <label class="<?php if (!empty($err_msg['pass_re'])) echo 'err'; ?>">パスワード（再入力）
            <span><?php  echo showErrMsg('pass_re'); ?></span>
            <input type="password" name="pass_re" value="<?php if (!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
          </label>
        </div>

        <input type="submit" value="登録する">
      </div>
    </form>
    <p>登録済みの方は <a href="login.php">こちらからログイン</a></p>
  </div>
</main>

<!--　共通フッター呼び出し　-->
<?php
require('footer.php');
 ?>
