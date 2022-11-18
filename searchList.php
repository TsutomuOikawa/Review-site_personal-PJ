<?php
require('function.php');

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
  header('Location:searchList.php');
  exit;
}
 ?>

<?php
$p_title = '施設情報一覧';
//共通headタグ呼び出し
require('head.php');
//共通ヘッダー呼び出し
require('header.php');
?>

<!--　メインコンテンツ　-->
<div class="page-wrapper">

  <div class="container">
    <h1 class="container_title">施設情報一覧</h1>
    <div class="container_body container_body--divide">

      <?php require('sidebarLeft.php'); ?>
      <main class="container_mainBody--list module instList">

        <div class="instList_number">
          <p class="module_title"><?php echo '検索結果：'.$dbInstList['total_data'].'件'; ?></p>
          <p><?php echo ($currentMinNum +1);?>-<?php echo (($currentMinNum + $listSpan)<$dbInstList['total_data'])?($currentMinNum + $listSpan):$dbInstList['total_data']; ?>件／<?php echo $dbInstList['total_data'] ?>件中</p>
        </div>
        <div class="instList_body">
          <?php if ($dbInstList['total_data'] === 0): ?>
            <p class="noItem">検索条件に該当する施設がありません<br>
            条件を変更して再度お試しください</p>

          <?php else: ?>
            <ul class="reviewCard-wrapper">
              <?php foreach ($dbInstListReview as $key => $value): ?>
              <li class="reviewCard reviewCard--sizeM">
                <?php if(!empty($value['inst']['purpose'])) echo '<a href="#" class="tag --tagM tag--purpose tag--leftTop">'.$value['inst']['purpose'].'におすすめ</a>'; ?>
                <i class="fa-solid fa-heart fa-2x reviewCard_favorite js-favorite-animation <?php echo((isLike($_SESSION['user_id'], $value['inst']['id']))?'active':'nonactive');?>" data-instid="<?php echo $value['inst']['id'];?>"></i>
                <i class="fa-solid fa-heart fa-2x reviewCard_favorite js-favorite <?php echo((isLike($_SESSION['user_id'], $value['inst']['id']))?'active':'nonactive');?>" data-instid="<?php echo $value['inst']['id'];?>"></i>
                <h2 class="reviewCard_name"><a href="searchDetail.php?i=<?php echo $value['inst']['id'].'&p='.$currentPageNum; ?>" class="--hoverLine"><?php echo $value['inst']['name']; ?></a></h2>

                <div class="reviewCard_body">
                  <div class="reviewCard_imgArea">
                    <img src="<?php echo $value['image'][0]['path']; ?>" class="--imgL" alt="">
                    <div class="imgBox">
                      <?php for ($i=1; $i <= 3 ; $i++):?>
                      <img src="<?php echo $value['image'][$i]['path']; ?>" alt="" class="--imgS --3img">
                      <?php endfor; ?>
                    </div>
                  </div>

                  <div class="reviewCard_description">
                    <p><a href="#" class="tag tag--type --tagM"><?php echo $value['inst']['type']; ?></a></p>
                    <div class="pointArea">
                      <div class="pointArea_total">
                        <div class="pointArea_starBox">
                      <?php for ($i=1; $i <= 5; $i++):?>
                          <i class="fa-sharp fa-solid fa-star fa-lg <?php if($value['inst']['t_avg']<$i) echo 'nonactive';?>"></i>
                      <?php endfor; ?>
                        </div>
                        <p class="pointArea_totalPt"><?php echo (($value['inst']['t_avg'])? number_format($value['inst']['t_avg'], 2) : '-.--'); ?></p>
                      </div>
                      <p class="font-sizeS"><a href="searchDetail.php?i=<?php echo $value['inst']['id'].'&p='.$currentPageNum;?>#reviews" class="--hoverLine"><?php echo (($value['inst']['total_review'])? $value['inst']['total_review'] : 0); ?>件のクチコミ</a></p>
                    </div>

                    <ul class="reviewCard_featureTag borderSeparate">
                      <?php if($value['inst']['concent']==='1') echo'<li class="tag tag--feature"><a href="#" class="link--full --tagM">コンセントあり</a></li>'; ?>
                      <?php if($value['inst']['wifi']==='1') echo'<li class="tag tag--feature"><a href="#" class="link--full --tagM">Wi-fiあり</a></li>'; ?>
                      <?php if($value['inst']['stay_id'] >= 5) echo'<li class="tag tag--feature"><a href="#" class="link--full --tagM">'.$value['inst']['stay'].'滞在</a></li>'; ?>
                      <?php if($value['inst']['s_avg'] >= 3.5) echo '<li class="tag tag--feature"><a href="#" class="link--full --tagM">集中しやすい環境</a></li>';?>
                    </ul>
                    <div class="font-sizeS borderSeparate">
                      <p>営業時間：<?php echo $value['inst']['hours']; ?></p>
                      <p>定休日：<?php echo $value['inst']['holidays']; ?></p>
                      <p>アクセス：<?php echo ($value['inst']['access'])? $value['inst']['access'] :'ー'; ?></p>
                    </div>

                    <div class="font-sizeS">
                      <?php for ($i=0; $i < 2; $i++){
                        $date = (!empty($value['latest_review'][$i]))? date('Y/m/d',strtotime($value['latest_review'][$i]['create_date'])).'：' : '';
                        $comment = (!empty($value['latest_review'][$i])) ? $value['latest_review'][$i]['title'] : 'ーー';
                        echo '<p>'.$date.$comment.'</p>';
                      };?>
                    </div>
                  </div>
                </div>
              </li>
              <?php endforeach; ?>
            </ul>
            <div>
              <?php require('pagination.php'); ?>
            </div>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </div>
</div>

<!--　共通フッター呼び出し　-->
<?php require('footer.php'); ?>
