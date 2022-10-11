<?php
require('function.php');

// ログイン認証は不要

//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================
$area = (!empty($_GET['ar'])) ? $_GET['ar'] :'';
$purpose = (!empty($_GET['pu'])) ? $_GET['pu'] :'';
$type = (!empty($_GET['ty'])) ? $_GET['ty'] :'';
$concent = (isset($_GET['c'])) ? $_GET['c'] :'';
$c_rate = (!empty($_GET['c_r'])) ? $_GET['c_r'] :'';
$wifi = (isset($_GET['w'])) ? $_GET['w'] :'';
$w_rate = (!empty($_GET['w_r'])) ? $_GET['w_r'] :'';
$s_rate = (!empty($_GET['s_r'])) ? $_GET['s_r'] :'';

// GET値によって現在のページが変化（GET値がなければ1ページ目）
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;
// ページ内の表示件数を設定
$listSpan = 10;
// 現在のページのうち、一番小さいデータを取得 0-> 10 -> 20
$currentMinNum = ($currentPageNum - 1) * $listSpan;
// 施設データ一覧を取得
$dbInstList = getInstList($listSpan, $currentMinNum, $area, $purpose,
              $type, $concent, $c_rate, $wifi, $w_rate, $s_rate);
debug('$dbInstListの値：'.print_r($dbInstList, true));

// 0件でなければ,各施設のレビューデータを取得
if($dbInstList['total_data'] !== 0){
  foreach ($dbInstList['inst_id_list'] as $key => $data) {
    $dbInstListReview[$data['id']] = getInstListReview($data['id']);
  }
  debug('$dbInstListReviewの値：'.print_r($dbInstListReview, true));
}

// 施設ジャンルを取得
$dbTypeData = getTypeData();
// 利用シーンデータ取得(サイドバーで利用)
$dbPurposeData = getPurposeData();

// 複数条件を指定した際のページネーション生成用変数
$link = '&ar='.$area.'&pu='.$purpose.'&ty='.$type.'&c='.$concent.
        '&c_r='.$c_rate.'&w='.$wifi.'&w_r='.$w_rate.'&s_r='.$s_rate;

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
        <span style="float:right;"><?php echo ($currentMinNum +1);?>-<?php echo (($currentMinNum + $listSpan)<$dbInstList['total_data'])?($currentMinNum + $listSpan):$dbInstList['total_data']; ?>件／<?php echo $dbInstList['total_data'] ?>件中</span>
      </div>
    </div>
    <div class="list">
      <div class="list-inner">
        <?php if ($dbInstList['total_data'] === 0): ?>
          <p>検索条件に該当する施設がありません</p>
          <p>条件を変更して再度お試しください</p>
        <?php else: ?>
        <ul>
          <?php foreach ($dbInstListReview as $key => $value): ?>
          <li>
            <div class="card display_flex">
              <div class="photo-area padding_top10">
                <div class="main-photo">
                  <img src="<?php echo $value['image'][0]['path']; ?>" class="big_photo"alt="">
                </div>
                <div class="sub-photo">
                  <?php for ($i=1; $i <= 3 ; $i++):?>
                  <img src="<?php echo $value['image'][$i]['path']; ?>" alt="" class="small_photo">
                  <?php endfor; ?>
                </div>
              </div>
              <div class="script_area padding_top10">
                <div class="name_area border_bottom padding_bottom10">
                  <div class="display_flex">
                    <h2><a href="searchDetail.php?i=<?php echo $value['inst']['id'].'&p='.$currentPageNum; ?>"><?php echo $value['inst']['name']; ?></a></h2>
                    <div class="icon-space">
                      <span class="material-icons md-24 js-favorite <?php echo((isLike($_SESSION['user_id'], $value['inst']['id']))?'active':'nonactive');?>" data-instid="<?php echo $value['inst']['id']; ?>">favorite</span>
                    </div>
                  </div>
                  <p class="small_font"><a href="#"><?php echo $value['inst']['type']; ?></a>　｜　アクセス：<?php echo (($value['inst']['access'])?$value['inst']['access']:'ー'); ?></p>
                </div>
                <div class="feature_area border_bottom padding_top10 padding_bottom10">
                  <div class="review_score display_flex">
                    <span class="material-icons md-24 <?php echo(($value['inst']['t_avg']>=1)?'active':'nonactive'); ?>">grade</span><span class="material-icons md-24 <?php echo(($value['inst']['t_avg']>=2)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($value['inst']['t_avg']>=3)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($value['inst']['t_avg']>=4)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($value['inst']['t_avg']>=5)?'active':'nonactive') ?>">grade</span>
                    <div class="total_score">
                      <span><?php echo (($value['inst']['t_avg'])? number_format($value['inst']['t_avg'], 2) : '-.--'); ?></span>
                    </div>
                    <div class="review_numbers small_font">
                      <?php echo (($value['inst']['total_review'])? $value['inst']['total_review'] : 0); ?>件のクチコミ
                    </div>
                  </div>
                  <div class="purpose">
                    <?php if(!empty($value['inst']['purpose'])) echo '<a href="#">'.$value['inst']['purpose'].'におすすめ</a>'; ?>
                  </div>
                  <ul class="display_flex">
                    <?php if($value['inst']['concent']==='1') echo'<li class="list-feature"><a href="#">コンセントあり</a></li>'; ?>
                    <?php if($value['inst']['wifi']==='1') echo'<li class = "list-feature"><a href="#">Wi-fiあり</a></li>'; ?>
                    <?php if($value['inst']['stay_id'] >= 5) echo'<li class="list-feature"><a href="#">'.$value['inst']['stay'].'滞在</a></li>'; ?>
                    <?php if($value['inst']['s_avg'] >= 3.5) echo '<li class="list-feature"><a href="#">集中しやすい環境</a></li>';?>
                  </ul>
                </div>
                <div class="others_area">
                  <p class="padding_top10 small_font">営業時間：<?php echo $value['inst']['hours']; ?>　|　定休日：<?php echo $value['inst']['holidays']; ?></p>
                  <div class="comment">
                    <?php for ($i=0; $i < 2; $i++):?>
                    <?php
                     $date = (!empty($value['latest_review'][$i]))? date('Y/m/d',strtotime($value['latest_review'][$i]['create_date'])).'：' : '';
                     $com = (!empty($value['latest_review'][$i])) ? $value['latest_review'][$i]['title'] : 'ーー';
                     ?>
                    <p class="small_font"><?php echo $date.$com; ?></p>
                  <?php endfor; ?>
                  </div>
                </div>
              </div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
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
