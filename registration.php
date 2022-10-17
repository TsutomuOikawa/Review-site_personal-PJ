<?php
require('function.php');
//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================
//ここからバリデーションチェック

//POST送信されているか確認
if (!empty($_POST)) {
  debug('=============================================');
  debug('POST送信あり・処理を開始します');

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
    validMaxLen($email,'email',255);
    validEmail($email,'email');
    validPass($pass,'pass');

    //エラーがなければ次のバリデーションへ
    if (empty($err_msg)){
      debug('メール・パスワード形式OK');

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
  debug('処理を終了します');
}
?>


<?php
//タイトルタグの設定
$p_title = '会員登録';
//共通headタグ呼び出し
require('head.php');

//共通ヘッダー呼び出し
require('header.php');
?>

<!--　メインコンテンツ　-->
<main class="page-wrapper">
  <h1 class="page_title">会員登録（無料）</h1>
  <div class="page_contents--center mainContents-wrapper">

    <form method="post" class="scrollContents-wrapper baseColor">

      <h2 class="subTitle --fontCenter">ご登録フォーム</h2>
      <div class ="contents_form">

        <div class="area-msg">
          <?php echo showErrMsg('common'); ?>
        </div>

        <label>
          <div class="form_title">
            <span class="form_label form_label--required">必須</span>
            メールアドレス
          </div>
          <input type="text" name="email" class="form_input form_input--mainContents <?php if (!empty($err_msg['email'])) echo 'err'; ?>" value="<?php echo getFormData('email'); ?>" placeholder="example@test.com">
        </label>
        <div class="area-msg">
          <?php echo showErrMsg('email'); ?>
        </div>

        <label>
          <div class="form_title">
            <span class="form_label form_label--required">必須</span>
            パスワード
            <span class="font-sizeS">（半角英数字6文字以上）</span>
          </div>
          <input type="password" name="pass" class="form_input form_input--mainContents <?php if (!empty($err_msg['pass'])) echo 'err'; ?>" value="<?php echo getFormData('pass'); ?>">
        </label>
        <div class="area-msg">
          <?php echo showErrMsg('pass'); ?>
        </div>

        <label>
          <div class="form_title">
            <span class="form_label form_label--required">必須</span>
            確認のためもう一度入力してください
          </div>
          <input type="password" name="pass_re" class="form_input form_input--mainContents <?php if (!empty($err_msg['pass_re'])) echo 'err'; ?>" value="<?php echo getFormData('pass_re'); ?>">
        </label>
        <div class="area-msg">
          <?php echo showErrMsg('pass_re'); ?>
        </div>

        <input type="submit" class="btn btn--submit btn--submit--mainContents" value="登録する">
        <p class="form_notion"><a href="login.php" class="--hoverLine">&gt 登録済みの方はこちら</a></p>

      </div>
    </form>
  </div>
</main>

<!--　共通フッター呼び出し　-->
<?php require('footer.php'); ?>
