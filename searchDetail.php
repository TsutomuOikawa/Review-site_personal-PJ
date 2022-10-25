<?php
require('function.php');

//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
// =========================================
// GETパラメータを確認・変数に格納
$i_id = (!empty($_GET['i'])) ? $_GET['i'] : '';
debug('施設ID：'.$i_id);
// 施設詳細データを取得
$dbInstDetail = getInstDetail($i_id);
debug('$dbInstDetail：'.print_r($dbInstDetail,true));

// GETパラメータがあるのにデータが空であれば一覧ページに戻る
if (empty($dbInstDetail['inst'])) {
  debug('GETパラメータに不正な値が入りました。一覧ページへ遷移します');
  header('Location:searchList.php');
  exit;
}

$t_avg = (isset($dbInstDetail['inst']['t_avg']))?number_format($dbInstDetail['inst']['t_avg'],2):'ーー';
$c_avg = (isset($dbInstDetail['inst']['c_avg']))?number_format($dbInstDetail['inst']['c_avg'],1):'ーー';
$w_avg = (isset($dbInstDetail['inst']['w_avg']))?number_format($dbInstDetail['inst']['w_avg'],1):'ーー';
$s_avg = (isset($dbInstDetail['inst']['s_avg']))?number_format($dbInstDetail['inst']['s_avg'],1):'ーー';
 ?>

<?php
$p_title = $dbInstDetail['inst']['name'];
//共通headタグ呼び出し
require('head.php');
//共通ヘッダー呼び出し
require('header.php');
?>

<div id="js_show_msg" class="js_msg_window" style="display:none;">
  <p><?php echo getSessionMsg('js-msg'); ?></p>
</div>

