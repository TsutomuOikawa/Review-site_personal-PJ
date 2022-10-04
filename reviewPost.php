<?php
require('function.php');

//ログイン認証は不要

//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================
// GETパラメータを確認
$i_id = (!empty($_GET['i'])) ? $_GET['i'] :'';
$r_id = (!empty($_GET['r'])) ? $_GET['r'] :'';
// セッション変数からユーザーIDを確認
$u_id = (!empty($_SESSION['user_id'])) ? $_SESSION['user_id'] :'';
// 施設情報を取得
$dbInstData = getInstData($i_id);
// クチコミ情報(フォーム情報)を取得
$dbFormData = getReviewData($r_id);
// 利用目的データを取得
$dbPurposeData = getPurposeData();
// クチコミGETの有無で編集か新規投稿かを判別
$edit_flg = (!empty($r_id)) ? true :false;

// 施設IDのGETがない、もしくは不正の場合は一覧ページへ遷移
if (!isset($i_id) || empty($dbInstData)) {
  debug('GET情報に誤りがあります');
  header('Location:searchList.php');
  exit;
}

// POST送信があったら処理スタート
if (!empty($_POST)) {
  debug('POST送信がありました。処理を開始します');
  debug('POST送信の中身：'.print_r($_POST,true));

  // POSTの中身を変数に詰める
  $stay = $_POST['stay'];
  $purpose_id = (!empty($_POST['purpose_id']))?implode(',', $_POST['purpose_id']):'';
  debug($purpose_id);
  $concent_pt = $_POST['concent_pt'];
  $wifi_pt = $_POST['wifi_pt'];
  $silence_pt = $_POST['silence_pt'];
  $total_pt = $_POST['total_pt'];
  $title = $_POST['title'];
  $comment = $_POST['comment'];

  if (!$edit_flg) {
    // 新規登録用バリデーション実施
    validRequired($stay, 'stay');
    validRequired($purpose_id, 'purpose_id');
    validRequired($concent_pt, 'concent_pt');
    validRequired($wifi_pt, 'wifi_pt');
    validRequired($total_pt, 'total_pt');
    validRequired($silence_pt, 'silence_pt');

    if (empty($err_msg)) {
      debug('未入力チェックOK');
      // 最大文字数チェック
      validMaxLen($stay, 'stay', 7);
      // セレクトボックスチェック
      validSelect($concent_pt, 'concent_pt');
      validSelect($wifi_pt, 'wifi_pt');
      validSelect($silence_pt, 'silence_pt');
      validSelect($total_pt, 'total_pt');

      // 半角数字チェック
      validPurpose($purpose_id, 'purpose_id');
      // コメントチェック
      validMaxLen($title, 'title', 30);
      validMaxLen($comment, 'comment', 200);
    }

  }else {
    // delete_flgがtrueのときはデータが変わったもののみバリデーション
    if ($stay !== (int)$dbFormData['stay'] ) {
      validMaxLen($stay, 'stay', 7);
      validRequired($stay, 'stay');
    }
    if ($purpose_id !== (int)$dbFormData['purpose_id']) {
      validNum($purpose_id, 'purpose_id');
      validRequired($purpose_id, 'purpose_id');
    }
    if ($concent_pt !== (int)$dbFormData['concent_pt']) {
      validSelect($concent_pt, 'concent_pt');
      validRequired($concent_pt, 'concent_pt');
    }
    if ($wifi_pt !== (int)$dbFormData['wifi_pt']) {
      validSelect($wifi_pt, 'wifi_pt');
      validRequired($wifi_pt, 'wifi_pt');
    }
    if ($silence_pt !== (int)$dbFormData['silence_pt']) {
      validSelect($silence_pt, 'silence_pt');
      validRequired($silence_pt, 'silence_pt');
    }
    if ($total_pt !== (int)$dbFormData['total_pt']) {
      validSelect($total_pt, 'total_pt');
      validRequired($total_pt, 'total_pt');
    }
    if ($title !== $dbFormData['title']) {
      validMaxLen($title, 'title', 30);
    }
    if ($comment !== $dbFormData['comment']) {
      validMaxLen($comment, 'comment', 200);
    }
  }

  if (empty($err_msg)) {
    debug('バリデーションOK');

    try {
      $dbh = dbConnect();
      // 新規登録か更新かでSQLを分ける
      if (!$edit_flg) {
        debug('DBに新規登録します');
        $sql = 'INSERT INTO review (institution_id, stay, purpose_id, concent_pt, wifi_pt, silence_pt, total_pt, title, comment, user_id, create_date)
                VALUES (:i_id, :stay, :p_id, :c_pt, :w_pt, :s_pt, :t_pt, :title, :comment, :u_id, :c_date)';
        $data = array(':i_id'=> $i_id, ':stay'=> $stay, ':p_id'=> $purpose_id, ':c_pt'=> $concent_pt, ':w_pt'=> $wifi_pt, ':s_pt'=> $silence_pt, ':t_pt'=> $total_pt, ':title'=> $title, ':comment'=> $comment, ':u_id'=> $u_id, ':c_date'=> date('Y/m/d H:i:s'));
      }else {
        debug('DBを更新します');
        $sql = 'UPDATE review SET stay = :stay, purpose_id = :p_id, concent_pt = :c_pt, wifi_pt= :w_pt, silence_pt = :s_pt, total_pt = :t_pt, title = :title, comment = :comment WHERE institution_id = :i_id AND user_id = :u_id AND delete_flg = 0';
        $data = array(':i_id'=> $i_id, ':stay'=> $stay, ':p_id'=> $purpose_id, ':c_pt'=> $concent_pt, ':w_pt'=> $wifi_pt, ':s_pt'=> $silence_pt, ':t_pt'=> $total_pt, ':title'=> $title, ':comment'=> $comment, ':u_id'=> $u_id);
      }
      $stmt = queryPost($dbh, $sql, $data);

      if ($stmt) {
        debug('施設詳細ページに遷移します');

        if (!$edit_flg) {
          $_SESSION['js-msg'] = JSMSG05;
        }else {
          $_SESSION['js-msg'] = JSMSG06;
        }
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
$p_title = ($edit_flg) ? 'クチコミ編集' : 'クチコミ投稿' ;
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
      <form method="post">
        <section>
          <div class="h2_space2">
            <h2>施設のご利用状況</h2>
          </div>

          <div class="section-container">
            <div style="margin-bottom:45px;">施設名<span class="small_font">（変更不可）</span>
              <div class="option-items">
                <?php echo $dbInstData['name']; ?>
              </div>
            </div>

            <label>
              <div class="label-required">必須</div>滞在時間
              <select class="<?php if(!empty($err_msg['stay'])) echo 'err'; ?>" name="stay">
                <option value="" <?php if(empty(getFormData('stay'))) echo 'selected';?> >選択してください</option>
                <option value="30分以下" <?php if(getFormData('stay') === "30分以下") echo 'selected';?> >30分以下</option>
                <option value="30分~1時間"<?php if(getFormData('stay') === "30分~1時間") echo 'selected';?> >30分~1時間</option>
                <option value="1~2時間" <?php if(getFormData('stay') === "1~2時間") echo 'selected';?> >1~2時間</option>
                <option value="2~3時間" <?php if(getFormData('stay') === "2~3時間") echo 'selected';?>>2~3時間</option>
                <option value="3~5時間" <?php if(getFormData('stay') === "3~5時間") echo 'selected';?>>3~5時間</option>
                <option value="5時間以上" <?php if(getFormData('stay') === "5時間以上") echo 'selected';?>>5時間以上</option>
                <option value="1日中" <?php if(getFormData('stay') === "1日中") echo 'selected';?>>1日中</option>
              </select>
            </label>
            <div class="area-msg">
              <?php echo showErrMsg('stay'); ?>
            </div>

            <div>
              <div class="label-required">必須</div>利用目的<span class="small_font">（複数選択可）</span>
              <div class="option-items" style="<?php if(!empty($err_msg['purpose_id'])) echo 'background: #f7dcd9;'; ?>">
                <label style="display:inline;">
                  <input type="hidden" name="purpose_id[]" value="">
                </label>
                <?php foreach ($dbPurposeData as $key => $value):?>
                <label style="display:inline;">
                  <input type="checkbox" name="purpose_id[]" value="<?php echo $value['id']; ?>" <?php echo ((empty($_POST) || (!empty($_POST)&&!strpos($purpose_id,$value['id'])))?'':'checked');?> ><?php echo $value['name']; ?>
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
              <div class="label-required">必須</div>コンセント設備へのご評価<span class="small_font">（設置席数・利用しやすさなど）</span>
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
              <div class="label-required">必須</div>Wi-Fiへのご評価<span class="small_font">（電波の強さ・接続方法の分かりやすさなど）</span>
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
              <div class="label-required">必須</div>施設内の環境へのご評価<span class="small_font">（集中しやすさ・静かさ・混雑など）</span>
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
              <div class="label-required">必須</div>総合評価<span class="small_font"></span>
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
            <h2>フリーコメント</h2>
          </div>

          <div class="section-container">
            <label>
              <div class="label-optional">任意</div>ひとこと感想
              <input type="text" name="title" class="js-text-count1 <?php if(!empty($err_msg['title'])) echo 'err'; ?>" value="<?php echo getFormData('title'); ?>" placeholder="静かで設備も充実しており、作業にぴったりでした">
              <p class="text-counter"><span class="js-text-count-view1">0</span>/30文字</p>
            </label>
            <div class="area-msg">
              <?php echo showErrMsg('title'); ?>
            </div>

            <label>
              <div class="label-optional">任意</div>詳細なコメント・その他備考など
              <textarea name="comment" class="js-text-count2 <?php if(!empty($err_msg['comment'])) echo 'err'; ?>" rows="8"><?php echo getFormData('comment'); ?></textarea>
              <p class="text-counter"><span class="js-text-count-view2">0</span>/200文字</p>
            </label>
            <div class="area-msg">
              <?php echo showErrMsg('comment'); ?>
            </div>

            <input type="submit" value="<?php echo (($edit_flg)? '編集する':'投稿する'); ?>">
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
