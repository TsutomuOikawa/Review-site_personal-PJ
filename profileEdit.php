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
$dbPrefData = getPrefData();

//POST送信があったら処理スタート
if (!empty($_POST)) {
  debug('=============================================');
  debug('POST送信あり・処理を開始します');

  $name = $_POST['name'];
  $tel = $_POST['tel'];
  $email = $_POST['email'];
  $age = (!empty($_POST['age'])) ?$_POST['age'] :0;
  $prefecture = $_POST['prefecture_id'];
  $city = $_POST['city'];

  if ($name !== $dbFormData['name']) {
    debug('名前変更あり');
    validMaxLen($name,'name',255);
  }
  if ($tel !== $dbFormData['tel'] && $tel !== '') {
    debug('電話番号変更あり・バリデーションチェック実施');
    validTel($tel,'tel');
  }
  if ($email !== $dbFormData['email']) {
    debug('email変更あり・バリデーションチェック実施');
    validRequired($email,'email');
    if (empty($err_msg)) {
      validEmail($email,'email');
      validMaxLen($email,'email',255);
      if (empty($err_msg)) {
        validEmailDup($email);
      }
    }
  }
  if ($age !== (int)$dbFormData['age']) {
    debug('年齢変更あり・バリデーションチェック実施');
    validAge($age, 'age');
  }
 if ($prefecture !== $dbFormData['prefecture_id']) {
   debug('都道府県変更あり・バリデーションチェック実施');
   validOptSelect($prefecture, 'prefecture_id');
 }
 if ($city !== $dbFormData['city']) {
   debug('市区町村変更あり・バリデーションチェック実施');
   validMaxLen($city, 'city', 10);
   validOptCity($city, 'city');
 }

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

        $_SESSION['js-msg'] = JSMSG04;
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
$p_title = 'プロフィール編集';
//共通headタグ呼び出し
require('head.php');
//共通ヘッダー呼び出し
require('header.php');
?>
<!--　メインコンテンツ　-->
<div class="page-wrapper">

  <div class="container">
    <h1 class="container_title">マイページ</h1>
    <div class="container_body container_body--divide">
      <main class="container_mainBody">

        <form method="post" class="module form form--wide">
          <h2 class="module_title module_title--surround">プロフィール編集</h2>
          <div class="module_body">

            <div class="form_errMsg">
              <?php echo showErrMsg('common'); ?>
            </div>
            <label>
              <div class="form_name">
                <span class="form_label form_label--optional">任意</span>
                名前
                <span class="font-sizeS">（ニックネーム可）</span>
              </div>
              <input type="text" name="name" class="form_input  <?php if(!empty($err_msg['name'])) echo 'err'; ?>" value="<?php echo getFormData('name'); ?>" placeholder="コントレ太郎">
            </label>
            <div class="form_errMsg">
              <?php echo showErrMsg('name'); ?>
            </div>
            <label>
              <div class="form_name">
                <span class="form_label form_label--optional">任意</span>
                電話番号
                <span class="font-sizeS">（ハイフン不要）</span>
              </div>
              <input type="text" name="tel" class="form_input  <?php if(!empty($err_msg['name'])) echo 'err'; ?>" value="<?php echo getFormData('tel'); ?>" placeholder="000××××××××">
            </label>
            <div class="form_errMsg">
              <?php echo showErrMsg('tel'); ?>
            </div>
            <label>
              <div class="form_name">
                <span class="form_label form_label--required">必須</span>
                メールアドレス
              </div>
              <input type="text" name="email" class="form_input  <?php if(!empty($err_msg['email'])) echo 'err'; ?>" value="<?php echo getFormData('email'); ?>" placeholder="example@test.com">
            </label>
            <div class="form_errMsg">
              <?php echo showErrMsg('email'); ?>
            </div>
            <label>
              <div class="form_name">
                <span class="form_label form_label--optional">任意</span>
                年齢
              </div>
              <input type="number" min="10" max="100" name="age" class="form_input form_input--short <?php if(!empty($err_msg['age'])) echo 'err'; ?>" value="<?php echo getFormData('age'); ?>">
            </label>
            <div class="form_errMsg">
              <?php echo showErrMsg('age'); ?>
            </div>
            <label>
              <div class="form_name">
                <span class="form_label form_label--optional">任意</span>
                都道府県
              </div>
              <select name="prefecture_id" size="1" class="form_input  <?php if(!empty($err_msg['prefecture_id'])) echo 'err'; ?>">
                <option value="0" <?php if(empty($dbFormData['prefecture_id'])) echo 'selected';?>>選択してください</option>
              <?php foreach ($dbPrefData as $value): ?>
                <option value="<?php echo $value['id']; ?>" <?php if (getFormData('prefecture_id') == $value['id']) echo 'selected'; ?>>
                  <?php echo $value['name']; ?>
                </option>
              <?php endforeach; ?>
              </select>
            </label>
            <div class="form_errMsg">
              <?php echo showErrMsg('prefecture_id'); ?>
            </div>
            <label>
              <div class="form_name">
                <span class="form_label form_label--optional">任意</span>
                市区町村
              </div>
              <input type="text" name="email" class="form_input  <?php if(!empty($err_msg['city'])) echo 'err'; ?>" value="<?php echo getFormData('city'); ?>" placeholder="新宿区">
            </label>
            <div class="form_errMsg">
              <?php echo showErrMsg('city'); ?>
            </div>

            <button type="submit" class="btn btn--submit">更新する</button>

          </div>
        </form>
      </main>
      <?php require('sidebarRight.php'); ?>
    </div>
  </div>
</div>

<!--　共通フッター呼び出し　-->
<?php require('footer.php'); ?>
