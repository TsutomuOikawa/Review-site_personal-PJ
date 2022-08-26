<?php
require('function.php');

//ログイン認証
require('auth.php');

//CSSファイルとタイトルタグの設定
$css_title = basename(__FILE__,".php");
$p_title = 'トップ';
//共通headタグ呼び出し
require('head.php');
//共通ヘッダー呼び出し
require('header.php');

 ?>

<!-- コンテンツ部分ー -->
<div class="main">

  <section id="first-wrap">
      <div class="fv">
        <div class="toumei">
          <h2>集中できる場所を<br>見つけよう</h2>
          <div class="top-input">
            <input type="text" name="free" placeholder="駅名、地域名など"><input type="submit" value="検索">
          </div>
          <p>
          Concent-rate は、勉強や読書、仕事のために<br>
          集中できる場所を探している人のための口コミサイトです。<br><br>
          Wi-Fiやコンセントの有無、客層や滞在可能時間等の観点から<br>
          作業場所として優れたカフェや施設を探すことができます。
          </p>
        </div>
      </div>
      <div class="rate-ex">
      </div>
  </section>

  <section id="find">
    <div class="toumei2">


    <div class="station">
      <h2>駅から探す</h2>

      <div class="stationex">
        <ul>
          <li><a href="#">新宿駅</a></li>
          <li><a href="#">渋谷駅</a></li>
          <li><a href="#">東京駅</a></li>
          <li><a href="#">池袋駅</a></li>
          <li><a href="#">上野駅</a></li>
          <li><a href="#">品川駅</a></li>
          <li><a href="#">吉祥寺駅</a></li>
          <li><a href="#">横浜駅</a></li>
        </ul>
      </div>
    </div>

    <div class="scene">
      <h2>利用シーンから探す</h2>

      <div class="scene_studying">
        <a href="#"><img src="img/studying.jpg" alt="勉強">勉強
        </a>
      </div>
      <div class="scene_reading">
        <a href="#"><img src="img/reading.jpg" alt="読書">読書
        </a>
      </div>
      <div class="scene_pc">
        <a href="#"><img src="#" alt="PCワーク">PCワーク
        </a>
      </div>
      <div class="scene_video">
        <a href="#">imgビデオ通話
        </a>
      </div>
      <div class="scene_meeting">
        <a href="#">img会議
        </a>
      </div>
    </div>

    <div class="latest">
      <h2>最新の口コミ投稿</h2>

      <div class="latest_1">
        投稿1
      </div>
      <div class="latest_2">
        投稿2
      </div>
      <div class="latest_3">
        投稿3
      </div>
      <div class="latest_4">
        投稿4
      </div>
    </div>

    </div>
  </section>

</div>
<!-- ここでコンテンツ終わり -->

<!--　共通フッター　-->
<?php

require('footer.php');

 ?>
