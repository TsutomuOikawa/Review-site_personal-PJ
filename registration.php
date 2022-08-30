<?php
require('function.php');


//=========================================
//ここからバリデーションチェック

//入力されているか確認
if (!empty($_POST)) {

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

    //バリデーション②メール・パスワード形式チェック
    validEmail($email,'email');
    validPass($pass,'pass');

    //エラーがなければ次のバリデーションへ
    if (empty($err_msg)){

      //メール・パスワード文字数チェック
      validMaxLen($email,'email');
      validMaxLen($pass,'pass');
      validMinLen($pass,'pass');

      //エラーがなければ次のバリデーションへ
      if (empty($err_msg)) {
        validEmailDup($email);
        validMatch($pass,$pass_re,'pass_re');

//=========================================
//ここからDB接続

        if (empty($err_msg)) {
          $dbh = dbConnect();
          $sql = 'INSERT INTO users (email,password,login_time,create_date)
                  VALUES(:email,:password,:login_time,:create_date)';
          $data = array(':email'=>$email,':password'=>password_hash($pass,PASSWORD_DEFAULT),
                        ':login_time'=>date('Y-m-d H:i:s'),':create_date'=>date('Y-m-d H:i:s'));

          try {
            queryPost($dbh,$sql,$data);
            header('Location:mypage.php');
          } catch (\Exception $e) {
            error_log('エラー発生：'.$e->getMessage());
          }
        }
      }
    }
  }
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
<section class="wrap">
  <div class="h1-narrow">
    <h1>会員登録（無料）</h1>
  </div>
  <div class="container">
    <form method="post" class="narrow">
      <div class ="regi-user">
        <div class="email-form">
          <label>eメール
            <span class="err_msg"><?php if (!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
          </label>
          <input type="text" name="email" placeholder="example@test.com" value="">
        </div>
        <div class="pass-form">
          <label>パスワード
            <span class="err_msg"><?php if (!empty($err_msg['pass'])) echo $err_msg['pass']; ?></span>
          </label>
          <input type="text" name="pass" placeholder="半角英数字6文字以上" value="">
        </div>
        <div class="repass-form">
          <label>確認用パスワード
            <span class="err_msg"><?php if (!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; ?></span>
          </label>
          <input type="password" name="pass_re" value="">
        </div>
        <input type="submit" value="登録する">
      </div>
    </form>

    <p>登録済みの方は<a href="login.php">こちらからログイン</a></p>
  </div>
</section>
<!--　共通フッター呼び出し　-->
<?php
require('footer.php');
 ?>
