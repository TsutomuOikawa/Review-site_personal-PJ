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

  <div class="container">
    <h1 class="container_title">メール認証</h1>
    <div class="container_body">

      <form method="post" class="module form">
        <h2 class="module_title">認証コード入力フォーム</h2>
        <div class="module_body">

          <div class="form_errMsg">
            <?php echo showErrMsg('common'); ?>
          </div>
          <p class="form_notion" style="text-align:center;">届いた認証コードを入力してください</p>
          <p class="form_lastItem" style="text-align:center;">パスワードを再発行いたします</p>
          <label>
            <div class="form_name">
              <span class="form_label form_label--required">必須</span>
              認証コード
            </div>
            <input type="text" name="auth_key" class="form_input  <?php if (!empty($err_msg['auth_key'])) echo 'err'; ?>" value="<?php echo getFormData('auth_key'); ?>">
          </label>
          <div class="form_errMsg">
            <?php echo showErrMsg('auth_key'); ?>
          </div>
          <button type="submit" class="btn btn--submit">認証する</button>
        </div>
      </form>
    </div>

  </div>
</main>

<!--　共通フッター呼び出し　-->
<?php require('footer.php'); ?>
