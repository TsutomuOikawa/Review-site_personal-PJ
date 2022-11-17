<?php
require('function.php');

//ログイン認証
require('auth.php');
//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================
// GET関数を確認
$i_id = (!empty($_GET['i_id'])) ? $_GET['i_id'] : '';
$dbFormData = getInstData($i_id);
$dbPrefData = getPrefData();
$dbTypeData = getTypeData();
// GETの有無で編集か新規登録かを判断
$edit_flg = (!empty($i_id)) ? true : false;

// POST送信があったら処理スタート
if (!empty($_POST)) {
  debug('=============================================');
  debug('POST送信がありました。処理を開始します');

  // POSTの中身を変数に詰める
  $name = $_POST['name'];
  $prefecture = $_POST['prefecture_id'];
  $city = $_POST['city'];
  $address = $_POST['address'];
  $type = $_POST['type_id'];
  $access = $_POST['access'];
  $hours = $_POST['hours'];
  $holidays = $_POST['holidays'];
  $concent = (!empty($_POST['concent'])) ? 1 : 0;
  $wifi = (!empty($_POST['wifi'])) ? 1 : 0;
  $homepage = $_POST['homepage'];

  // 新規登録か更新かによってバリデーションチェックを分割
  if ($edit_flg){
    // POSTがDBデータと異なる場合にバリデーション
    if ($name !== $dbFormData['name']) {
      validMaxLen($name, 'name', 255);
      validRequired($name, 'name');
    }
    if ($prefecture !== $dbFormData['prefecture_id']) {
      validSelect($prefecture, 'prefecture_id');
    }
    if ($city !== $dbFormData['city']) {
      validMaxLen($city, 'city', 255);
      validCity($city, 'city');
      validRequired($city, 'city');
    }
    if ($address !== $dbFormData['address']) {
      validMaxLen($address, 'address', 255);
    }
    if ($type !== $dbFormData['type_id']) {
      validSelect($type, 'type_id');
    }
    if ($access !== $dbFormData['access']) {
      validMaxLen($access, 'access', 255);
    }
    if ($hours !== $dbFormData['hours']) {
      validMaxLen($hours, 'hours', 255);
    }
    if ($homepage !== $dbFormData['homepage']) {
      validMaxLen($homepage, 'homepage', 255);
      validURL($homepage, 'homepage');
    }

  }else{
    validRequired($name, 'name');
    validSelect($prefecture, 'prefecture_id');
    validRequired($city, 'city');
    validSelect($type, 'type_id');

    if (empty($err_msg)) {
      debug('未入力チェックOK');
      // nameチェック
      validMaxLen($name, 'name', 255);
      // cityチェック
      validMaxLen($city, 'city', 255);
      validCity($city, 'city');
      // addressチェック
      validMaxLen($address, 'address', 255);
      // accessチェック
      validMaxLen($access, 'access', 255);
      // hoursチェック
      validMaxLen($hours, 'hours', 255);
      // homepageチェック
      validMaxLen($homepage, 'homepage', 255);
      validURL($homepage, 'homepage');
    }
  }

  if (empty($err_msg)) {
    debug('バリデーションチェックOKです');

    try {
      $dbh = dbConnect();
      // $edit_flg の値によって、SQL文を調整
      if ($edit_flg) {
        debug('DBを更新します');
        $sql = 'UPDATE institution SET name = :name, prefecture_id = :pref, city = :city, address = :add, type_id = :type, access = :access, hours = :hours, holidays = :holidays, concent = :concent, wifi = :wifi, homepage = :homepage, user_id = :u_id WHERE id = :i_id';
        $data = array(':name'=> $name, ':pref'=> $prefecture, ':city'=> $city, ':add'=> $address, ':type'=> $type, ':access'=> $access, ':hours'=> $hours,
        ':holidays'=> $holidays, ':concent'=> $concent, ':wifi'=> $wifi, ':homepage'=> $homepage, ':u_id'=> $_SESSION['user_id'], ':i_id'=> $i_id);
      }else {
        debug('DBに新規登録します');
        $sql = 'INSERT INTO institution (name, prefecture_id, city, address, type_id, access, hours, holidays, concent, wifi, homepage, user_id, create_date) VALUES(:name, :pref, :city, :add, :type, :access, :hours, :holidays, :concent, :wifi, :homepage, :u_id, :c_date)';
        $data = array(':name'=> $name, ':pref'=> $prefecture, ':city'=> $city, ':add'=> $address, ':type'=> $type, ':access'=> $access, ':hours'=> $hours,
        ':holidays'=> $holidays, ':concent'=> $concent, ':wifi'=> $wifi, ':homepage'=> $homepage, ':u_id'=> $_SESSION['user_id'], ':c_date' => date('Y-m-d H:i:s'));
      }
      // SQL実行
      $stmt = queryPost($dbh, $sql, $data);

      if ($stmt) {
        $_SESSION['js-msg'] = JSMSG03;
        debug('マイページへ遷移します');
        header('Location:mypage.php');

      }else {
        $err_msg['common'] = MSG08;
      }
    } catch (\Exception $e) {
      error_log('エラー発生：'. $e->getMessage());
      $err_msg['common'] = MSG08;
    }
  }
}


 ?>

