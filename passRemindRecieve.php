<?php
require('function.php');
// デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================

// セッション変数に認証キーがなければ遷移
if (empty($_SESSION['auth_key'])) {
  debug('認証キーなし・発行画面へ遷移します');
  header('Location:passRemindSend.php');
  exit;
}

// POST送信されたらスタート
if (!empty($_POST)) {
  debug('=============================================');
  debug('POST送信あり・処理を開始します');

  // 送信された値を変数に格納
  $auth_key = $_POST['auth_key'];

  // バリデーションチェック(セキュリティ対策のためにも実施)
  validRequired($auth_key, 'auth_key');

  if (empty($err_msg)) {
    debug('未入力チェッククリア');

    // 固定長チェックと半角英数字チェック
    validHalf($auth_key, 'auth_key');
    validLength($auth_key, 'auth_key', 8);

    if (empty($err_msg)) {
      debug('バリデーションクリア');

      // 認証キーが正しくなかったら処理を終了
      if ($auth_key !== $_SESSION['auth_key']) {
        debug('認証キーが間違っています');
        $err_msg['auth_key'] = MSG17;
      }
      // 認証キーの有効期限を確認
      if ($_SESSION['auth_key_limit'] < time()) {
        debug('認証キーの有効期限切れです');
        $err_msg['auth_key'] = MSG18;
      }

      if (empty($err_msg)) {
        debug('正しい認証キーが入力されました');

        // パスワードを再発行し、DBを更新
        try {

          $second_pass = makeRandkey(8);
          debug('パスワード再発行完了・DBに接続します');

          $dbh = dbConnect();
          $sql = 'UPDATE users SET password = :pass WHERE email = :email AND delete_flg = 0';
          $data = array(':pass' => password_hash($second_pass,PASSWORD_DEFAULT), ':email' => $_SESSION['auth_email']);
          // SQL発行
          $stmt = queryPost($dbh, $sql, $data);

          if ($stmt) {
            debug('DBの更新が完了しました');

            // メール送信準備
            $from = 'o.2106.baket@gmail.com';
            $to = $_SESSION['auth_email'];
            $subject = 'パスワード再発行のお知らせ【Concent-rate】';
            $message = <<<EOT
{$to} 様
本メールアドレス宛にパスワードが再発行されました。
パスワードをご確認いただき、下記URLよりログインください。

ログインページURL：http://localhost:8888/Concent-rate/login.php
パスワード：
{$second_pass}
※ログイン後、パスワードの変更をお願いいたします。

==============================
Concent-rate カスタマーセンター
Email:

~評価を参考に「集中できる場所」を探そう~
Concent-rate(コンセントレート、コントレ)
URL:https://concent-rate.com
==============================
EOT;
            sendMail($from, $to, $subject, $message);
            // 一度セッションを削除し、jsメッセージを格納
            session_unset();
            $_SESSION['js-msg'] = JSMSG02;

            debug('ログインページに遷移します');
            header('Location:login.php');
            exit;

          }else{
            debug('DBの更新に失敗しました');
            $err_msg['common'] = MSG08;
          }
        } catch (\Exception $e) {
          error_log('エラー発生:'.$e->getMessage());
          $err_msg['common'] = MSG08;
        }
      }
    }
  }
}
 ?>

<?php
// タイトルタグの設定
$p_title = 'パスコード入力';
// 共通headタグ呼び出し
require('head.php');
// 共通ヘッダー呼び出し
require('header.php');
?>

<div id="js_show_msg" style="display:none;" class="js_msg_window" >
  <p><?php echo getSessionMsg('js-msg'); ?></p>
</div>
<!--　メインコンテンツ　-->
<main class="page-wrapper">
  <h1 class="page_title">メール認証</h1>
  <div class="page_contents--center mainContents-wrapper">

    <form method="post" class="scrollContents-wrapper baseColor">

      <h2 class="subTitle --fontCenter">認証コード入力フォーム</h2>
      <div class="contents_form">

        <div class="area-msg">
          <?php echo showErrMsg('common'); ?>
        </div>

        <p class="form_notion form_lastItem --fontCenter" style="line-height:1.5;">
          メールに届いた認証コードをご入力ください<br>
          パスワードを再発行いたします
        </p>

        <label>
          <p class="form_title">認証コード</p>
          <input type="text" name="auth_key" class="form_input form_input--mainContents <?php if (!empty($err_msg['auth_key'])) echo 'err'; ?>" value="<?php echo getFormData('auth_key'); ?>">
        </label>
        <div class="area-msg">
          <?php echo showErrMsg('auth_key'); ?>
        </div>

        <input type="submit" class="btn btn--submit btn--submit--mainContents" value="送信する">

      </div>
    </form>
  </div>
</main>

<!--　共通フッター呼び出し　-->
<?php require('footer.php'); ?>
