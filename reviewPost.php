<?php
require('function.php');

//ログイン認証は不要

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
  debug('POST送信がありました。処理を開始します');
  debug('POST送信の中身：'.print_r($_POST,true));

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
  debug('$picの値：'.print_r($pic, true));

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
$css_title = basename(__FILE__,".php");
$p_title = 'クチコミ投稿' ;
//共通headタグ呼び出し
require('head.php');

//共通ヘッダー呼び出し
require('header.php');
?>

<!--　メインコンテンツ　-->
<div class="wrap">
  <main>
    <div class="h1-wide">
      <h1>クチコミ投稿</h1>
    </div>
    <div class="area-msg">
      <?php echo showErrMsg('common'); ?>
    </div>

    <div class="contents-inner">
      <form method="post" action= "" enctype="multipart/form-data">
        <section>
          <div class="h2_space2">
            <h2>施設のご利用状況</h2>
          </div>

          <div class="section-container">
            <div style="margin-bottom:45px;">
              <div class="input-label">
                施設名<span class="small_font">（変更不可）</span>
              </div>
              <div class="option-items">
                <?php echo $dbInstData['name']; ?>
              </div>
            </div>

            <label>
              <div class="input-label">
                <div class="label-required">必須</div>
                滞在時間
              </div>
              <select class="<?php if(!empty($err_msg['stay_id'])) echo 'err'; ?>" name="stay_id">
                <option value="" <?php if(empty(getFormData('stay_id'))) echo 'selected';?> >選択してください</option>
                <?php foreach ($dbStayData as $key => $st):?>
                <option value="<?php echo $st['id']; ?>" <?php if(getFormData('stay_id') === $st['id']) echo 'selected';?> ><?php echo $st['name']; ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <div class="area-msg">
              <?php echo showErrMsg('stay_id'); ?>
            </div>

            <div>
              <div class="input-label">
                <div class="label-required">必須</div>
                利用目的<span class="small_font">（複数選択可）</span>
              </div>
              <div class="option-items" style="<?php if(!empty($err_msg['purpose_id'])) echo 'background: #f7dcd9;'; ?>">
                <label style="display:inline;">
                  <input type="hidden" name="purpose_id[]" value="">
                </label>
                <?php foreach ($dbPurposeData as $key => $value):?>
                <label style="display:inline;">
                  <input type="checkbox" name="purpose_id[]" value="<?php echo $value['id']; ?>" <?php if(!empty($_POST)) echo ((in_array($value['id'],$purpose_id))?'checked':'');?> ><?php echo $value['name']; ?>
                </label>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="area-msg">
              <?php echo showErrMsg('purpose_id'); ?>
            </div>

          </div>
        </section>

        <section>
          <div class="h2_space2">
            <h2>利用後のご感想・ご評価<span class="small_font">（各項目1点~5点で採点してください）</span></h2>
          </div>

          <div class="section-container">
            <label>
              <div class="input-label">
                <div class="label-required">必須</div>
                コンセント設備へのご評価<span class="small_font">（設置席数・利用しやすさなど）</span>
              </div>
              <select class="<?php if(!empty($err_msg['concent_pt'])) echo 'err'; ?>" name="concent_pt">
                <option value="" <?php if(empty(getFormData('concent_pt'))) echo 'selected';?> >選択してください</option>
                <option value="1" <?php if(getFormData('concent_pt') == "1") echo 'selected';?> >1点（悪い）</option>
                <option value="2"<?php if(getFormData('concent_pt') == "2") echo 'selected';?> >2点（やや悪い）</option>
                <option value="3" <?php if(getFormData('concent_pt') == "3") echo 'selected';?> >3点（普通）</option>
                <option value="4" <?php if(getFormData('concent_pt') == "4") echo 'selected';?>>4点（やや良い）</option>
                <option value="5" <?php if(getFormData('concent_pt') == "5") echo 'selected';?>>5点（良い）</option>
              </select>
            </label>
            <div class="area-msg">
              <?php echo showErrMsg('concent_pt'); ?>
            </div>


            <label>
              <div class="input-label">
                <div class="label-required">必須</div>
                Wi-Fiへのご評価<span class="small_font">（電波の強さ・接続方法の分かりやすさなど）</span>
              </div>
              <select class="<?php if(!empty($err_msg['wifi_pt'])) echo 'err'; ?>" name="wifi_pt">
                <option value="" <?php if(empty(getFormData('wifi_pt'))) echo 'selected';?> >選択してください</option>
                <option value="1" <?php if(getFormData('wifi_pt') == "1") echo 'selected';?> >1点（悪い）</option>
                <option value="2"<?php if(getFormData('wifi_pt') == "2") echo 'selected';?> >2点（やや悪い）</option>
                <option value="3" <?php if(getFormData('wifi_pt') == "3") echo 'selected';?> >3点（普通）</option>
                <option value="4" <?php if(getFormData('wifi_pt') == "4") echo 'selected';?>>4点（やや良い）</option>
                <option value="5" <?php if(getFormData('wifi_pt') == "5") echo 'selected';?>>5点（良い）</option>
              </select>
            </label>
            <div class="area-msg">
              <?php echo showErrMsg('wifi_pt'); ?>
            </div>

            <label>
              <div class="input-label">
                <div class="label-required">必須</div>
                施設内の環境へのご評価<span class="small_font">（集中しやすさ・静かさ・混雑など）</span>
              </div>
              <select class="<?php if(!empty($err_msg['silence_pt'])) echo 'err'; ?>" name="silence_pt">
                <option value="" <?php if(empty(getFormData('silence_pt'))) echo 'selected';?> >選択してください</option>
                <option value="1" <?php if(getFormData('silence_pt') == "1") echo 'selected';?> >1点（悪い）</option>
                <option value="2"<?php if(getFormData('silence_pt') == "2") echo 'selected';?> >2点（やや悪い）</option>
                <option value="3" <?php if(getFormData('silence_pt') == "3") echo 'selected';?> >3点（普通）</option>
                <option value="4" <?php if(getFormData('silence_pt') == "4") echo 'selected';?>>4点（やや良い）</option>
                <option value="5" <?php if(getFormData('silence_pt') == "5") echo 'selected';?>>5点（良い）</option>
              </select>
            </label>
            <div class="area-msg">
              <?php echo showErrMsg('silence_pt'); ?>
            </div>

            <label>
              <div class="input-label">
                <div class="label-required">必須</div>
                総合評価<span class="small_font"></span>
              </div>
              <select class="<?php if(!empty($err_msg['total_pt'])) echo 'err'; ?>" name="total_pt">
                <option value="" <?php if(empty(getFormData('total_pt'))) echo 'selected';?> >選択してください</option>
                <option value="1" <?php if(getFormData('total_pt') == "1") echo 'selected';?> >1点（悪い）</option>
                <option value="2"<?php if(getFormData('total_pt') == "2") echo 'selected';?> >2点（やや悪い）</option>
                <option value="3" <?php if(getFormData('total_pt') == "3") echo 'selected';?> >3点（普通）</option>
                <option value="4" <?php if(getFormData('total_pt') == "4") echo 'selected';?>>4点（やや良い）</option>
                <option value="5" <?php if(getFormData('total_pt') == "5") echo 'selected';?>>5点（良い）</option>
              </select>
            </label>
            <div class="area-msg">
              <?php echo showErrMsg('total_pt'); ?>
            </div>
          </div>
        </section>

        <section>
          <div class="h2_space2">
            <h2>フリーコメント<span class="small_font">（勉強場所・作業場所としてのご感想や注意点をご記入ください）</span></h2>
          </div>

          <div class="section-container">
            <label>
              <div class="input-label">
                <div class="label-optional">任意</div>
                ひとこと感想
              </div>
              <input type="text" name="title" class="js-text-count1 <?php if(!empty($err_msg['title'])) echo 'err'; ?>" value="<?php echo getFormData('title'); ?>" placeholder="静かで設備も充実しており、作業にぴったりでした">
              <p class="text-counter"><span class="js-text-count-view1">0</span>/30文字</p>
            </label>
            <div class="area-msg">
              <?php echo showErrMsg('title'); ?>
            </div>

            <label>
              <div class="input-label">
                <div class="label-optional">任意</div>
                詳細なコメント・その他備考など
              </div>
              <textarea name="comment" class="js-text-count2 <?php if(!empty($err_msg['comment'])) echo 'err'; ?>" rows="8"><?php echo getFormData('comment'); ?></textarea>
              <p class="text-counter"><span class="js-text-count-view2">0</span>/200文字</p>
            </label>
            <div class="area-msg">
              <?php echo showErrMsg('comment'); ?>
            </div>

            <div class="pic-wrapper">

              <label class="dropPic-container">
                <div class="input-label">
                  <div class="label-optional">任意</div>
                  画像1
                </div>
                <div class="drop-area">ドラッグ&ドロップ
                  <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                  <input type="file" name="pic1" class="input-file">
                  <img src="<?php echo getFormData('pic1'); ?>" alt="" class="js-img-preview">
                </div>
              </label>
              <label class="dropPic-container">
                <div class="input-label">
                  <div class="label-optional">任意</div>
                  画像2
                </div>
                <div class="drop-area">ドラッグ&ドロップ
                  <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                  <input type="file" name="pic2" class="input-file">
                  <img src="<?php echo getFormData('pic2'); ?>" alt="" class="js-img-preview">
                </div>
              </label>
              <label class="dropPic-container">
                <div class="input-label">
                  <div class="label-optional">任意</div>
                  画像3
                </div>
                <div class="drop-area">ドラッグ&ドロップ
                  <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                  <input type="file" name="pic3" class="input-file">
                  <img src="<?php echo getFormData('pic3'); ?>" alt="" class="js-img-preview">
                </div>
              </label>
              <div class="area-msg">
                <?php echo showErrMsg('pic'); ?>
              </div>
            </div>

            <input type="submit" value="投稿する">
          </div>
        </section>

      </form>
    </div>
  </main>

</div>

<!--　共通フッター呼び出し　-->
<?php
require('footer.php');
 ?>