<?php
$p_title = ($edit_flg) ? '施設情報編集' : '施設新規登録';
//共通headタグ呼び出し
require('head.php');
//共通ヘッダー呼び出し
require('header.php');
?>

<!--　メインコンテンツ　-->
<div class="page-wrapper">

  <div class="container">
    <h1 class="container_title"><?php echo ($edit_flg) ? '施設情報編集' : '施設新規登録'; ?></h1>
    <div class="container_body container_body--divide">
      <main class="container_mainBody">
        <form method="post">

          <section class="section">
            <div class="module form form--wide">
              <h2 class="module_title module_title--surround">基本情報</h2>
              <div class="module_body">
                <div class="form_errMsg">
                  <?php echo showErrMsg('common'); ?>
                </div>
                <label>
                  <div class="form_name">
                    <span class="form_label form_label--required">必須</span>
                    施設名
                  </div>
                  <input type="text" name="name" class="form_input  <?php if(!empty($err_msg['name'])) echo 'err'; ?>" value="<?php echo getFormData('name'); ?>" placeholder="コントレcafe">
                </label>
                <div class="form_errMsg">
                  <?php echo showErrMsg('name'); ?>
                </div>

                <label>
                  <div class="form_name">
                    <span class="form_label form_label--required">必須</span>
                    都道府県
                  </div>
                  <select name="prefecture_id" class="form_input  <?php if(!empty($err_msg['prefecture_id'])) echo 'err'; ?>">
                    <option value="0" <?php if(empty(getFormData('prefecture_id'))) echo 'selected';?> >選択してください</option>
                  <?php foreach ($dbPrefData as $key => $value): ?>
                    <option value="<?php echo $value['id']; ?>" <?php if (getFormData('prefecture_id') == $value['id']) echo 'selected'; ?>><?php echo $value['name']; ?></option>
                  <?php endforeach; ?>
                  </select>
                </label>
                <div class="form_errMsg">
                  <?php echo showErrMsg('prefecture_id'); ?>
                </div>

                <label>
                  <div class="form_name">
                    <span class="form_label form_label--required">必須</span>
                    市区町村
                    <span class="font-sizeS">（「市区町村」まで必須）</span>
                  </div>
                  <input type="text" name="city" class="form_input  <?php if(!empty($err_msg['city'])) echo 'err'; ?>" value="<?php echo getFormData('city'); ?>" placeholder="中央区">
                </label>
                <div class="form_errMsg">
                  <?php echo showErrMsg('city'); ?>
                </div>

                <label>
                  <div class="form_name">
                    <span class="form_label form_label--optional">任意</span>
                    その他住所
                  </div>
                  <input type="text" name="address" class="form_input  <?php if(!empty($err_msg['address'])) echo 'err'; ?>" value="<?php echo getFormData('address'); ?>" placeholder="日本橋1-1-1">
                </label>
                <div class="form_errMsg">
                  <?php echo showErrMsg('address'); ?>
                </div>

                <label>
                  <div class="form_name">
                    <span class="form_label form_label--required">必須</span>
                    施設カテゴリー
                  </div>
                  <select name="type_id" class="form_input  <?php if(!empty($err_msg['type_id'])) echo 'err'; ?>">
                    <option value="0" <?php if(empty(getFormData('prefecture_id'))) echo 'selected';?> >選択してください</option>
                  <?php foreach ($dbTypeData as $key => $val): ?>
                    <option value="<?php echo $val['id']; ?>" <?php if (getFormData('type_id') == $val['id']) echo 'selected'; ?>><?php echo $val['name']; ?></option>
                  <?php endforeach; ?>
                  </select>
                </label>
                <div class="form_errMsg">
                  <?php echo showErrMsg('type_id'); ?>
                </div>
              </div>
            </div>
          </section>

          <section class="section">
            <div class="module form form--wide">
              <h2 class="module_title module_title--surround">詳細情報</h2>
              <div class="module_body">
                <p class="form_notion form_lastItem" style="text-align:center;">※正確な情報をご入力ください</p>

                <label>
                  <div class="form_name">
                    <span class="form_label form_label--optional">任意</span>
                    アクセス
                  </div>
                  <input type="text" name="access" class="form_input  <?php if(!empty($err_msg['access'])) echo 'err'; ?>" value="<?php echo getFormData('access'); ?>" placeholder="JR東京駅八重洲口徒歩10分">
                </label>
                <div class="form_errMsg">
                  <?php echo showErrMsg('access'); ?>
                </div>

                <label>
                  <div class="form_name">
                    <span class="form_label form_label--optional">任意</span>
                    営業時間
                  </div>
                  <input type="text" name="hours" class="form_input  <?php if(!empty($err_msg['hours'])) echo 'err'; ?>" value="<?php echo getFormData('hours'); ?>" placeholder="平日11:00~19:00／休日10:00~20:00">
                </label>
                <div class="form_errMsg">
                  <?php echo showErrMsg('hours'); ?>
                </div>

                <label>
                  <div class="form_name">
                    <span class="form_label form_label--optional">任意</span>
                    定休日
                  </div>
                  <input type="text" name="holidays" class="form_input  <?php if(!empty($err_msg['holidays'])) echo 'err'; ?>" value="<?php echo getFormData('holidays'); ?>" placeholder="毎週水曜日／祝日">
                </label>
                <div class="form_errMsg">
                  <?php echo showErrMsg('holidays'); ?>
                </div>

                <div>
                  <div class="form_name">
                    <span class="form_label form_label--optional">任意</span>
                    コンセント・Wi-Fiの設置
                  </div>
                  <div class="form_input form_input--checkbox <?php if(!empty($err_msg['concent'])|| !empty($err_msg['wifi'])) echo 'err'; ?>">
                    <label class="marginItemLine">
                      <input type="checkbox" name="concent" value="1" <?php if(getFormData('concent') == (1 || on)) echo 'checked';?>>
                      コンセントあり
                    </label>
                    <label class="marginItemLine">
                      <input type="checkbox" name="wifi" value="1" <?php if(getFormData('wifi') == (1 || on)) echo 'checked';?>>
                      Wi-Fiあり
                    </label>
                  </div>
                </div>
                <div class="form_errMsg">
                  <?php echo showErrMsg('concent'); ?>
                  <?php echo showErrMsg('wifi'); ?>
                </div>

                <label>
                  <div class="form_name">
                    <span class="form_label form_label--optional">任意</span>
                    ホームページ
                  </div>
                  <input type="text" name="homepage" class="form_input  <?php if(!empty($err_msg['homepage'])) echo 'err'; ?>" value="<?php echo getFormData('homepage'); ?>" placeholder="https://wwww">
                </label>
                <div class="form_errMsg">
                  <?php echo showErrMsg('homepage'); ?>
                </div>

                <button type="submit" class="btn btn--submit"><?php echo ($edit_flg) ? '更新する':'登録する'; ?></button>

              </div>
            </div>
          </section>
        </form>
      </main>

      <?php require('sidebarRight.php'); ?>
    </div>
  </div>
</div>

<!--　共通フッター呼び出し　-->
<?php require('footer.php'); ?>
