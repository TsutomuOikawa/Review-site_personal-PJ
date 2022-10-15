<?php
//ログイン中だったら（セッション情報があったら）
if (!empty($_SESSION)) {
  //headerにはマイページとログアウト
  $header = array("logout.php","ログアウト", "mypage.php","マイページ",);

//未ログインだったら（セッション情報がなければ）
}else {
  //headerには会員登録とログイン
  $header = array("login.php","ログイン","registration.php","会員登録");
}
?>

<body>

<header id="header">
  <div class="header_wrapper header_wrapper--display">
    <a href="index.php" class="headerTitle"><!--<img src="#" class="logoImg" alt="サービスロゴ">-->Concent-rate</a>
    <ul class="headerNav">
      <li class="headerNav_Item"><a href=<?php echo $header[0]; ?> class="headerLink --hoverLine"><?php echo $header[1]; ?></a></li>
      <li class="headerNav_Item headerNav_Item--strong"><a href=<?php echo $header[2]; ?> class="headerLink --hoverFlow"><?php echo $header[3]; ?></a></li>
    </ul>
  </div>
</header>
