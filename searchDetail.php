<?php
require('function.php');

//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================
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

// POST送信があったら画面処理を開始
if (!empty($_POST)) {
  // 施設IDを渡しながらレビュー投稿画面へ
  header('Location:reviewPost.php?i='.$i_id);
  exit;
}
 ?>

<?php
$css_title = basename(__FILE__,".php");
$p_title = '店名';
//共通headタグ呼び出し
require('head.php');

//共通ヘッダー呼び出し
require('header.php');
?>

<div id="js_show_msg" class="js_msg_window" style="display:none;">
  <p><?php echo getSessionMsg('js-msg'); ?></p>
</div>

<main>
  <div class="wrapper">
    <h1 class="h1-wide"><?php echo $dbInstDetail['inst']['name']; ?></h1>
    <article>
      <div class="photo_top">
        <div class="main-photo">
          <img src="<?php echo showImg('img/pc.jpg'); ?>" alt="メイン画像1" id="js-img-main1">
        </div>
        <div class="main-photo">
          <img src="<?php echo showImg('img/reading.jpg'); ?>" alt="メイン画像2" id="js-img-main2">
        </div>
        <div class="sub-photo">
          <img src="<?php echo showImg('img/pc.jpg'); ?>" alt="画像1：<?php echo $dbInstDetail['inst']['name']; ?>" class="js-img-sub">
          <img src="<?php echo showImg('img/reading.jpg'); ?>" alt="画像2：<?php echo $dbInstDetail['inst']['name']; ?>" class="js-img-sub">
          <img src="<?php echo showImg('img/studying.jpg'); ?>" alt="画像3：<?php echo $dbInstDetail['inst']['name']; ?>" class="js-img-sub">
          <img src="<?php echo showImg('img/top.jpg'); ?>" alt="画像4：<?php echo $dbInstDetail['inst']['name']; ?>" class="js-img-sub">
          <img src="<?php echo showImg(''); ?>" alt="画像5：<?php echo $dbInstDetail['inst']['name']; ?>" class="js-img-sub">
          <img src="<?php echo showImg(''); ?>" alt="画像6：<?php echo $dbInstDetail['inst']['name']; ?>" class="js-img-sub">
          <img src="<?php echo showImg(''); ?>" alt="画像7：<?php echo $dbInstDetail['inst']['name']; ?>" class="js-img-sub">
          <img src="<?php echo showImg(''); ?>" alt="画像8：<?php echo $dbInstDetail['inst']['name']; ?>" class="js-img-sub">
          <img src="<?php echo showImg(''); ?>" alt="画像9：<?php echo $dbInstDetail['inst']['name']; ?>" class="js-img-sub">
        </div>
      </div>
      <section id="summarize">
        <div class="padding_top10 display_flex">
          <h2><?php echo $dbInstDetail['inst']['name']; ?></h2>
          <div class="genre_tag">
            <?php echo $dbInstDetail['inst']['type']; ?>
          </div>
          <span class="material-icons md-36" data-instid="<?php echo $i_id; ?>">favorite</span>

        </div>
        <div style="overflow:hidden;">
          <div class="summarize_left">
            <div class="review_score display_flex">
              <div class="total_score">
                <span>【4.57】</span>
              </div>
              <div class="score_right small_font">
                <div class="review_numbers">
                  【<?php echo $dbInstDetail['review-num']; ?>】件のクチコミ
                </div>
              </div>
            </div>
            <div class="features">
              <p class="padding_bottom10">
                <span>要修正勉強</span>
                <span>仕事</span>
                <span>ビデオ会議</span>
                におすすめ
              </p>
              <ul class="feature_tag display_flex">
                <li style="<?php if($dbInstDetail['inst']['concent'] ==0 )echo'display:none;'?>"><a href="#">コンセントあり</a></li>
                <li style="<?php if($dbInstDetail['inst']['wifi'] ==0 )echo'display:none;'?>"><a href="#">Wi-fiあり</a></li>
                <li><a href="#">3~4時間滞在</a></li>
                <li><a href="#">とても静か</a></li>
              </ul>
            </div>
          </div>
          <div class="summarize_right small_font">
            <div class="move_to_id_basic">
              <a href="#basic_information">施設情報の詳細をみる</a>
            </div>
            <form action="" method="post">
              <input type="submit" name="submit" value="この施設のクチコミを投稿する">
            </form>
          </div>
        </div>
      </section>
      <section id="reviews">
        <h2 class="padding_top10">最新のクチコミ</h2>
        <?php if (!empty($dbInstList['list_data'])): ?>
        <ul>
          <?php foreach ($dbInstDetail['review'] as $key => $val):?>
          <li>
            <div class="background">
              <div class="review_summary border_bottom padding_bottom10">
                <p class="small_font"><?php $val['create_date'].'投稿' ?>投稿</p>
                <div class="score display_flex">
                  <div class="total_score">
                    <span>【4.57】</span>
                  </div>
                  <div class="detail_score">
                    [コンセント：<?php echo $val['concent_pt'];?>｜Wi-Fi：<?php echo $val['wifi_pt']; ?>｜静かさ：<?php echo $val['silence_pt']; ?>]
                  </div>
                </div>
                <div class="how_used">
                  勉強・読書で利用｜滞在時間：<?php echo $val['stay'];?>
                </div>
              </div>
              <div class="review_detail">
                <h3><?php echo $val['title'];?></h3>
                <p><?php echo $val['comment'];?></p>
                <ul class="display_flex">
                  <li><img src="http://dummyimage.com/100x100/acc/fff.gif&text=画像" alt=""></li>
                  <li><img src="http://dummyimage.com/100x100/acc/fff.gif&text=画像" alt=""></li>
                  <li><img src="http://dummyimage.com/100x100/acc/fff.gif&text=画像" alt=""></li>
                  <li><img src="http://dummyimage.com/100x100/acc/fff.gif&text=画像" alt=""></li>
                  <li><img src="http://dummyimage.com/100x100/acc/fff.gif&text=画像" alt=""></li>
                </ul>
              </div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <p class="align_center">まだクチコミ投稿がありません</p>
      <?php endif; ?>

        <form class="review_action align_center" action="" method="post">
          <input type="submit" name="submit" value="この施設のクチコミを投稿する">
        </form>
      </section>
      <section id="basic_information">
        <h2 class="padding_top10">施設情報</h2>
        <div class="background">
          <table>
            <tr>
              <th scope="row"><span>施設名</span></th>
              <td><?php echo $dbInstDetail['inst']['name']; ?></td>
            </tr>
            <tr>
              <th scope="row"><span>施設タイプ</span></th>
              <td><?php echo $dbInstDetail['inst']['type']; ?></td>
            </tr>
            <tr>
              <th scope="row"><span>住所</span></th>
              <td><?php echo ($dbInstDetail['inst']['prefecture'].$dbInstDetail['inst']['city'].$dbInstDetail['inst']['address']); ?></td>
            </tr>
            <tr>
              <th scope="row"><span>アクセス</span></th>
              <td><?php echo $dbInstDetail['inst']['access']; ?></td>
            </tr>
            <tr>
              <th scope="row"><span>営業時間</span></th>
              <td><?php echo $dbInstDetail['inst']['hours']; ?></td>
            </tr>
            <tr>
              <th scope="row"><span>定休日</span></th>
              <td><?php echo $dbInstDetail['inst']['holidays']; ?></td>
            </tr>
            <tr>
              <th scope="row"><span>ホームページ</span></th>
              <td><?php echo $dbInstDetail['inst']['homepage']; ?></td>
            </tr>
          </table>
        </div>
      </section>
    </article>
  </div>
</main>

<!--　共通フッター呼び出し　-->
<?php
require('footer.php');
 ?>
