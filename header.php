<?php
//ログイン中だったら（セッション情報があったら）
if (!empty($_SESSION['login_date'])) {
  //headerにはマイページとログアウト
  $header = array('logout.php','ログアウト', 'mypage.php','マイページ',);

//未ログインだったら（セッション情報がなければ）
}else {
  //headerには会員登録とログイン
  $header = array('login.php','ログイン','registration.php','会員登録');
}
?>

<body>

<header id="header" class="header js-change-header <?php echo ($_SERVER['PHP_SELF']==='/Concent-rate/index.php')? 'active' :'' ?>">
  <div class="header_wrapper">
    <a href="index.php" class="header_title"><!--<img src="#" class="header_logo" alt="サービスロゴ">-->Concent-rate</a>
    <div class="header_humburger js-show-menu">
      <span></span>
      <span></span>
      <span></span>
    </div>
    <nav class="header_nav js-show-menu-target">
      <ul class="menu">
      <?php if (!empty($_SESSION['login_date'])):?>
        <li class="menu_item"><a href="profileEdit.php" class="menu_link --hoverLine">プロフィール編集</a></li>
        <li class="menu_item"><a href="mypage.php" class="menu_link --hoverLine">マイページ</a></li>
        <li class="menu_item"><a href="mypage.php" class="menu_link --hoverLine">お気に入り施設</a></li>
        <li class="menu_item"><a href="logout.php" class="menu_link menu_link--strong --hoverLine">ログアウト</a></li>
      <?php else: ?>
        <li class="menu_item"><a href="searchList.php" class="menu_link --hoverLine">施設検索</a></li>
        <li class="menu_item"><a href="login.php" class="menu_link --hoverLine">ログイン</a></li>
        <li class="menu_item"><a href="registration.php" class="menu_link menu_link--strong">会員登録</a></li>
      <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>
