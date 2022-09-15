<?php
require('function.php');

//ログイン認証
require('auth.php');
//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================
//DBからユーザー情報を取得しておく（POSTがなくてもプロフィール欄に表示）
$dbFormData = getUserData($_SESSION['user_id']);
debug('取得した情報：'.print_r($dbFormData,true));

//POST送信があったら処理スタート
if (!empty($_POST)) {
  debug('POST送信がありました');

  $name = $_POST['name'];
  $tel = $_POST['tel'];
  $email = $_POST['email'];
  $age = (!empty($_POST['age'])) ?$_POST['age'] :0;
  $prefecture = $_POST['prefecture'];
  $city = $_POST['city'];

  if ($name !== $dbFormData['name']) {
    debug('名前変更あり');
    validMaxLen($name,'name',255);
  }
  if ($tel !== $dbFormData['tel']) {
    debug('電話番号変更あり・バリデーションチェック実施');
    validTel($tel,'tel');
  }
  if ($email !== $dbFormData['email']) {
    debug('email変更あり・バリデーションチェック実施');
    validEmail($email,'email');
    validMaxLen($email,'email',255);
    validRequired($email,'email');
    if (empty($err_msg)) {
      validEmailDup($email);
    }
  }
  if ($age !== (int)$dbFormData['age']) {
    debug('年齢変更あり・バリデーションチェック実施');
    validAge($age, 'age');
  }
//  if ($prefecture !== $dbFormData) {
//    debug('都道府県変更あり・バリデーションチェック実施');

//  }
//  if ($city !== $dbFormData['city']) {
//    debug('市区町村変更あり・バリデーションチェック実施');
//    validMaxLen($city, 'city', 10);
//    validCity($city, 'city');
//  }

  if (empty($err_msg)) {
    debug('バリデーションOK・DBを更新します');

    try {
      $dbh = dbConnect();
      $sql = 'UPDATE users SET name = :name, email = :email, tel = :tel, age = :age WHERE id = :u_id';
      $data = array(':name'=> $name, ':email'=> $email, ':tel'=> $tel, ':age'=> $age, 'u_id'=>$_SESSION['user_id']);
      //SQL実行
      $stmt = queryPost($dbh, $sql, $data);
      debug(print_r($stmt,true));

      if($stmt){
        debug('クエリ成功');
        debug('マイページへ遷移します');
        header('Location:mypage.php');
        exit;

      }else {
        debug('クエリ失敗');
        $err_msg = MSG08;
      }
    } catch (\Exception $e) {
      error_log('エラー発生：'. $e ->getMessage());
      $err_msg = MSG08;
    }
  }
  debug('処理を終了します');
}
 ?>


<?php
$css_title = basename(__FILE__,".php");
$p_title = 'プロフィール編集';
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
          <h2>プロフィール編集</h2>
        </div>
        <div class="for-space">
          <form class="wide" method="post">
            <div class ="regi-user">
              <div class="name-form">
                <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">名前（ニックネームも可）
                  <span><?php if (!empty($err_msg['name'])) echo $err_msg['name']; ?></span>
                  <input type="text" name="name" placeholder="コントレ太郎" value="<?php echo getFormData('name'); ?>">
                </label>
              </div>
              <div class="tel-form">
                <label class="<?php if(!empty($err_msg['tel'])) echo 'err'; ?>">電話
                  <span><?php if (!empty($err_msg['tel'])) echo $err_msg['tel']; ?></span>
                  <input type="text" name="tel" placeholder="ハイフン不要" value="<?php echo getFormData('tel'); ?>">
                </label>
              </div>
              <div class="email-form">
                <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">eメール
                  <span><?php if (!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
                  <input type="text" name="email" placeholder="example@test.com" value="<?php echo getFormData('email'); ?>">
                </label>
              </div>
              <div class="age-form">
                <label class="<?php if(!empty($err_msg['age'])) echo 'err'; ?>">年齢
                  <span><?php if (!empty($err_msg['age'])) echo $err_msg['age']; ?></span>
                  <input type="number" min="0"　max="100" name="age" value="<?php echo getFormData('age'); ?>">
                </label>
              </div>
              <div class="prefecture-form">
                <label class="<?php if(!empty($err_msg['prefecture'])) echo 'err'; ?>">都道府県
                  <span><?php if (!empty($err_msg['prefecture'])) echo $err_msg['prefecture']; ?></span>
                  <select name="prefecture" size="1">
                    <option disabled >選択する</option>
                    <?php
                    $prefecturesList = ['北海道','青森県','岩手県'];
                    foreach ($prefecturesList as $prefecture) {
                      echo '<option>'.$prefecture.'</option>';
                    }
                     ?>
                  </select>
                </label>
              </div>
              <div class="city-form">
                <label class="<?php if(!empty($err_msg['city'])) echo 'err'; ?>">市区町村
                  <span><?php if (!empty($err_msg['city'])) echo $err_msg['city']; ?></span>
                  <input type="text" name="city" placeholder="新宿区" value="">
                </label>
              </div>
              <input type="submit" value="更新する">
            </div>
          </form>
        </div>
      </section>
    </div>
  </main>
  <?php
  require('sidebarRight.php'); ?>

</div>

<!--　共通フッター呼び出し　-->
<?php
require('footer.php');
 ?>
