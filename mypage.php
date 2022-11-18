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
  <div class="container">
    <h1 class="container_title">マイページトップ</h1>
    <div class="container_body container_body--divide">
      <main class="container_mainBody">

        <section class="section">
          <div class="module scrollView">
            <h2 class="module_title module_title--surround">お気に入りの施設</h2>
            <?php if(empty($dbMypageData['favorite'])): ?>
            <p class="noItem">お気に入り登録済みの施設がありません</p>

            <?php else: ?>
            <div class="scrollView_contents scrollView_contents--panel">

              <?php foreach ($dbMypageData['favorite'] as $key => $val):?>
              <a href="searchDetail.php?i=<?php echo $val['id']; ?>" class="panel panel--3frame --hoverFlow">
                <img src="<?php echo $val['image']['path']; ?>" class="panel_img" alt="">
                <div class="panel_description">
                  <span class="tag tag--type --tagS"><?php echo $val['type']; ?></span>
                  <p class="smallTitle"><?php echo $val['name']; ?></p>
                  <div class="pointArea_total">
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
          </div>
        </section>

        <section class="section">
          <div class="module scrollView">
            <h2 class="module_title module_title--surround">投稿済みのクチコミ</h2>
            <?php if(empty($dbMypageData['review'])): ?>
            <p class="noItem">まだクチコミ投稿がありません</p>

            <?php else: ?>
            <ul class="scrollView_contents">

              <?php foreach ($dbMypageData['review'] as $key => $val):?>
              <li class="reviewCard reviewCard--sizeL">
                <div class="reviewCard_description">
                  <p class="font-sizeS"><?php echo date('Y年m月d日',strtotime($val['create_date'])).'投稿<span class="sp-delete">｜</span><br>'.$val['name']; ?></p>
                  <div class="pointArea">
                    <div class="pointArea_total">
                      <div class="pointArea_starBox">
                        <span class="material-icons md-24 <?php echo(($val['total_pt']>=1)?'active':'nonactive'); ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=2)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=3)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=4)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=5)?'active':'nonactive') ?>">grade</span>
                      </div>
                      <p class="pointArea_totalPt"><?php echo $val['total_pt'] ?>.0</p>
                    </div>
                    <p class="pointArea_detailPt"><span class="sp-delete">［</span>コンセント<span class="material-icons md-18 md-18--padding">grade</span><?php echo $val['concent_pt'];?>｜Wi-Fi<span class="material-icons md-18 md-18--padding">grade</span><?php echo $val['wifi_pt']; ?>｜静かさ<span class="material-icons md-18 md-18--padding">grade</span><?php echo $val['silence_pt']; ?><span class="sp-delete">］</span></p>
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
          </div>
        </section>

        <section class="section">
          <div class="module scrollView">
            <h2 class="module_title module_title--surround">あなたが新しく追加した施設</h2>
            <?php if(empty($dbMypageData['favorite'])): ?>
            <p class="noItem">追加した施設はありません</p>

            <?php else: ?>
            <div class="scrollView_contents scrollView_contents--panel">

              <?php foreach ($dbMypageData['registration'] as $key => $val):?>
              <div class="panel panel--3frame">
                <img src="<?php echo $val['image']; ?>" class="panel_img" alt="">
                <div class="panel_description">
                  <span class="tag tag--type --tagS"><?php echo $val['type']; ?></span>
                  <p class="smallTitle"><?php echo $val['name']; ?></p>
                  <div class="pointArea_total">
                    <div class="pointArea_starBox">
                      <span class="material-icons md-18">grade</span>
                    </div>
                    <p class="pointArea_totalPt --pointS"><?php echo isEmpty((int)$val['t_avg'], 2); ?></p>
                  </div>
                  <div class="panel_link">
                    <a href="searchDetail.php?i=<?php echo $val['id']; ?>" class="connectedLink connectedLink--left link--full --hoverFlow">詳細をみる</a>
                    <a href="institutionRegi.php?i=<?php echo $val['id']; ?>" class="connectedLink connectedLink--right link--full --hoverFlow">編集する</a>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>

            </div>
            <?php endif; ?>
          </div>
        </section>
      </main>

      <?php require('sidebarRight.php'); ?>
    </div>
  </div>
</div>

<!--　共通フッター呼び出し　-->
<?php require('footer.php'); ?>
