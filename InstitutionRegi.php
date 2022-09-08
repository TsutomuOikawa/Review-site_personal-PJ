<?php
require('function.php');

//ログイン認証
require('auth.php');

 ?>

<?php
$css_title = basename(__FILE__,".php");
$p_title = '施設新規登録';
//共通headタグ呼び出し
require('head.php');

//共通ヘッダー呼び出し
require('header.php');
?>

<!--　メインコンテンツ　-->
<div class="wrap">
  <main>
    <div class="h1-wide">
      <h1>施設新規登録</h1>
    </div>
    <div class="mypage-inner">
      <section>
        <div class="h2_space">
          <h2>基本情報入力</h2>
        </div>
        <div class="for-space">
          <form class="wide" method="post">
            <div class ="regi-user">
              <div class="name-form">
                <label>施設名
                  <span class="err_msg"><?php if (!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
                </label>
                <input type="text" name="email" placeholder="cafeコントレ" value="">
              </div>
              <div class="name-form">
                <label>都道府県
                  <span class="err_msg"><?php if (!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
                </label>
                <input type="text" name="email" placeholder="東京都" value="">
              </div>
              <div class="email-form">
                <label>市区町村
                  <span class="err_msg"><?php if (!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
                </label>
                <input type="text" name="email" placeholder="中央区" value="">
              </div>
              <div class="old-form">
                <label>番地
                  <span class="err_msg"><?php if (!empty($err_msg['pass'])) echo $err_msg['pass']; ?></span>
                </label>
                <input type="text" name="pass" placeholder="中央1-1-1" value="">
              </div>
              <div class="zip-form">
                <label>施設ジャンル1
                  <span class="err_msg"><?php if (!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; ?></span>
                </label>
                <input type="text" name="pass_re" placeholder="">
              </div>
              <div class="address-form">
                <label>施設ジャンル2
                  <span class="err_msg"><?php if (!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; ?></span>
                </label>
                <input type="text" name="pass_re" placeholder="">
              </div>
            </div>
          </form>
        </div>
      </section>
      <section>
        <div class="h2_space">
          <h2>詳細情報入力</h2>
        </div>
        <div class="for-space">
          <form class="wide" method="post">
            <div class ="regi-user">
              <div class="name-form">
                <label>アクセス
                  <span class="err_msg"><?php if (!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
                </label>
                <input type="text" name="email" placeholder="JR東京駅八重洲口徒歩10分" value="">
              </div>
              <div class="name-form">
                <label>営業時間
                  <span class="err_msg"><?php if (!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
                </label>
                <input type="text" name="email" placeholder="平日：11:00~19:00／休日：10:00~20:00" value="">
              </div>
              <div class="email-form">
                <label>定休日
                  <span class="err_msg"><?php if (!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
                </label>
                <input type="text" name="email" placeholder="毎週水曜日" value="">
              </div>
              <div class="old-form">
                <label>コンセントの有無
                  <span class="err_msg"><?php if (!empty($err_msg['pass'])) echo $err_msg['pass']; ?></span>
                </label>
                <input type="checkbox" name="pass" value="">
              </div>
              <div class="zip-form">
                <label>Wi-Fiの有無
                  <span class="err_msg"><?php if (!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; ?></span>
                </label>
                <input type="checkbox" name="pass_re">
              </div>
              <div class="address-form">
                <label>ホームページ
                  <span class="err_msg"><?php if (!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; ?></span>
                </label>
                <input type="text" name="pass_re" placeholder="https://wwww">
              </div>
            </div>
          </form>
        </div>
      </section>
    </div>
  </main>

  <aside id="right-sidebar">
    <div class="sidebar-inner align-center">
      <div class="sidebar-title"><p>マイページメニュー</p></div>
      <ul>
        <li><a href="mypage.php">マイページトップ</a></li>
        <li><a href="profileEdit.php">プロフィール編集</a></li>
        <li><a href="institutionRegi.php">施設新規登録</a></li>
        <li><a href="passChange.php">パスワード変更</a></li>
        <li><a href="withdrawal.php">退会</a></li>
      </ul>
    </div>
  </aside>
</div>

<!--　共通フッター呼び出し　-->
<?php
require('footer.php');
 ?>
