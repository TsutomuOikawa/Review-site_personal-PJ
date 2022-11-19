<?php
require('function.php');

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
// タイトルタグの設定
$p_title = 'トップ';
//共通headタグ呼び出し
require('head.php');
//共通ヘッダー呼び出し
require('header.php');
 ?>

<!-- コンテンツ部分ー -->
<main>
  <div class="js-change-header-target">
    <section id="firstView" class="firstView">
      <div class="firstView--cover">
        <h1 class="firstView_title">見つけよう<br><span>集中</span>できる場所</h1>
        <form class="firstView_form" method="GET">
          <input type="text" name="ar" class="firstView_input" placeholder="駅名、地域名で検索">
          <button type="submit" class="firstView_button" name=""><i class="fa-solid fa-magnifying-glass fa-lg"></i></button>
        </form>
      </div>
    </section>

    <section id="narration" class="narration">
      <div class="narration_textBox">
        <p class="narration_text">Concent-rateは、集中力が高まる場所を探している人のための口コミサイト<br><span>その場所を訪れるまでわからなかった「集中できる場所かどうか」を、クチコミ評価によって可視化</span></p>
        <p class="narration_text">作業場所（コンセントがあるような場所 =concent）へのクチコミ評価（rate）を通じて<br>自分の時間に集中(concentrate)できる人が増えてほしい<br>そして、目標を叶えられる人が増えてほしい、という想いからうまれたサービスです</p>
      </div>
    </section>
  </div>

  <section id="features" class="features">
    <div class="container">
      <h2 class="container_title container_title--topPage">Features</h2>
      <div class="container_body">
        <p class="module_title">「集中できる場所」が見つかる<br>ありそうでなかったクチコミサイト</p>
        <div class="featuresBox">

          <div class="features_body features--01 panel panel--3frame">
            <h3 class="features_title">「集中できるか」で評価</h3>
            <div class="panel_thumbnail">
              <img src="img/studying-people.JPG" class="panel_bigImg" alt="">
            </div>
            <p class="panel_description ">電源やWi-Fi、静かさなど<br>作業のために重要な項目を評価<br>行くまでわからなかった情報が<span class="sp-delete"><br></span>事前にわかる</p>
          </div>

          <div class="features_body features--02 panel panel--3frame">
            <h3 class="features_title">多様な施設をカバー</h3>
            <div class="panel_thumbnail">
              <img src="img/cafe&library.JPG" class="panel_bigImg" alt="">
            </div>
            <p class="panel_description ">カフェや図書館から<br>レンタルスペースまで広くカバー<br>目的に合った施設が見つかる</p>
          </div>

          <div class="features_body features--03 panel panel--3frame">
            <h3 class="features_title">お気に入り機能</h3>
            <div class="panel_thumbnail">
              <img src="img/favorite.jpg" class="panel_bigImg" alt="">
            </div>
            <p class="panel_description ">気に入った施設は保存が可能<br>お気に入りの場所で<span class="sp-delete"><br></span>どんどん作業を進めよう</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="warries" class="warries">
    <div class="container">
      <div class="warries-wrapper" style="background:white;">
        <h2 class="container_title container_title--topPage">Warries</h2>
        <div class="container_body">
          <div class="module_title">
            <p>こんなふうに困った経験ありませんか<p>
            <p style="color:#EB9342;">Concent-rateなら もう困らない</p>
          </div>
          <div class="panelBox">
            <div class="panel panel--3frame panel--warries">
              <div class="panel_description">
                <i class="fa-regular fa-face-frown fa-2x icon--warries"></i>
                <p class="warries_text">PC作業をしたかったが<br>コンセントがない・Wi-Fiが弱い</p>
              </div>
              <div class="panel_description panel_description--warriesAns">
                <i class="fa-regular fa-face-smile fa-2x icon--warries"></i>
                <p class="warries_text">コンセント・Wi-Fi設備を点数化<br>設備の整った場所が見つかる</p>
              </div>
            </div>
            <div class="panel panel--3frame panel--warries">
              <div class="panel_description">
                <i class="fa-regular fa-face-frown fa-2x icon--warries"></i>
                <p class="warries_text">他の利用客の話し声で集中できない<br>利用時間に制限がある</p>
              </div>
              <div class="panel_description panel_description--warriesAns">
                <i class="fa-regular fa-face-smile fa-2x icon--warries"></i>
                <p class="warries_text">クチコミで客層や混雑度をチェック<br>作業に向かない場所を避けられる</p>
              </div>
            </div>
            <div class="panel panel--3frame panel--warries">
              <div class="panel_description">
                <i class="fa-regular fa-face-frown fa-2x icon--warries"></i>
                <p class="warries_text">家でweb会議ができない<br>ゆっくり集中できる場所がほしい</p>
              </div>
              <div class="panel_description panel_description--warriesAns">
                <i class="fa-regular fa-face-smile fa-2x icon--warries"></i>
                <p class="warries_text">利用目的や滞在時間別の検索も可能<br>最適な場所が見つかる</p>
              </div>
            </div>

          </div>

        </div>
      </div>
    </div>
  </section>

  <section id="search" class="">
    <div class="container">
        <h2 class="container_title container_title--topPage">Search</h2>
        <div class="container_body">

          <div class="search-wrapper">
            <h3 class="module_title">地域から探す</h3>
            <div class="cardBox">
              <a href="searchList.php?ar=新宿" class="card card--design">
                <img src="img/shinjuku.jpg" alt="" class="card_img">
                <div class="card_img--cover"></div>
                <p class="card_name card_name--design">新宿</p>
              </a>
              <a href="searchList.php?ar=渋谷" class="card card--design">
                <img src="img/shibuya.jpg" alt="" class="card_img">
                <div class="card_img--cover"></div>
                <p class="card_name card_name--design">渋谷</p>
              </a>
              <a href="searchList.php?ar=東京" class="card card--design">
                <img src="img/tokyo.jpg" alt="" class="card_img">
                <div class="card_img--cover"></div>
                <p class="card_name card_name--design">東京</p>
              </a>
              <a href="searchList.php?ar=池袋" class="card card--design">
                <img src="img/ikebukuro.webp" alt="" class="card_img">
                <div class="card_img--cover"></div>
                <p class="card_name card_name--design">池袋</p>
              </a>
              <a href="searchList.php?ar=上野" class="card card--design">
                <img src="img/ueno.jpg" alt="" class="card_img">
                <div class="card_img--cover"></div>
                <p class="card_name card_name--design">上野</p>
              </a>
              <a href="searchList.php?ar=品川" class="card card--design">
                <img src="img/shinagawa.webp" alt="" class="card_img">
                <div class="card_img--cover"></div>
                <p class="card_name card_name--design">品川</p>
              </a>
              <a href="searchList.php?ar=吉祥寺" class="card card--design">
                <img src="img/kichijoji.webp" alt="" class="card_img">
                <div class="card_img--cover"></div>
                <p class="card_name card_name--design">吉祥寺</p>
              </a>
              <a href="searchList.php?ar=横浜" class="card card--design">
                <img src="img/yokohama.webp" alt="" class="card_img">
                <div class="card_img--cover"></div>
                <p class="card_name card_name--design">横浜</p>
              </a>
            </div>
          </div>

          <div class="search-wrapper">
            <h3 class="module_title">利用目的から探す</h3>
            <div class="panelBox">
              <a href="searchList.php?pu=1" class="panel panel--purpose --hoverFlow">
                <div class="panel_thumbnail">
                  <img src="img/studying.jpg" class="panel_bigImg" alt="勉強">
                </div>
                <p class="panel_oneWord">勉強</p>
              </a>
              <a href="searchList.php?pu=2" class="panel panel--purpose --hoverFlow">
                <div class="panel_thumbnail">
                  <img src="img/pc.jpg" class="panel_bigImg" alt="PC作業">
                </div>
                <p class="panel_oneWord">PC作業</p>
              </a>
              <a href="searchList.php?pu=3" class="panel panel--purpose --hoverFlow">
                <div class="panel_thumbnail">
                  <img src="img/work.jpg" class="panel_bigImg" alt="テレワーク">
                </div>
                <p class="panel_oneWord">テレワーク</p>
              </a>
              <a href="searchList.php?pu=4" class="panel panel--purpose --hoverFlow">
                <div class="panel_thumbnail">
                  <img src="img/meeting.jpg" class="panel_bigImg" alt="グループワーク">
                </div>
                <p class="panel_oneWord">グループワーク</p>
              </a>
              <a href="searchList.php?pu=5" class="panel panel--purpose --hoverFlow">
                <div class="panel_thumbnail">
                  <img src="img/video-meeting.jpg" class="panel_bigImg" alt="web面接">
                </div>
                <p class="panel_oneWord">web会議・面接</p>
              </a>
            </div>
          </div>
        </div>
    </div>
  </section>
</main>

<!--　共通フッター　-->
<?php require('footer.php'); ?>
