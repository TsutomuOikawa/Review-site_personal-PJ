<?php
//共通変数・関数読み込み
require('function.php');

//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================
//ログアウト機能
//セッション情報を初期化
$_SESSION = array();

debug('セッション情報破棄完了・ログインページへ遷移します');
$_SESSION['js-msg'] = JSMSG06;

//ログインページへ移動
header("Location:login.php");
