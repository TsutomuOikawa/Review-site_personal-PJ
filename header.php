<?php
//ログイン中だったら（セッション情報があったら）
if (!empty($_SESSION)) {
  //headerにはマイページとログアウト
  $header = array("mypage.php","マイページ","logout.php","ログアウト");

//未ログインだったら（セッション情報がなければ）
}else {
  //headerには会員登録とログイン
  $header = array("registration.php","会員登録","login.php","ログイン");
}
?>

<body>

<header>
  <div class="header-left">
    <a href="index.php"><img src="img/logo.png" alt=""></a>
  </div>
  <div class="header-right">
    <div class="r-btn btn">
      <a href=<?php echo $header[0]; ?>><?php echo $header[1]; ?></a>
    </div>
    <div class="l-btn btn">
      <a href=<?php echo $header[2]; ?>><?php echo $header[3]; ?></a>
    </div>
  </div>

</header>


<!-- ログアウトが押された時にはJSでポップアップを表示したい -->
