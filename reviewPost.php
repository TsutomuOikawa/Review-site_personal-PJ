<?php
require('function.php');

//ログイン認証は不要

//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================
// GETパラメータを確認
$i_id = (!empty($_GET['i_id'])) ? $_GET['i_id'] : '';
$r_id = (!empty($_GET['i_id'])) ? $_GET['i_id'] : '';
// 施設情報を取得
$dbInstData = getInstData($i_id);
// クチコミ情報(フォーム情報)を取得
$dbFormData = getReviewData($r_id);
// クチコミGETの有無で編集か新規投稿かを判別
$edit_flg = (!empty($r_id)) ? true :false;


// POST送信があったら処理スタート
if (!empty($_POST)) {
  debug('POST送信がありました。処理を開始します');
  debug('POST送信の中身：'.print_r($_POST,true));

  // POSTの中身を変数に詰める
  $stay = $_POST['stay'];
  $purpose_id = $_POST['purpose_id'];
  $concent_pt = $_POST['concent_pt'];
  $wifi_pt = $_POST['wifi_pt'];
  $silence_pt = $_POST['silence_pt'];
  $title = $_POST['title'];
  $comment = $_POST['comment'];

  if (!$delete_flg) {
    // 新規登録用バリデーション実施
    validRequired($stay, 'stay');
    validRequired($purpose_id, 'purpose_id');
    validRequired($concent_pt, 'concent_pt');
    validRequired($wifi_pt, 'wifi_pt');
    validRequired($silence_pt. 'silence_pt');

    if (empty($err_msg)) {
      debug('未入力チェックOK');
      // セレクトボックスチェック
      validSelect($stay, 'stay');
      validSelect($concent_pt, 'concent_pt', 5);
      validSelect($wifi_pt, 'wifi_pt', 5);
      validSelect($silence_pt. 'silence_pt', 5);
      // コメントチェック
      validMaxLen($title, 'title', 30);
      validMaxLen($comment, 'comment', 200);
    }

  }else {
    // delete_flgがtrueのときはデータが変わったもののみバリデーション
    
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
    <div class="review-inner">
      <form method="post">
        <section>
          <div class="h2_space">
            <h2>施設のご利用状況</h2>
          </div>

          <div class="<?php if(!empty($err_msg['common'])) echo 'err'; ?>">
            <span><?php echo showErrMsg('common'); ?></span>
          </div>

          <div class="for-space">
            <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
              <div class="label-required">必須</div>施設名
              <span><?php echo showErrMsg('name'); ?></span>
              <input type="text" name="name" value="GET値で施設名は固定" readonly>
            </label>

            <label class="<?php if(!empty($err_msg['stay'])) echo 'err'; ?>">
              <div class="label-required">必須</div>滞在時間
              <span><?php echo showErrMsg('stay'); ?></span>
              <select class="" name="stay">
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

            <div class="<?php if(!empty($err_msg['purpose'])) echo 'err'; ?>">
              <div class="label-required">必須</div>利用目的
              <span><?php echo showErrMsg('purpose'); ?></span>
              <label><input type="checkbox" name="purpose[]" value="1">勉強</label>
              <label><input type="checkbox" name="purpose[]" value="2">読書</label>
              <label><input type="checkbox" name="purpose[]" value="3">PC作業</label>
              <label><input type="checkbox" name="purpose[]" value="4">ビデオ会議</label>
              <label><input type="checkbox" name="purpose[]" value="5">面接</label>
            </div>
          </div>
        </section>
        <section>
          <div class="h2_space">
            <h2>利用後のご感想・ご評価<span class="small_font">（各項目1点~5点で採点してください）</span></h2>

          </div>

          <div class="for-space">
            <label class="<?php if(!empty($err_msg['concent_pt'])) echo 'err'; ?>">
              <div class="label-required">必須</div>コンセント設備へのご評価<span class="small_font">（設置席数・利用しやすさなど）</span>
              <span><?php echo showErrMsg('concent_pt'); ?></span>
              <select class="" name="concent_pt">
                <option value="" <?php if(empty(getFormData('concent_pt'))) echo 'selected';?> >選択してください</option>
                <option value="1" <?php if(getFormData('concent_pt') == "1") echo 'selected';?> >1点</option>
                <option value="2"<?php if(getFormData('concent_pt') == "2") echo 'selected';?> >2点</option>
                <option value="3" <?php if(getFormData('concent_pt') == "3") echo 'selected';?> >3点</option>
                <option value="4" <?php if(getFormData('concent_pt') == "4") echo 'selected';?>>4点</option>
                <option value="5" <?php if(getFormData('concent_pt') == "5") echo 'selected';?>>5点</option>
              </select>
            </label>

            <label class="<?php if(!empty($err_msg['wifi_pt'])) echo 'err'; ?>">
              <div class="label-required">必須</div>Wi-Fiへのご評価<span class="small_font">（電波の強さ・接続情報の分かりやすさなど）</span>
              <span><?php echo showErrMsg('wifi_pt'); ?></span>
              <select class="" name="wifi_pt">
                <option value="" <?php if(empty(getFormData('wifi_pt'))) echo 'selected';?> >選択してください</option>
                <option value="1" <?php if(getFormData('wifi_pt') == "1") echo 'selected';?> >1点</option>
                <option value="2"<?php if(getFormData('wifi_pt') == "2") echo 'selected';?> >2点</option>
                <option value="3" <?php if(getFormData('wifi_pt') == "3") echo 'selected';?> >3点</option>
                <option value="4" <?php if(getFormData('wifi_pt') == "4") echo 'selected';?>>4点</option>
                <option value="5" <?php if(getFormData('wifi_pt') == "5") echo 'selected';?>>5点</option>
              </select>
            </label>

            <label class="<?php if(!empty($err_msg['silence_pt'])) echo 'err'; ?>">
              <div class="label-required">必須</div>施設内の環境へのご評価<span class="small_font">（集中しやすさ・静かさ・混雑など）</span>
              <span><?php echo showErrMsg('silence_pt'); ?></span>
              <select class="" name="silence_pt">
                <option value="" <?php if(empty(getFormData('silence_pt'))) echo 'selected';?> >選択してください</option>
                <option value="1" <?php if(getFormData('silence_pt') == "1") echo 'selected';?> >1点</option>
                <option value="2"<?php if(getFormData('silence_pt') == "2") echo 'selected';?> >2点</option>
                <option value="3" <?php if(getFormData('silence_pt') == "3") echo 'selected';?> >3点</option>
                <option value="4" <?php if(getFormData('silence_pt') == "4") echo 'selected';?>>4点</option>
                <option value="5" <?php if(getFormData('silence_pt') == "5") echo 'selected';?>>5点</option>
              </select>
            </label>
          </div>
        </section>

        <section>
          <div class="h2_space">
            <h2>フリーコメント</h2>
          </div>

          <div class="for-space">
            <label class="<?php if(!empty($err_msg['title'])) echo 'err'; ?>">
              <div class="label-optional">任意</div>ひとこと感想
              <span><?php echo showErrMsg('title'); ?></span>
              <input type="text" name="title" placeholder="静かで設備も充実しており、作業にぴったりでした" value="<?php echo getFormData('title'); ?>">
              <p class="text-counter"><span class="js-text-count">0</span>/30文字</p>
            </label>

            <label class="<?php if(!empty($err_msg['comment'])) echo 'err'; ?>">
              <div class="label-optional">任意</div>詳細なコメント・その他備考など
              <span><?php echo showErrMsg('comment'); ?></span>
              <input type="text" name="comment" value="<?php echo getFormData('comment'); ?>">
              <p class="text-counter"><span class="js-text-count">0</span>/200文字</p>
            </label>

            <input type="submit" value="<?php ($edit_flg)? echo '編集する': echo'投稿する'; ?>">
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
