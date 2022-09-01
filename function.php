<?php
//エラーを画面に表示させる
error_reporting(E_ALL);
ini_set('log_errors','On');
ini_set('error_log','logs/php_error.log');


//=========================================
//セッション準備・有効期限を延長
//=========================================
//セッションの保管場所を/var/tmp へ移動
session_save_path('/var/tmp/');
//ガーベージコレクションが削除するセッションの有効期限を設定
//（30日以上経っているものに対してだけ100分の1の確率で削除）
ini_set('session.gc_maxlifetime',60*60*24*30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を30日に延ばす
ini_set('session.cookie_lifetime',60*60*24*30);
//セッションスタート
session_start();
//セッションIDを新しいものと置き換える
session_regenerate_id();

//=========================================
//エラーメッセージ
//=========================================
define('MSG01','入力必須です');
define('MSG02','メールの形式に誤りがあります');
define('MSG03','半角英数字のみ入力可能です');
define('MSG04','6文字以上で入力してください');
define('MSG05','255文字以下で入力してください');
define('MSG06','パスワードが一致していません');
define('MSG07','このメールアドレスは既に登録されています');
define('MSG08','エラーが発生しております。しばらく経ってから再度お試しください');
define('MSG09','メールアドレスとパスワードのどちらかが間違っています');

$err_msg = array();


//=========================================
//バリデーション関数
//=========================================
//未入力チェック
function validRequired($str,$key){
  if (empty($str)) {
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}

//メール形式チェック
function validEmail($str,$key){
  if (!preg_match('/^[a-zA-Z0-9\.-_]+@([a-zA-Z0-9\.-_])+[a-zA-Z0-9\.-_]+$/',$str)) {
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}

//パスワード形式チェック
function validPass($str,$key){
  if (!preg_match('/^[a-zA-Z0-9]+$/',$str)){
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}

//パスワード最小文字数チェック
function validMinLen($str,$key,$min=6){
  if (mb_strlen($str) < $min) {
    global $err_msg;
    $err_msg[$key] = MSG04;
  }
}

//パスワード最大文字数チェック
function validMaxLen($str,$key,$max=255){
  if (mb_strlen($str) > $max) {
    global $err_msg;
    $err_msg[$key] = MSG05;
  }
}

//パスワード一致チェック
function validMatch($str,$str2,$key){
  if ($str!==$str2) {
    global $err_msg;
    $err_msg[$key] = MSG06;
  }
}

//メール重複チェック
function validEmailDup($str){
  $dbh = dbConnect(); //$dbhが返り値
  $sql ='SELECT count(*) FROM users WHERE email=:email';
  $data = array(':email'=>$str);

  try {
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty(array_shift($result))) {
      $err_msg['email'] = MSG07;
    }

} catch (\Exception $e) {
    $err_msg['common']= MSG08;
  }
}


//=========================================
//データベース
//=========================================

//DB接続情報を用意する関数
function dbConnect(){
  //DB接続情報を定義
  $dsn = 'mysql:dbname=dokoBen;host=localhost;charset=utf8';
  $user = 'root';
  $password = 'root';
  $options = array(
   PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
   PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
   PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true );

   //DB接続情報をまとめたPDOクラスを生成して返す
   $dbh = new PDO($dsn,$user,$password,$options);
   return $dbh;
}


//SQL実行関数
function queryPost($dbh,$sql,$data){
  try {
    //prepareメソッドでSQL文をセット
    $stmt = $dbh->prepare($sql);
    //executeでprepareが用意したSQL文の実行し、結果を返す
    $stmt -> execute($data);
    return $stmt;

  } catch (\Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}



?>
