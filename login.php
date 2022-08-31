<?php

require('function.php');

//ログイン認証
require('auth.php');


//=========================================
//ここからバリデーションチェック

//入力されているか確認
if (!empty($_POST)) {

  //送信内容を変数に格納
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $login_check = ($_POST['login_check']) ? true : false;

  //バリデーション①未入力チェック
  validRequired($email,'email');
  validRequired($pass,'pass');

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

//=========================================
//ここからDB接続
//メール情報をもとに登録パスワードを取得

      if (empty($err_msg)) {
        //データベース接続情報を用意
        $dbh = dbConnect();
        $sql = 'SELECT password,id FROM users WHERE(email = :email)';
        $data = array(':email' => $email);

        try {
          //SQLを実行してデータを取得
          $stmt = queryPost($dbh,$sql,$data);
          //データを$resultに格納（配列形式）
          $result = $stmt -> fetch(PDO::FETCH_ASSOC);

          //その後入力パスワードと照合
          if (!empty($result) && password_verify($pass,array_shift($result))) {

            //セッション時間のデフォルトを1時間に設定
            $sesLimit = 60*60;

            //最終ログイン日時を現在時間に更新
            $_SESSION['login_date'] = time();

            //パスワードがマッチしたらチェックボックスを確認
            if ($login_check) {
              //チェックされていたら期限を30日に更新
              $_SESSION['login_limit'] = $sesLimit *24*30;
            }else {
              //チェックされていないのでセッションはデフォルトに設定
              $_SESSION['login_limit'] = $sesLimit;
            }
            //セッション情報にidを持たせてページ遷移
            $_SESSION['user_id'] = $result['id'];
            header('Location:mypage.php');

          }else {
            //パスワードがマッチしていない
            $err_msg['email'] = MSG09;
          }

        //データベース接続エラー
        } catch (\Exception $e) {
          error_log('エラー発生：'.$e->getMessage());
          $err_msg['pass'] = MSG07;
        }
      }
    }
  }
}
 ?>

 <?php
 //CSSファイルとタイトルタグの設定
 $css_title = basename(__FILE__,".php");
 $p_title = 'ログイン';
 //共通headタグ呼び出し
 require('head.php');

 //共通ヘッダー呼び出し
 require('header.php');
 ?>

<!--　メインコンテンツ　-->
<main class="wrap">
  <div class="h1-narrow">
    <h1>ログイン</h1>
  </div>
  <div class="container">
    <form method="post" class="narrow">
      <div class ="regi-user">
        <label>eメール
          <span class="err_msg"><?php if (!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
        </label>
        <input type="text" name="email" placeholder="example@test.com" value="">

        <label>パスワード
          <span class="err_msg"><?php if (!empty($err_msg['pass'])) echo $err_msg['pass']; ?></span>
        </label>
        <input type="password" name="pass" placeholder="ご登録のパスワードを入力してください" value="">
        <div class="login-preserve">
          <input type="checkbox" name="login_check" value="">ログイン状態を保存する
        </div>
        <input type="submit" value="送信">
      </div>
    </form>
    <p class="pass_forget">パスワードをお忘れの方は <a href="passReminder.php">こちら</a> </p>
    <p>会員登録がまだの方は <a href="registration.php">こちらからご登録</a>ください</p>
  </div>
</main>

<!--　共通フッター呼び出し　-->
<?php
require('footer.php');
 ?>
