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
debug('中身：'.print_r($dbTypeData,true));
// GETの有無で編集か新規登録かを判断
$edit_flg = (!empty($i_id)) ? true : false;

// POST送信があったら処理スタート
if (!empty($_POST)) {
  debug('POST送信がありました。処理を開始します');
  debug('POST送信の中身：'.print_r($_POST,true));

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
  if(!empty($dbFormData)){
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
$css_title = basename(__FILE__,".php");
$p_title = (!empty($_GET['i_id'])) ? '施設情報編集' : '施設新規登録';
//共通headタグ呼び出し
require('head.php');

//共通ヘッダー呼び出し
require('header.php');
?>

<!--　メインコンテンツ　-->
<div class="wrap">
  <main>
    <div class="h1-wide">
      <h1><?php echo (!empty($i_id)) ? '施設情報編集':'施設新規登録'; ?></h1>
    </div>
    <div class="mypage-inner">
      <form method="post">
        <section>
          <div class="h2_space">
            <h2>基本情報入力</h2>
          </div>
          <div class="<?php if(!empty($err_msg['common'])) echo 'err'; ?>">
            <span><?php echo showErrMsg('common'); ?></span>
          </div>
          <div class="for-space">
            <div class ="regi-user">
              <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
                <div class="label-required">必須</div>施設名
                <span><?php echo showErrMsg('name'); ?></span>
                <input type="text" name="name" placeholder="コントレcafe" value="<?php echo getFormData('name'); ?>">
              </label>
              <label class="<?php if(!empty($err_msg['prefecture_id'])) echo 'err'; ?>">
                <div class="label-required">必須</div>都道府県
                <span><?php echo showErrMsg('prefecture_id'); ?></span>

                <select class="" name="prefecture_id">
                  <option value="0" <?php if(empty(getFormData('prefecture_id'))) echo 'selected';?> >選択してください</option>
                  <?php foreach ($dbPrefData as $key => $value) { ?>
                  <option value="<?php echo $value['id']; ?>" <?php if (getFormData('prefecture_id') == $value['id']) echo 'selected'; ?>>
                    <?php echo $value['name']; ?>
                  </option><?php } ?>
                </select>

              </label>
              <label class="<?php if(!empty($err_msg['city'])) echo 'err'; ?>">
                <div class="label-required">必須</div>市区町村
                <span><?php echo showErrMsg('city'); ?></span>
                <input type="text" name="city" placeholder="中央区" value="<?php echo getFormData('city'); ?>">
              </label>
              <label class="<?php if(!empty($err_msg['address'])) echo 'err'; ?>">
                <div class="label-optional">任意</div>番地
                <span><?php echo showErrMsg('address'); ?></span>
                <input type="text" name="address" placeholder="日本橋1-1-1" value="<?php echo getFormData('address'); ?>">
              </label>
              <label class="<?php if(!empty($err_msg['type_id'])) echo 'err'; ?>">
                <div class="label-required">必須</div>施設タイプ
                <span><?php echo showErrMsg('type_id'); ?></span>

                <select class="" name="type_id">
                  <option value="0" <?php if(empty(getFormData('type_id'))) echo 'selected';?> >選択してください</option>
                  <?php foreach ($dbTypeData as $key => $val) {?>
                  <option value="<?php echo $val['id']; ?>" <?php if (getFormData('type_id') == $val['id']) echo 'selected'; ?>>
                    <?php echo $val['name']; ?>
                  </option><?php } ?>
                </select>

              </label>
            </div>
          </div>
        </section>
        <section>
          <div class="h2_space">
            <h2>詳細情報入力</h2>
          </div>
          <div class="for-space">
            <div class ="regi-user">
              <label class="<?php if(!empty($err_msg['access'])) echo 'err'; ?>">
                <div class="label-optional">任意</div>アクセス
                <span><?php echo showErrMsg('access'); ?></span>
                <input type="text" name="access" placeholder="JR東京駅八重洲口徒歩10分" value="<?php echo getFormData('access'); ?>">
              </label>
              <label class="<?php if(!empty($err_msg['hours'])) echo 'err'; ?>">
                <div class="label-optional">任意</div>営業時間
                <span><?php echo showErrMsg('hours'); ?></span>
                <input type="text" name="hours" placeholder="平日11:00~19:00／休日10:00~20:00" value="<?php echo getFormData('hours'); ?>">
              </label>
              <label class="<?php if(!empty($err_msg['holidays'])) echo 'err'; ?>">
                <div class="label-optional">任意</div>定休日
                <span><?php echo showErrMsg('holidays'); ?></span>
                <input type="text" name="holidays" placeholder="毎週水曜日" value="<?php echo getFormData('holidays'); ?>">
              </label>
              <div style="display:flex; margin-bottom:50px;">
                <label class="<?php if(!empty($err_msg['concent'])) echo 'err'; ?>">
                  <div class="label-optional">任意</div>コンセントあり
                  <span><?php  echo showErrMsg('concent'); ?></span>
                  <input type="checkbox" name="concent" value="">
                </label>
                <label style="margin-left:20px;" class="<?php if(!empty($err_msg['wifi'])) echo 'err'; ?>">
                  Wi-Fiあり
                  <span><?php  echo showErrMsg('wifi'); ?></span>
                  <input type="checkbox" name="wifi" value="">
                </label>
              </div>
              <label class="<?php if(!empty($err_msg['homepage'])) echo 'err'; ?>">
                <div class="label-optional">任意</div>ホームページ
                <span><?php  echo showErrMsg('homepage'); ?></span>
                <input type="text" name="homepage" placeholder="https://wwww" value="<?php echo getFormData('homepage'); ?>">
              </label>
              <input type="submit" value="<?php echo (!empty($i_id)) ? '更新する':'登録する'; ?>">
            </div>
          </div>
        </section>
      </form>
    </div>
  </main>

  <?php
  require('sidebarRight.php');
   ?>
</div>

<!--　共通フッター呼び出し　-->
<?php
require('footer.php');
 ?>
