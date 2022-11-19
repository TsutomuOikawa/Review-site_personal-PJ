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

$t_avg = (isset($dbInstDetail['inst']['t_avg']))?number_format($dbInstDetail['inst']['t_avg'],2):'--';
$c_avg = (isset($dbInstDetail['inst']['c_avg']))?number_format($dbInstDetail['inst']['c_avg'],1):'--';
$w_avg = (isset($dbInstDetail['inst']['w_avg']))?number_format($dbInstDetail['inst']['w_avg'],1):'--';
$s_avg = (isset($dbInstDetail['inst']['s_avg']))?number_format($dbInstDetail['inst']['s_avg'],1):'--';
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
  <div class="container">

    <section id="summarize" class="section section--summarize">
      <div class="container_body borderSeparate">
        <div class="instImg">
          <?php if (empty($dbInstDetail['image'])): ?>
            <div class="noItem">
              <img src="img/noimage.png" alt="サンプル画像" class="instImg_mainImg">
              <p>この施設はまだ写真が投稿されていません</p>
            </div>
          <?php else: ?>
            <div class="instImg_fv">
              <img src="<?php if(!empty($dbInstDetail['image'])) echo $dbInstDetail['image'][0]['path']; ?>" alt="メイン画像1" id="js-img-main" class="instImg_mainImg">
              <div class="instImg_imgBox">
                <?php foreach ($dbInstDetail['image'] as $id => $pic): ?>
                <img src="<?php echo $pic['path']; ?>" alt="<?php echo '画像'.$id.'：'.$dbInstDetail['inst']['name']; ?>" class="instImg_subImg js-img-sub">
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
        <div class="instInfo">
          <div class="instInfo_tagFav">
            <div class="instInfo_tags">
              <span class="tag --tagM tag--type"><?php echo $dbInstDetail['inst']['type'];?></span>
            <?php if(!empty($dbInstDetail['inst']['purpose'])):?>
              <span class="tag --tagM tag--purpose"><?php echo '<a href="searchList.php?pu='.$dbInstDetail['inst']['purpose_id'].'">'.$dbInstDetail['inst']['purpose'].'におすすめ</a>';?></span>
            <?php endif; ?>
            </div>
            <p class="tag --tagM">お気に入り登録
              <i class="fa-solid fa-heart fa-lg instInfo_favorite js-favorite <?php if(!empty($_SESSION['user_id'])) echo((isLike($_SESSION['user_id'], $i_id))?'active':'nonactive');?>" data-instid="<?php echo $i_id; ?>"></i>
              <i class="fa-solid fa-heart fa-lg instInfo_favorite js-favorite-animation <?php if(!empty($_SESSION['user_id'])) echo((isLike($_SESSION['user_id'], $i_id))?'active':'nonactive');?>" data-instid="<?php echo $i_id; ?>" data-instid="<?php echo $value['inst']['id'];?>"></i>
            </p>
          </div>
          <div class="instInfo_name">
            <h1 class="module_title"><?php echo $dbInstDetail['inst']['name']; ?></h1>
            <a href="#basic_information" class="--hoverLine">&gt 施設情報の詳細をみる</a>
          </div>
          <div class="pointArea">
            <div class="pointArea_total">
              <div class="pointArea_starBox">
              <?php for ($i=1; $i <= 5; $i++):?>
                <i class="fa-sharp fa-solid fa-star fa-2x <?php if($t_avg < $i) echo 'nonactive';?>"></i>
              <?php endfor; ?>
              </div>
              <p class="pointArea_totalPt--large"><?php echo $t_avg; ?></p>
            </div>
            <p class="pointArea_detailPt"><?php echo '［コンセント：<i class="fa-sharp fa-solid fa-star fa-lg"></i>'.$c_avg.'｜Wi-Fi：<i class="fa-sharp fa-solid fa-star fa-lg"></i>'.$w_avg.'｜静かさ：<i class="fa-sharp fa-solid fa-star fa-lg"></i>'.$s_avg.'］'; ?></p>
          </div>
          <ul class="featureTagBox">
            <?php if($dbInstDetail['inst']['concent']==='1') echo'<li class="tag tag--feature --tagM"><a href="#">コンセントあり</a></li>'; ?>
            <?php if($dbInstDetail['inst']['wifi']==='1') echo'<li class = "tag tag--feature --tagM"><a href="#">Wi-fiあり</a></li>'; ?>
            <?php if($dbInstDetail['inst']['stay_id'] >= 5) echo'<li class="tag tag--feature --tagM"><a href="#">'.$dbInstDetail['inst']['stay'].'滞在</a></li>'; ?>
            <?php if($dbInstDetail['inst']['s_avg'] >= 3.5) echo '<li class="tag tag--feature --tagM"><a href="#">集中しやすい環境</a></li>';?>
          </ul>
        </div>
      </div>
    </section>

    <section id="reviews" class="section">
      <div class="container_body borderSeparate">

        <div class="reviews_head">
          <h2 class="module_title">投稿されたクチコミ</h2>
          <p>
            <span class="pointArea_reviewNum"><?php echo 'クチコミ'.$dbInstDetail['inst']['total_review'].'件／'; ?></span>
            <a href="<?php echo 'reviewPost.php?i='.$i_id; ?>" class="--hoverLine">この施設のクチコミを投稿</a>
          </p>
        </div>
        <div class="module scrollView">
          <div class="scrollView_contents">
            <?php if (!empty($dbInstDetail['review'])): ?>
            <ul class="reviewCard-wrapper">

              <?php foreach ($dbInstDetail['review'] as $key => $val):?>
              <li class="reviewCard reviewCard--sizeM">
                <div class="reviewCard_description">
                  <p class="font-sizeS"><?php echo(date('Y年m月d日',strtotime($val['create_date']))); ?>投稿</p>
                  <div class="pointArea">
                    <div class="pointArea_total">
                      <div class="pointArea_starBox">
                      <?php for ($i=1; $i <= 5; $i++):?>
                        <i class="fa-sharp fa-solid fa-star fa-lg <?php if($val['total_pt'] < $i) echo 'nonactive';?>"></i>
                      <?php endfor; ?>
                      </div>
                      <span class="pointArea_totalPt"><?php echo $val['total_pt']; ?>.0</span>
                    </div>
                    <p class="pointArea_detailPt">［コンセント：<i class="fa-sharp fa-solid fa-star"></i><?php echo $val['concent_pt'];?>｜Wi-Fi：<i class="fa-sharp fa-solid fa-star"></i><?php echo $val['wifi_pt']; ?>｜静かさ：<i class="fa-sharp fa-solid fa-star"></i><?php echo $val['silence_pt']; ?>］</p>
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

        </div>
      </div>
    </section>

    <section id="basic_information" class="section">
      <div class="container_body">
        <h2 class="module_title module_title--left">施設情報</h2>
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
  </div>
</article>

<!--　共通フッター呼び出し　-->
<?php require('footer.php'); ?>
