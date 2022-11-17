<?php
require('function.php');

//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================
// GETパラメータを確認
$i_id = (!empty($_GET['i'])) ? $_GET['i'] :'';
// セッション変数からユーザーIDを確認
$u_id = (!empty($_SESSION['user_id'])) ? $_SESSION['user_id'] :'';
// 施設情報を取得
$dbInstData = getInstData($i_id);
// 利用目的データを取得
$dbPurposeData = getPurposeData();
// 滞在時間データを取得
$dbStayData = getStayData();

// 施設IDのGETがない、もしくは不正の場合は一覧ページへ遷移
if (!isset($i_id) || empty($dbInstData)) {
  debug('GET情報に誤りがあります');
  header('Location:searchList.php');
  exit;
}

//=========================================
// POST送信があったら処理スタート
if (!empty($_POST)) {
  debug('=============================================');
  debug('POST送信がありました。処理を開始します');

  // POSTの中身を変数に詰める
  $stay_id = $_POST['stay_id'];

  array_shift($_POST['purpose_id']);   // $purpose_idは配列の先頭に含まれている[0]を削除
  $purpose_id = ($_POST['purpose_id']) ? $_POST['purpose_id'] : '';
  $concent_pt = $_POST['concent_pt'];
  $wifi_pt = $_POST['wifi_pt'];
  $silence_pt = $_POST['silence_pt'];
  $total_pt = $_POST['total_pt'];
  $title = $_POST['title'];
  $comment = $_POST['comment'];

  debug('$_FILESの値：'.print_r($_FILES, true));
  if (!empty($_FILES['pic1']['name'])) $pic['pic1'] = uploadImg($_FILES['pic1'], 'pic1');
  if (!empty($_FILES['pic2']['name'])) $pic['pic2'] = uploadImg($_FILES['pic2'], 'pic2');
  if (!empty($_FILES['pic3']['name'])) $pic['pic3'] = uploadImg($_FILES['pic3'], 'pic3');

  // 新規登録用バリデーション実施
  validRequired($stay_id, 'stay_id');
  validRequired($purpose_id, 'purpose_id');
  validRequired($concent_pt, 'concent_pt');
  validRequired($wifi_pt, 'wifi_pt');
  validRequired($total_pt, 'total_pt');
  validRequired($silence_pt, 'silence_pt');

  if (empty($err_msg)) {
    debug('未入力チェックOK');
    // セレクトボックスチェック
    validSelect($stay_id, 'stay_id');
    validSelect($concent_pt, 'concent_pt');
    validSelect($wifi_pt, 'wifi_pt');
    validSelect($silence_pt, 'silence_pt');
    validSelect($total_pt, 'total_pt');
    // 半角数字チェック
    validIsArrayNum($purpose_id, 'purpose_id');
    // コメントチェック
    validMaxLen($title, 'title', 30);
    validMaxLen($comment, 'comment', 200);
  }

  if (empty($err_msg)) {
    debug('バリデーションOK');
    try {
      debug('DBに登録します');
      $dbh = dbConnect();
      $sql = 'INSERT INTO review (institution_id, stay_id, concent_pt, wifi_pt, silence_pt, total_pt, title, comment, user_id, create_date)
              VALUES (:i_id, :stay, :c_pt, :w_pt, :s_pt, :t_pt, :title, :comment, :u_id, :c_date)';

      $data = array(':i_id'=> $i_id, ':stay'=> $stay_id, ':c_pt'=> $concent_pt, ':w_pt'=> $wifi_pt, ':s_pt'=> $silence_pt, ':t_pt'=> $total_pt, ':title'=> $title, ':comment'=> $comment, ':u_id'=> $u_id, ':c_date'=> date('Y/m/d H:i:s'));
      $stmt = queryPost($dbh, $sql, $data);

      $r_id = $dbh->lastInsertID();

      // 画像データ格納
      if (!empty($pic)) {
        foreach ($pic as $key => $value) {
          $sql2 = 'INSERT INTO image_in_review (`path`, review_id, institution_id, create_date) VALUES (:p, :r_id, :i_id, :c_date)';
          $data2 = array(':p'=> $value, ':r_id'=> $r_id, ':i_id'=> $i_id, ':c_date'=> date('Y/m/d H:i:s'));
          $stmt2 = queryPost($dbh, $sql2, $data2);
        }
      }

      // 利用目的データ格納
      foreach ($purpose_id as $key => $p_id) {
        $sql3 = 'INSERT INTO purpose_in_review (review_id, purpose_id, institution_id, user_id) VALUES (:r_id, :p_id, :i_id, :u_id)';
        $data3 = array(':r_id'=> $r_id, ':p_id'=> $p_id, ':i_id'=>$i_id, ':u_id'=>$u_id);

        $stmt3 = queryPost($dbh, $sql3, $data3);
      }

      if ($stmt && $stmt2 && $stmt3) {
        debug('施設詳細ページに遷移します');
        $_SESSION['js-msg'] = JSMSG05;
        header('Location:searchDetail.php?i='.$i_id);
        exit;

      }else {
        debug('失敗したSQL：'.$sql);
        $err_msg['common'] = MSG08;
      }

    } catch (\Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG08;
    }
  }
}

