<?php
require('function.php');

//ログイン認証
require('auth.php');

//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
// =========================================
// 検索条件用データ取得
$dbTypeData = getTypeData();
$dbPurposeData = getPurposeData();
// 最新のクチコミ取得
$dbhLatestReview = getLatestReview();
debug('$dbhLatestReview:'.print_r($dbhLatestReview, true));

$area = (!empty($_GET['ar'])) ? $_GET['ar'] :'';
$purpose = (!empty($_GET['pu'])) ? $_GET['pu'] :'';
$type = (!empty($_GET['ty'])) ? $_GET['ty'] :'';
$concent = (isset($_GET['c'])) ? $_GET['c'] :'';
$c_rate = (!empty($_GET['c_r'])) ? $_GET['c_r'] :'';
$wifi = (isset($_GET['w'])) ? $_GET['w'] :'';
$w_rate = (!empty($_GET['w_r'])) ? $_GET['w_r'] :'';
$s_rate = (!empty($_GET['s_r'])) ? $_GET['s_r'] :'';

// GET送信があったら処理を開始
if (!empty($_GET)) {
  debug('GET送信あり・処理を開始します');

  // GET値からリンクを生成
  $link = 'searchList.php?';

  if (!empty($area)) {
    $link .= 'ar='.$area.'&';
  }
  if (!empty($purpose)) {
    $link .= 'pu='.$purpose.'&';
  }
  if (!empty($type)) {
    $link .= 'ty='.$type.'&';
  }
  if (!empty($concent)) {
    $link .= 'ty='.$concent.'&';
  }
  if (!empty($c_rate)) {
    $link .= 'ty='.$c_rate.'&';
  }
  if (!empty($wifi)) {
    $link .= 'ty='.$wifi.'&';
  }
  if (!empty($w_rate)) {
    $link .= 'ty='.$w_rate.'&';
  }
  if (!empty($s_rate)) {
    $link .= 'ty='.$s_rate.'&';
  }

  header('Location:'.$link);
  exit;
}

?>

<?php
//CSSファイルとタイトルタグの設定
$css_title = basename(__FILE__,".php");
$p_title = 'トップ';
//共通headタグ呼び出し
require('head.php');
//共通ヘッダー呼び出し
require('header.php');

 ?>

