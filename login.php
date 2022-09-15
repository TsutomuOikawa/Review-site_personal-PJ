<?php
require('function.php');

//ログイン認証
require('auth.php');

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
  $login_check = (!empty($_POST['login_check'])) ? true : false;

  //バリデーション①未入力チェック
  validRequired($email,'email');
  validRequired($pass,'pass');

  //エラーがなければ次のバリデーションへ
  if (empty($err_msg)) {
    debug('未入力項目なし');

    //バリデーション②メール・パスワード形式チェック
    validEmail($email,'email');
    validPass($pass,'pass');

    //エラーがなければ次のバリデーションへ
    if (empty($err_msg)){
      debug('メール・パスワード形式OK');

      //メール・パスワード文字数チェック
      validMaxLen($email,'email',255);
      validMaxLen($pass,'pass',255);
      validMinLen($pass,'pass',6);

//=========================================
//ここからDB接続
//メール情報をもとに登録パスワードを取得

      if (empty($err_msg)) {
        debug('文字数OK');
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
            $sesLimit = 60*60;
            //最終ログイン日時を現在時間に更新
            $_SESSION['login_date'] = time();
            //パスワードがマッチしたらチェックボックスを確認
            if ($login_check) {
              //チェックされていたら期限を30日に更新
              debug('ログイン延長のチェックあり・セッション期限を延長します');
              $_SESSION['login_limit'] = $sesLimit *24*30;
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
  }
  debug('処理を終了します');
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
        <div class="<?php if (!empty($err_msg['common'])) echo 'err'; ?>">
          <span><?php if (!empty($err_msg['common'])) echo $err_msg['common']; ?></span>
        </div>

        <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">eメール
          <span><?php if (!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
          <input type="text" name="email" placeholder="example@test.com" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
        </label>

        <label class="<?php if (!empty($err_msg['pass'])) echo 'err'; ?>">パスワード
          <span><?php if (!empty($err_msg['pass'])) echo $err_msg['pass']; ?></span>
          <input type="password" name="pass" placeholder="ご登録のパスワードを入力してください" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
        </label>

        <div class="login-preserve">
          <label>
            <input type="checkbox" name="login_check">ログイン状態を保存する
          </label>
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