?>

<?php
$p_title = 'クチコミ投稿' ;
//共通headタグ呼び出し
require('head.php');
//共通ヘッダー呼び出し
require('header.php');
?>

<!--　メインコンテンツ　-->

<main class="page-wrapper">

  <div class="container">
    <h1 class="container_title">クチコミ投稿</h1>
    <div class="container_body">
      <form method="post" enctype="multipart/form-data">

        <section class="section">
          <div class="module form">
            <h2 class="module_title module_title--surround">施設ご利用時の状況</h2>
            <div class="module_body">
              <div class="form_errMsg">
                <?php echo showErrMsg('common'); ?>
              </div>
              <label>
                <div class="form_name">
                  施設名
                  <span class="font-sizeS">（変更不可）</span>
                </div>
                <p class="form_input form_lastItem" style="line-height:35px;"><?php echo $dbInstData['name']; ?></p>
              </label>
              <label>
                <div class="form_name">
                  <span class="form_label form_label--required">必須</span>
                  滞在時間
                </div>
                <select class="form_input <?php if(!empty($err_msg['stay_id'])) echo 'err'; ?>" name="stay_id">
                  <option value="" <?php if(empty(getFormData('stay_id'))) echo 'selected';?> >選択してください</option>
                <?php foreach ($dbStayData as $key => $st):?>
                  <option value="<?php echo $st['id']; ?>" <?php if(getFormData('stay_id') === $st['id']) echo 'selected';?> ><?php echo $st['name']; ?></option>
                <?php endforeach; ?>
                </select>
              </label>
              <div class="form_errMsg">
                <?php echo showErrMsg('stay_id'); ?>
              </div>
              <div>
                <div class="form_name">
                  <span class="form_label form_label--required">必須</span>
                  利用目的
                  <span class="font-sizeS">（複数選択可）</span>
                </div>
                <div class="form_input form_input--checkbox <?php if(!empty($err_msg['purpose_id'])) echo 'err'; ?>">
                  <label>
                    <input type="hidden" name="purpose_id[]" value="">
                  </label>
                <?php foreach ($dbPurposeData as $key => $value):?>
                  <label class="marginItemLine">
                    <input type="checkbox" name="purpose_id[]" value="<?php echo $value['id']; ?>" <?php if($_POST) echo ($_POST['purpose_id']&&in_array($value['id'],$purpose_id))?'checked':'';?> >
                    <?php echo $value['name']; ?>
                  </label>
                <?php endforeach; ?>
                </div>
              </div>
              <div class="form_errMsg">
                <?php echo showErrMsg('purpose_id'); ?>
              </div>
            </div>
          </div>
        </section>

        <section class="section">
          <div class="module form">
            <h2 class="module_title module_title--surround">設備へのご評価</h2>
            <div class="module_body">
              <p class="form_notion" style="text-align:center;">作業場所に必要な設備について</p>
              <p class="form_lastItem" style="text-align:center;">1点~5点で評価してください</p>
              <label>
                <div class="form_name">
                  <span class="form_label form_label--required">必須</span>
                  コンセントの評価
                  <span class="font-sizeS">（設置席数など）</span>
                </div>
                <?php makeSelectTag('concent_pt'); ?>
              </label>
              <div class="form_errMsg">
                <?php echo showErrMsg('concent_pt'); ?>
              </div>
              <label>
                <div class="form_name">
                  <div class="form_label form_label--required">必須</div>
                  Wi-Fiの評価
                  <span class="font-sizeS">（電波の強さなど）</span>
                </div>
                <?php makeSelectTag('wifi_pt'); ?>
              </label>
              <div class="form_errMsg">
                <?php echo showErrMsg('wifi_pt'); ?>
              </div>
              <label>
                <div class="form_name">
                  <div class="form_label form_label--required">必須</div>
                  雰囲気の評価
                  <span class="font-sizeS">（静かさ、混雑など）</span>
                </div>
                <?php makeSelectTag('silence_pt'); ?>
              </label>
              <div class="form_errMsg">
                <?php echo showErrMsg('silence_pt'); ?>
              </div>
              <label>
                <div class="form_name">
                  <div class="form_label form_label--required">必須</div>
                  総合評価
                </div>
                <?php makeSelectTag('total_pt'); ?>
              </label>
              <div class="form_errMsg">
                <?php echo showErrMsg('total_pt'); ?>
              </div>
            </div>
          </div>
        </section>

        <section class="section">
          <div class="module form">
            <h2 class="module_title module_title--surround">フリーコメント</h2>
            <div class="module_body">

              <p class="form_notion" style="text-align:center;">勉強場所・作業場所としての</p>
              <p class="form_lastItem" style="text-align:center;">ご感想や注意点をご記入ください</p>
              <label>
                <div class="form_name">
                  <span class="form_label form_label--optional">任意</span>
                  ひとこと感想
                </div>
                <input type="text" name="title" class="form_input js-text-count1 <?php if(!empty($err_msg['title'])) echo 'err'; ?>" value="<?php echo getFormData('title'); ?>" placeholder="静かで設備も充実しており、作業にぴったり">
              </label>
              <div class="text-counter">
                <div class="form_errMsg">
                  <?php echo showErrMsg('title'); ?>
                </div>
                <p><span class="js-text-count-view1">0</span>/30文字</p>
              </div>

              <label>
                <div class="form_name">
                  <span class="form_label form_label--optional">任意</span>
                  詳細なコメント・その他備考など
                </div>
                <textarea name="comment" class="form_input js-text-count2 <?php if(!empty($err_msg['comment'])) echo 'err'; ?>" rows="8"><?php echo getFormData('comment'); ?></textarea>
              </label>
              <div class="text-counter">
                <div class="form_errMsg">
                  <?php echo showErrMsg('comment'); ?>
                </div>
                <p><span class="js-text-count-view2">0</span>/200文字</p>
              </div>

              <div class="dropPic form_lastItem">
                <label class="dropPic_container">
                  <div class="form_name">
                    <span class="form_label form_label--optional">任意</span>
                    画像1
                  </div>
                  <div class="dropPic_area">ドラッグ&ドロップ
                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                    <input type="file" name="pic1" class="dropPic_inputFile">
                    <img src="<?php echo getFormData('pic1'); ?>" alt="" class="js-img-preview">
                  </div>
                </label>
                <label class="dropPic_container">
                  <div class="form_name">
                    <span class="form_label form_label--optional">任意</span>
                    画像2
                  </div>
                  <div class="dropPic_area">ドラッグ&ドロップ
                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                    <input type="file" name="pic2" class="dropPic_inputFile">
                    <img src="<?php echo getFormData('pic2'); ?>" alt="" class="js-img-preview">
                  </div>
                </label>
                <label class="dropPic_container">
                  <div class="form_name">
                    <span class="form_label form_label--optional">任意</span>
                    画像3
                  </div>
                  <div class="dropPic_area">ドラッグ&ドロップ
                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                    <input type="file" name="pic3" class="dropPic_inputFile">
                    <img src="<?php echo getFormData('pic3'); ?>" alt="" class="js-img-preview">
                  </div>
                </label>
                <div class="form_errMsg">
                  <?php echo showErrMsg('pic1'); ?>
                  <?php echo showErrMsg('pic2'); ?>
                  <?php echo showErrMsg('pic3'); ?>
                </div>
              </div>

              <input type="submit" class="btn btn--submit btn--submit--mainContents" value="投稿する">
            </div>
          </div>

        </section>
      </form>
    </div>
  </div>
</main>


<!--　共通フッター呼び出し　-->
<?php require('footer.php'); ?>
