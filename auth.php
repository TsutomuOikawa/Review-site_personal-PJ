<?php
//=========================================
//ログイン認証
//ログインしていないと見れないページで適用
//=========================================

//セッション情報の有無で未ログインユーザーか確認
if (!empty($_SESSION['login_date'])) {

  //セッション情報があるので、セッション有効期限を確認
  //有効期限切れの場合
  if ($_SESSION['login_date'] + $_SESSION['login_limit'] < time()) {
    //セッションを破棄（ログアウト）
    session_destroy();
    //ログインページへ
    //ログインページに遷移後、再度認証。セッション情報がない場合のフローへ
    header('Location:login.php');

  //有効期限内の場合
  }else {
    //最終ログイン時間を更新しマイページへ
    $_SESSION['login_date'] = time();
    //login.phpに入っている場合にはそのままマイページへ遷移
    if(basename($_SERVER['PHP_SELF'])==='login.php'){
      header('Location:mypage.php');
    }
  }

//セッション情報がないのでログイン画面へ
}else {
  //現在地がlogin.phpの場合はそのまま
  if (basename($_SERVER['PHP_SELF'])!=='login.php') {
    header('Location:login.php');
  }
}
 ?>
