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
$css_title = basename(__FILE__,".php");
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
<div class="wrap">
  <main>
    <div class="h1-wide">
      <h1>マイページトップ</h1>
    </div>
    <div class="mypage-inner">

      <section>
        <div class="h2_space">
          <h2>お気に入りの施設</h2>
        </div>
        <div class="myInstList scrollView">
          <?php foreach ($dbMypageData['favorite'] as $key => $val):?>
          <div class="myInstList__item">
            <a href="searchDetail.php?i=<?php echo $val['id']; ?>">
              <img src="<?php echo $val['image']; ?>" class="myInstList__item__img" alt="">
              <p><?php echo $val['name']; ?></p>
              <p class="small_font"><?php echo $val['prefecture'].$val['city'].'／'.$val['type']; ?></p>
              <p><?php echo $val['t_avg']; ?></p>
            </a>
          </div>
        <?php endforeach; ?>
        </div>
      </section>

      <section>
        <div class="h2_space">
          <h2>投稿済みのクチコミ</h2>
        </div>
        <?php if(empty($dbMypageData['review'])): ?>
        <p class="align-center">まだクチコミ投稿がありません</p>

        <?php else: ?>
        <div class="scrollView">
          <ul>
            <?php foreach ($dbMypageData['review'] as $key => $val):?>
            <li>
              <div class="background">
                <div class="review_summary border_bottom padding_bottom10">
                  <p class="small_font"><?php echo(date('Y年m月d日',strtotime($val['create_date']))); ?>投稿</p>
                  <p><?php echo $val['name']; ?></p>
                  <div class="score display-flex">
                    <div>
                      <span class="material-icons md-24 <?php echo(($val['total_pt']>=1)?'active':'nonactive'); ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=2)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=3)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=4)?'active':'nonactive') ?>">grade</span><span class="material-icons md-24 <?php echo(($val['total_pt']>=5)?'active':'nonactive') ?>">grade</span>
                      <span class="total_pt"><?php echo $val['total_pt'] ?>.0</span>
                    </div>
                    <div class="detail_score">
                      [コンセント:<span class="material-icons md-18">grade</span><?php echo $val['concent_pt'];?>｜Wi-Fi:<span class="material-icons md-18">grade</span><?php echo $val['wifi_pt']; ?>｜静かさ:<span class="material-icons md-18">grade</span><?php echo $val['silence_pt']; ?>]
                    </div>
                  </div>
                  <div class="how_used">
                    <?php echo $val['purpose']; ?>で利用｜滞在時間：<?php echo $val['stay'];?>
                  </div>
                </div>
                <div class="review_detail">
                  <h3><?php echo $val['title'];?></h3>
                  <p><?php echo $val['comment'];?></p>
                  <ul class="display-flex">
                  <?php if (!empty($val['image'])): ?>
                  <?php foreach ($val['image'] as $id => $pic):?>
                  <li><img src="<?php echo $pic['path']; ?>" class="imgInList" alt="<?php echo '画像'.$id.':'.$val['name']; ?>"></li>
                  <?php endforeach; ?>
                  <?php endif; ?>
                  </ul>
                </div>
              </div>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
      </section>

      <section>
        <div class="h2_space">
          <h2>あなたが新規追加した施設</h2>
        </div>
        <div class="myInstList scrollView">
          <?php foreach ($dbMypageData['registration'] as $key => $val):?>
          <div class="myInstList__item">
            <a href="searchDetail.php?i=<?php echo $val['id']; ?>">
              <img src="<?php echo $val['image']; ?>" class="myInstList__item__img" alt="">
              <p><?php echo $val['name']; ?></p>
              <p class="small_font"><?php echo $val['prefecture'].$val['city'].'／'.$val['type']; ?></p>
              <p><?php echo $val['t_avg']; ?></p>
            </a>
          </div>
        <?php endforeach; ?>
        </div>
      </section>

    </div>
  </main>

  <?php require('sidebarRight.php'); ?>
</div>

<!--　共通フッター呼び出し　-->
<?php
require('footer.php');
 ?>