<article class="page-wrapper">
  <section class="page_contents--separate">
    <?php if (empty($dbInstDetail['image'])): ?>
      <div class="noItem">
        <img src="img/noimage.png" alt="サンプル画像" class="mainPhoto">
        <p>この施設はまだ写真が投稿されていません</p>
      </div>

    <?php else: ?>

      <div class="display-between">
        <img src="<?php if(!empty($dbInstDetail['image'])) echo $dbInstDetail['image'][0]['path']; ?>" alt="メイン画像1" id="js-img-main1" class="imgBox_img --imgLL --3img">
        <img src="<?php if(!empty($dbInstDetail['image'])) echo $dbInstDetail['image'][1]['path']; ?>" alt="メイン画像2" id="js-img-main2" class="imgBox_img --imgLL --3img">
        <div class="imgBox_img --3img">
          <?php foreach ($dbInstDetail['image'] as $id => $pic): ?>
          <img src="<?php echo $pic['path']; ?>" alt="<?php echo '画像'.$id.'：'.$dbInstDetail['inst']['name']; ?>" class="--imgSS --3img js-img-sub">
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

  </section>

    <section id="summarize" class="borderSeparate page_contents--separate">
      <div class="display-between" style="padding: 0 30px;">
        <div>
          <span class="tag --tagM tag--type"><?php echo $dbInstDetail['inst']['type']; ?></span>
          <span class="tag --tagM tag--purpose"><?php echo $dbInstDetail['inst']['purpose'].'におすすめ'; ?></span>
        </div>
        <span class="tag display-centerBox">お気に入り登録<span class="material-icons md-24 js-favorite <?php echo((isLike($_SESSION['user_id'], $i_id))?'active':'nonactive');?>" data-instid="<?php echo $i_id; ?>">favorite</span></span>
      </div>
      <div class="display-between">
        <h1 class="subTitle"><?php echo $dbInstDetail['inst']['name']; ?></h1>
        <a href="#basic_information" class="--hoverLine" style="padding: 15px 30px;">&gt 施設情報の詳細をみる</a>
      </div>

      <div class="page_contents--center mainContents-wrapper">
        <div class="pointArea">
          <div class="pointArea_starBox">
            <span class="material-icons md-24 <?php echo(($t_avg>=1)?'active':'nonactive'); ?>">grade</span><span class="material-icons md-24 <?php echo(($t_avg>=2)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($t_avg>=3)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($t_avg>=4)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($t_avg>=5)?'active':'nonactive') ?>">grade</span>
          </div>
          <div class="pointArea_totalPt">
            <?php echo $t_avg; ?>
          </div>
          <p class="pointArea_detailPt"><?php echo '［コンセント：<span class="material-icons md-18">grade</span>'.$c_avg.'｜Wi-Fi：<span class="material-icons md-18">grade</span>'.$w_avg.'｜静かさ：<span class="material-icons md-18">grade</span>'.$s_avg.'］'; ?></p>
        </div>

        <ul class="display-flex">
          <?php if($dbInstDetail['inst']['concent']==='1') echo'<li class="tag tag--feature --tagM"><a href="#">コンセントあり</a></li>'; ?>
          <?php if($dbInstDetail['inst']['wifi']==='1') echo'<li class = "tag tag--feature --tagM"><a href="#">Wi-fiあり</a></li>'; ?>
          <?php if($dbInstDetail['inst']['stay_id'] >= 5) echo'<li class="tag tag--feature --tagM"><a href="#">'.$dbInstDetail['inst']['stay'].'滞在</a></li>'; ?>
          <?php if($dbInstDetail['inst']['s_avg'] >= 3.5) echo '<li class="tag tag--feature --tagM"><a href="#">集中しやすい環境</a></li>';?>
        </ul>
      </div>
    </section>

    <section id="reviews" class="borderSeparate page_contents--separate">
      <div class="display-between">
        <h2 class="subTitle">投稿されたクチコミ</h2>
        <div style="padding: 15px 30px;">
          <span class="pointArea_reviewNum"><?php echo 'クチコミ'.$dbInstDetail['inst']['total_review'].'件／'; ?></span>
          <a href="<?php echo 'reviewPost.php?i='.$i_id; ?>" class="--hoverLine">この施設のクチコミを投稿</a>
        </div>
      </div>
      <div class="page_contents--center mainContents-wrapper baseColor">
        <?php if (!empty($dbInstDetail['review'])): ?>
        <ul class="scrollView scrollView-wrapper">

          <?php foreach ($dbInstDetail['review'] as $key => $val):?>
          <li class="reviewCard reviewCard--sizeM">
            <div class="reviewCard_description">
              <p class="font-sizeS"><?php echo(date('Y年m月d日',strtotime($val['create_date']))); ?>投稿</p>
              <div class="pointArea">
                <div class="pointArea_starBox">
                  <span class="material-icons md-24 <?php echo(($val['total_pt']>=1)?'active':'nonactive'); ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=2)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=3)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=4)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=5)?'active':'nonactive') ?>">grade</span>
                  <span class="pointArea_totalPt"><?php echo $val['total_pt'] ?>.0</span>
                </div>
                <p class="pointArea_detailPt">［コンセント：<span class="material-icons md-18">grade</span><?php echo $val['concent_pt'];?>｜Wi-Fi：<span class="material-icons md-18">grade</span><?php echo $val['wifi_pt']; ?>｜静かさ：<span class="material-icons md-18">grade</span><?php echo $val['silence_pt']; ?>］</p>
              </div>
              <p class="borderSeparate"><?php echo $val['purpose']; ?>で利用｜滞在時間：<?php echo $val['stay'];?></p>
              <h3><?php echo $val['title'];?></h3>
              <p><?php echo $val['comment'];?></p>
            </div>

            <ul class="imgBox">
            <?php foreach ($dbInstDetail['image'] as $id => $pic):?>
            <?php if($pic['review_id'] == $val['id']): ?>
              <li class="--3img"><img src="<?php echo $pic['path']; ?>" class="imgBox_img --imgM" alt="<?php echo '画像'.$id.':'.$dbInstDetail['inst']['name']; ?>"></li>
            <?php endif; ?>
            <?php endforeach; ?>
            </ul>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <p class="noItem">まだクチコミ投稿がありません</p>
      <?php endif; ?>

      </div>
    </section>

    <section id="basic_information">
      <h2 class="subTitle">施設情報</h2>
      <div class="page_contents--center mainContents-wrapper scrollView-wrapper baseColor">
        <table class="table">
          <tr class="table_row">
            <th class="table_row_title"><span class="table_row_title--visual">施設名</span></th>
            <td class="table_row_data"><?php echo isEmpty($dbInstDetail['inst']['name']); ?></td>
          </tr>
          <tr class="table_row">
            <th scope="row" class="table_row_title"><span class="table_row_title--visual">施設タイプ</span></th>
            <td class="table_row_data"><?php echo isEmpty($dbInstDetail['inst']['type']); ?></td>
          </tr>
          <tr class="table_row">
            <th scope="row" class="table_row_title"><span class="table_row_title--visual">住所</span></th>
            <td class="table_row_data"><?php echo isEmpty($dbInstDetail['inst']['prefecture'].$dbInstDetail['inst']['city'].$dbInstDetail['inst']['address']); ?></td>
          </tr>
          <tr class="table_row">
            <th scope="row" class="table_row_title"><span class="table_row_title--visual">アクセス</span></th>
            <td class="table_row_data"><?php echo isEmpty($dbInstDetail['inst']['access']); ?></td>
          </tr>
          <tr class="table_row">
            <th scope="row" class="table_row_title"><span class="table_row_title--visual">営業時間</span></th>
            <td class="table_row_data"><?php echo isEmpty($dbInstDetail['inst']['hours']); ?></td>
          </tr>
          <tr class="table_row">
            <th scope="row" class="table_row_title"><span class="table_row_title--visual">定休日</span></th>
            <td class="table_row_data"><?php echo isEmpty($dbInstDetail['inst']['holidays']); ?></td>
          </tr>
          <tr class="table_row">
            <th scope="row" class="table_row_title"><span class="table_row_title--visual">ホームページ</span></th>
            <td class="table_row_data"><?php echo isEmpty($dbInstDetail['inst']['homepage']); ?></td>
          </tr>
        </table>
      </div>
    </section>

</article>

<!--　共通フッター呼び出し　-->
<?php require('footer.php'); ?>