<!-- コンテンツ部分ー -->
<main>

  <section id="first-wrap">
    <div class="fv">
      <div class="toumei">
        <h2>見つけよう<br>あなたが集中できる場所</h2>
        <form class="top-input" method="GET">
          <input type="text" name="ar" placeholder="駅名、地域名など">
          <input type="submit" value="検索">
        </form>
        <p>
        Concent-rate は、勉強や読書、仕事のために<br>
        集中できる場所を探している人のための口コミサイトです。<br>
        <span class="font-sizeS"><br></span>
        Wi-Fiやコンセントの有無、静かさや滞在可能時間等の観点から<br>
        作業場所として優れたカフェや施設を探すことができます。
        </p>
      </div>
    </div>
    <div class="rate-ex">
    </div>
  </section>

  <section id="find">
    <div class="sectionWrapper">
      <div class="pageWidth pageWidth--display">

        <?php require('sidebarLeft.php'); ?>
        <div class="center">

          <div class="introList introList--first">
            <h2 class="introList_title"> 地域から探す </h2>
            <div class="introList_item">
              <a href="searchList.php?ar=新宿" class="linkCard linkCard--introList">
                <img src="img/shinjuku.jpg" alt="" class="linkCard_img--introList">
                <p class="linkCard_name linkCard_name--introList">新宿</p>
              </a>
              <a href="searchList.php?ar=渋谷" class="linkCard linkCard--introList">
                <img src="img/shibuya.jpg" alt="" class="linkCard_img--introList">
                <p class="linkCard_name linkCard_name--introList">渋谷</p>
              </a>
              <a href="searchList.php?ar=東京" class="linkCard linkCard--introList">
                <img src="img/tokyo.jpg" alt="" class="linkCard_img--introList">
                <p class="linkCard_name linkCard_name--introList">東京</p>
              </a>
              <a href="searchList.php?ar=池袋" class="linkCard linkCard--introList">
                <img src="img/ikebukuro.webp" alt="" class="linkCard_img--introList">
                <p class="linkCard_name linkCard_name--introList">池袋</p>
              </a>
              <a href="searchList.php?ar=上野" class="linkCard linkCard--introList">
                <img src="img/ueno.jpg" alt="" class="linkCard_img--introList">
                <p class="linkCard_name linkCard_name--introList">上野</p>
              </a>
              <a href="searchList.php?ar=品川" class="linkCard linkCard--introList">
                <img src="img/shinagawa.webp" alt="" class="linkCard_img--introList">
                <p class="linkCard_name linkCard_name--introList">品川</p>
              </a>
              <a href="searchList.php?ar=吉祥寺" class="linkCard linkCard--introList">
                <img src="img/kichijoji.webp" alt="" class="linkCard_img--introList">
                <p class="linkCard_name linkCard_name--introList">吉祥寺</p>
              </a>
              <a href="searchList.php?ar=横浜" class="linkCard linkCard--introList">
                <img src="img/yokohama.webp" alt="" class="linkCard_img--introList">
                <p class="linkCard_name linkCard_name--introList">横浜</p>
              </a>
            </div>
          </div>

          <div class="introList introList--second">
            <h2 class="introList_title">利用目的から探す</h2>
            <div class="introList_item introList_item--center">
              <a href="searchList.php?pu=1" class="linkPic linkPic--introList">
                <img src="img/studying.jpg" class="linkPic_img linkPic_img--size" alt="勉強">
                <p class="linkPic_name linkPic_name--introList">勉強</p>
              </a>
              <a href="searchList.php?pu=2" class="linkPic linkPic--introList">
                <img src="img/pc.jpg" class="linkPic_img linkPic_img--size" alt="PC作業">
                <p class="linkPic_name linkPic_name--introList">PC作業</p>
              </a>
              <a href="searchList.php?pu=3" class="linkPic linkPic--introList">
                <img src="img/work.jpg" class="linkPic_img linkPic_img--size" alt="テレワーク">
                <p class="linkPic_name linkPic_name--introList">テレワーク</p>
              </a>
              <a href="searchList.php?pu=4" class="linkPic linkPic--introList">
                <img src="img/meeting.jpg" class="linkPic_img linkPic_img--size" alt="グループワーク">
                <p class="linkPic_name linkPic_name--introList">グループワーク</p>
              </a>
              <a href="searchList.php?pu=5" class="linkPic linkPic--introList">
                <img src="img/video-meeting.jpg" class="linkPic_img linkPic_img--size" alt="web面接">
                <p class="linkPic_name linkPic_name--introList">web会議・面接</p>
              </a>
            </div>
          </div>

          <div class="introList">
            <h2 class="introList_title">最新の口コミ投稿</h2>
            <ul class="introList_item introList_item--between">
              <?php foreach ($dbhLatestReview as $key => $val):?>
                <li class="linkPic linkPic--review">
                  <img src="<?php echo $val['path']; ?>" class="linkPic_img linkPic_img--size" alt="" >
                  <div class="linkPic_contents">
                    <p class="panelItem days"><?php echo date('Y/m/d',strtotime($val['create_date'])).'投稿' ; ?></p>
                    <div class="pointArea">
                      <div class="starBox pointAria_Item">
                        <span class="material-icons md-24 <?php echo(($val['total_pt']>=1)?'active':'nonactive'); ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=2)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=3)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=4)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=5)?'active':'nonactive') ?>">grade</span>
                      </div>
                      <div class="pointAria_Item">
                        <span class="totalPt"><?php echo $val['total_pt'] ?>.0</span>
                      </div>
                    </div>
                    <div class="panelItem panelItem--comment">
                      <?php echo $val['title'];?>
                    </div>
                  </div>

                </li>
              <?php endforeach; ?>
            </ul>
          </div>

        </div>
      </div>
    </div>
  </section>

<main>
<!-- ここでコンテンツ終わり -->

<!--　共通フッター　-->
<?php

require('footer.php');

 ?>
