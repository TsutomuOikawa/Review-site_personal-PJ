<?php
require('function.php');

//ログイン認証
require('auth.php');

//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
// =========================================
$u_id = $_SESSION['user_id'];
$dbMypageData = getMypageData($u_id);
// debug('$dbMypagedataの値：'.print_r($dbMypageData,true));

 ?>

<?php
$p_title = 'マイページ';
//共通headタグ呼び出し
require('head.php');

//共通ヘッダー呼び出し
require('header.php');
?>

<!--　メインコンテンツ　-->
<div id="js_show_msg" style="display:none;" class="js_msg_window" >
  <p><?php echo getSessionMsg('js-msg'); ?></p>
</div>

<div class="page-wrapper">
  <h1 class="page_title">マイページトップ</h1>
  <div class="page_contents--between">

    <main class="mainContents-wrapper">
      <section class="contents--interval baseColor">
        <h2 class="subTitle subTitle--visual">お気に入りの施設</h2>
        <?php if(empty($dbMypageData['favorite'])): ?>
        <p class="noItem">お気に入り登録済みの施設がありません</p>

        <?php else: ?>
        <div class="scrollView scrollView--panel scrollView-wrapper">

          <?php foreach ($dbMypageData['favorite'] as $key => $val):?>
          <a href="searchDetail.php?i=<?php echo $val['id']; ?>" class="panel panel--frame --hoverFlow">
            <img src="<?php echo $val['image']['path']; ?>" class="panel_img" alt="">
            <div class="panel_description">
              <span class= "typeTag --tagS"><?php echo $val['type']; ?></span>
              <p class="smallTitle"><?php echo $val['name']; ?></p>
              <div class="pointArea">
                <div class="pointArea_starBox">
                  <span class="material-icons md-18">grade</span>
                </div>
                <p class="pointArea_totalPt --pointS"><?php echo isEmpty((int)$val['t_avg'], 2); ?></p>
              </div>
              <p class="font-sizeS"><?php echo $val['prefecture'].$val['city']; ?></p>
            </div>
          </a>
          <?php endforeach; ?>

        </div>
        <?php endif; ?>
      </section>

      <section class="contents--interval baseColor">
        <h2 class="subTitle subTitle--visual">投稿済みのクチコミ</h2>
        <?php if(empty($dbMypageData['review'])): ?>
        <p class="noItem">まだクチコミ投稿がありません</p>

        <?php else: ?>
        <ul class="scrollView scrollView-wrapper">

          <?php foreach ($dbMypageData['review'] as $key => $val):?>
          <li class="reviewCard reviewCard--visualW">
            <div class="reviewCard_description">
              <p class="font-sizeS"><?php echo date('Y年m月d日',strtotime($val['create_date'])).'投稿｜'.$val['name']; ?></p>
              <div class="pointArea">
                <div class="pointArea_starBox">
                  <span class="material-icons md-24 <?php echo(($val['total_pt']>=1)?'active':'nonactive'); ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=2)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=3)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=4)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=5)?'active':'nonactive') ?>">grade</span>
                </div>
                <p class="pointArea_totalPt"><?php echo $val['total_pt'] ?>.0</p>
                <p class="pointArea_detailPt">［コンセント<span class="material-icons md-18 md-18--padding">grade</span><?php echo $val['concent_pt'];?>｜Wi-Fi<span class="material-icons md-18 md-18--padding">grade</span><?php echo $val['wifi_pt']; ?>｜静かさ<span class="material-icons md-18 md-18--padding">grade</span><?php echo $val['silence_pt']; ?>］</p>
              </div>
              <p class="borderSeparate"><?php echo $val['purpose'].'で利用｜滞在時間：'.$val['stay'];?></p>
              <h3 class="smallTitle"><?php echo $val['title'];?></h3>
              <p><?php echo $val['comment'];?></p>
            </div>
            <div class="imgBox">
              <?php if (!empty($val['image'])): ?>
              <?php foreach ($val['image'] as $id => $pic):?>
              <img src="<?php echo $pic['path']; ?>" class="imgBox_img --imgM --3img" alt="<?php echo '画像'.$id.':'.$val['name']; ?>">
              <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </li>
          <?php endforeach; ?>

        </ul>
        <?php endif; ?>
      </section>

      <section class="contents--interval baseColor">
        <h2 class="subTitle subTitle--visual">あなたが新しく追加した施設</h2>
        <?php if(empty($dbMypageData['favorite'])): ?>
        <p class="noItem">追加した施設はありません</p>

        <?php else: ?>
        <div class="scrollView scrollView--panel scrollView-wrapper">

          <?php foreach ($dbMypageData['registration'] as $key => $val):?>
          <div class="panel panel--frame">
            <img src="<?php echo $val['image']; ?>" class="panel_img" alt="">
            <div class="panel_description">
              <span class="typeTag --tagS"><?php echo $val['type']; ?></span>
              <p class="smallTitle"><?php echo $val['name']; ?></p>
              <div class="pointArea">
                <div class="pointArea_starBox">
                  <span class="material-icons md-18">grade</span>
                </div>
                <p class="pointArea_totalPt --pointS"><?php echo isEmpty((int)$val['t_avg'], 2); ?></p>
              </div>
              <div>
                <a href="searchDetail.php?i=<?php echo $val['id']; ?>" class="connectedLink connectedLink--left link--full --hoverFlow">詳細をみる</a>
                <a href="institutionRegi.php?i=<?php echo $val['id']; ?>" class="connectedLink connectedLink--right link--full --hoverFlow">編集する</a>
              </div>
            </div>
          </div>
          <?php endforeach; ?>

        </div>
        <?php endif; ?>
      </section>
    </main>

    <?php require('sidebarRight.php'); ?>
  </div>
</div>

<!--　共通フッター呼び出し　-->
<?php
require('footer.php');
 ?>
