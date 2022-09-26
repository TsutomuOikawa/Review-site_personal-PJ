<?php
require('function.php');

// ログイン認証は不要

//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================
// GET値によって現在のページが変化（GET値がなければ1ページ目）
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;
// ページ内の表示件数を設定
$listSpan = 3;
// 現在のページのうち、一番小さいデータを取得 1-> 11 -> 21
$currentMinNum = ($currentPageNum - 1) * $listSpan;
// 施設データ一覧を取得
$dbInstList = getInstList($listSpan, $currentMinNum);
// 施設ジャンルを取得
$dbTypeData = getTypeData();

// GET値に変な値が入っている（GETに値があるが、データを取得できない）場合はログインページへ
if (!empty($_GET['P']) && empty($dbInstList)) {
  debug('不正なGETパラメータです。p=1へ遷移します');
  header('Locetin:searchList');
  exit;
}

 ?>

<?php
$css_title = basename(__FILE__,".php");
$p_title = '検索結果一覧';
//共通headタグ呼び出し
require('head.php');

//共通ヘッダー呼び出し
require('header.php');
?>

<!--　メインコンテンツ　-->
<main>

  <?php require('sidebarLeft.php'); ?>
  <!--　検索結果一覧　-->
  <article>
    <div class="h1-wide">
      <h1>検索結果一覧</h1>
      <div style="">
        <span style="float:left;"><?php echo $dbInstList['total_data'] ?>件の施設が見つかりました</span>
        <span style="float:right;"><?php echo ($currentMinNum +1);?>-<?php echo ($currentMinNum + $listSpan); ?>件／<?php echo $dbInstList['total_data'] ?>件中</span>
      </div>
    </div>
    <div class="list">
      <div class="list-inner">
        <ul>
          <?php foreach ($dbInstList['list_data'] as $key => $value): ?>
          <li>
            <div class="card display_flex">
              <div class="photo-area padding_top10">
                <div class="main-photo">
                  <img src="http://dummyimage.com/185x185/acc/fff.gif&text=画像" alt="">
                </div>
                <div class="sub-photo">
                  <img src="http://dummyimage.com/55x55/acc/fff.gif&text=画像" alt="" class="small_photo">
                  <img src="http://dummyimage.com/55x55/acc/fff.gif&text=画像" alt="" class="small_photo">
                  <img src="http://dummyimage.com/55x55/acc/fff.gif&text=画像" alt="" class="small_photo">
                </div>
              </div>
              <div class="script_area padding_top10">
                <div class="name_area border_bottom padding_bottom10">
                  <h2><a href="searchDetail.php?i=<?php echo $value['id'].'&p='.$currentPageNum; ?>"><?php echo $value['name']; ?></a></h2>
                  <ul class="display_flex small_font">
                    <li>【<?php echo $value['prefecture_id']; ?>(要修正)】<?php echo $value['city']; ?>　</li>
                    <li>【アクション】におすすめの【<?php echo $value['type_id']; ?>(要修正)】</li>
                  </ul>
                </div>
                <div class="feature_area border_bottom padding_top10 padding_bottom10">
                  <div class="review_score display_flex">
                    <div class="total_score">
                      <span>【4.57】</span>
                    </div>
                    <div class="review_numbers small_font">
                      【34】件のクチコミ
                    </div>
                  </div>
                  <ul class="display_flex">
                    <?php if($value['concent']==='1') echo'<li><a href="#">コンセントあり</a></li>'; ?>
                    <?php if($value['wifi']==='1') echo'<li><a href="#">Wi-fiあり</a></li>'; ?>
                    <li><a href="#">3~4時間滞在</a></li>
                    <li><a href="#">とても静か</a></li>
                  </ul>
                </div>
                <div class="others_area ">
                  <p class="padding_top10 small_font">営業時間：<?php echo $value['hours']; ?>　|　定休日：<?php echo $value['holidays']; ?></p>
                  <p class="padding_top10 small_font">アクセス：<?php echo $value['access']; ?></p>
                  <div class="comment">
                    <p>最新のクチコミ</p>
                  </div>
                </div>
              </div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="more align_center">
        <?php require('pagination.php'); ?>
      </div>
    </div>
  </article>
</main>

<!--　共通フッター呼び出し　-->
<?php
require('footer.php');
 ?>
