<?php
//共通変数・関数読み込み
require('function.php');

//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
//=========================================
//ログアウト機能
//セッションを破棄
session_destroy();
debug('セッション破棄完了・ログインページへ遷移します');

//ログインページへ移動
header("Location:login.php");
