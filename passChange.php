<?php
require('function.php');

//ログイン認証
require('auth.php');
//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================

//DBから情報を入手しておく
$userData = getUserData($_SESSION['user_id']);
debug('セッション変数の中身：'.print_r($userData,true));

//POST送信があったら処理開始
if (!empty($_POST)) {
  debug('=============================================');
  debug('POST送信がありました。処理を開始します');
  debug('POST送信の中身：'.print_r($_POST,true));

  //POSTデータを変数に格納
  $pass_old = $_POST['pass_old'];
  $pass_new = $_POST['pass_new'];
  $pass_new_re = $_POST['pass_new_re'];

  //フォームの未入力をチェック
  validRequired($pass_old, 'pass_old');
  validRequired($pass_new, 'pass_new');
  validRequired($pass_new_re, 'pass_new_re');

  if (empty($err_msg)) {
    debug('未入力チェッククリア');

    //新旧パスワードのバリデーションチェック
    validPass($pass_old, 'pass_old');
    validPass($pass_new, 'pass_new');

    if(empty($err_msg)){
      debug('パスワードの入力形式に問題ありません');

      //旧パスワードが正しいかチェック
      if (!password_verify($pass_old, $userData['password'])) {
        debug('旧パスワードに誤りがありました');
        $err_msg['pass_old'] = MSG13;

      }else{
        debug('正しい旧パスワードが入力されています');

        //新旧パスワードが異なっているかチェック
        if ($pass_old === $pass_new) {
          debug('パスワードが変更されていません');
          $err_msg['pass_new'] = MSG14;

        }else{
          debug('パスワードの変更を確認しました');
          //パスワード再確認とマッチしているか確認
          validMatch($pass_new, $pass_new_re, 'pass_new_re');

          if (empty($err_msg)) {
            debug('バリデーションクリア・DBに接続します');

            try {
              //データベース接続情報を用意
              $dbh = dbConnect();
              $sql = 'UPDATE users SET password = :pass WHERE id = :id';
              $data = array(':pass'=> password_hash($pass_new,PASSWORD_DEFAULT), ':id'=> $userData['id']);
              //クエリ発行
              $stmt = queryPost($dbh, $sql, $data);

              if ($stmt) {
                debug('$stmtの中身：'.print_r($stmt));
                debug('クエリ成功・パスワード変更が完了しました');
                $_SESSION['js-msg'] = JSMSG01;

                //パスワード変更の通知メールを設定
                $username = ($userData['name'])?$userData['name']: '匿名';
                $from = 'o.2106.basket@gmail.com';
                $to = $userData['email'];
                $subject = 'パスワード変更通知【Concent-rate】';
                $message = <<<EOT
{$username}さん
パスワードが変更されました。

==============================
Concent-rate カスタマーセンター
Email:

~評価を参考に「集中できる場所」を探そう~
Concent-rate(コンセントレート、コントレ)
URL:https://concent-rate.com
==============================
EOT;
                //sendMail($from, $to, $subject, $message);

                debug('マイページに遷移します');
                header('Location:mypage.php');
                exit;

              }else {
                debug('クエリに失敗しました');
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
  }
  debug('処理を終了します');
}

 ?>

<?php
$css_title = basename(__FILE__,".php");
$p_title = 'パスワード変更';
//共通headタグ呼び出し
require('head.php');

//共通ヘッダー呼び出し
require('header.php');
?>

<!--　メインコンテンツ　-->
<div class="wrap">
  <main>
    <div class="h1-wide">
      <h1>マイページ</h1>
    </div>
    <div class="mypage-inner">
      <section>
        <div class="h2_space">
          <h2>パスワード変更</h2>
        </div>
        <div class="<?php if (!empty($err_msg['common'])) echo 'err'; ?>">
          <span><?php echo showErrMsg('common'); ?></span>
        </div>
        <div class="for-space">
          <form class="wide" method="post">
            <div class ="regi-user">
              <div class="current-pass" style="margin-bottom:30px;">
                <label class="<?php if(!empty($err_msg['pass_old'])) echo 'err' ?>">現在のパスワード
                  <span><?php echo showErrMsg('pass_old'); ?></span>
                  <input type="password" name="pass_old" placeholder="半角英数字6文字以上" value="<?php echo getFormData('pass_old'); ?>">
                </label>
              </div>
              <div class="new-pass">
                <label class="<?php if(!empty($err_msg['pass_new'])) echo 'err' ?>">新しいパスワード
                  <span><?php echo showErrMsg('pass_new');?></span>
                  <input type="password" name="pass_new" placeholder="半角英数字6文字以上" value="<?php echo getFormData('pass_new'); ?>">
                </label>
              </div>
              <div class="email-form">
                <label class="<?php if(!empty($err_msg['pass_new_re'])) echo 'err' ?>">確認用パスワード
                  <span><?php echo showErrMsg('pass_new_re'); ?></span>
                  <input type="password" name="pass_new_re" placeholder="" value="<?php echo getFormData('pass_new_re'); ?>">
                </label>
              </div>

              <input type="submit" value="更新する">
            </div>
          </form>
        </div>
      </section>
    </div>
  </main>

  <?php require('sidebarRight.php'); ?>

</div>

<!--　共通フッター呼び出し　-->
<?php require('footer.php'); ?>
