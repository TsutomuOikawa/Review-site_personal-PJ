<?php
//エラーを画面に表示させる
error_reporting(E_ALL);
//ログを取る設定に変更
ini_set('log_errors','On');
//ログの出力先をerror.logに設定
ini_set('error_log','error.log');

//=========================================
//デバッグログ出力
//=========================================
//true or falseでデバッグ出力のスイッチに
$debug_flg = true;
//デバッグログ関数
function debug($str){
  global $debug_flg;
  if (!empty($debug_flg)){
    error_log('デバッグ：'.$str);
  }
}

//画面遷移のタイミングでログに自動表示
function debugLogStart(){
  global $debug_current_page;
  debug('〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜');
  debug('■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■');
  debug('現在のページ：'.$debug_current_page);
  debug('セッションID：'.session_id());
  debug('セッション変数の中身：'.print_r($_SESSION,true));
  debug('現在日時：'.time());
  if (!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])) {
    debug('セッション期限：'.($_SESSION['login_date'] + $_SESSION['login_limit']));
  }
}

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
//定数（エラーメッセージ・）
//=========================================
define('MSG01','入力必須です');
define('MSG02','メールアドレスの形式に誤りがあります');
define('MSG03','半角英数字のみ入力可能です');
define('MSG04','6文字以上で入力してください');
define('MSG05','文字以下で入力してください');
define('MSG06','パスワードが一致していません');
define('MSG07','有効なメールアドレスをご利用ください');
define('MSG08','エラーが発生しております。しばらく経ってから再度お試しください');
define('MSG09','メールアドレスまたはパスワードに誤りがあります');
define('MSG10','電話番号の形式に誤りがあります');
define('MSG11','年齢の形式に誤りがあります');
define('MSG12','「市」「区」「町」「村」までつけて入力してください');
define('MSG13','パスワードに誤りがあります');
define('MSG14','パスワードが変更されていません');
define('MSG15','メールアドレスに誤りがあります');
define('MSG16','文字で入力してください');
define('MSG17','認証キーに誤りがあります');
define('MSG18','認証キーの有効期限が切れています');
define('MSG19','選択に誤りがあります');
define('MSG20','http もしくは https から入力してください');
define('MSG21','選択形式に誤りがあります');
define('JSMSG01','パスワードが変更されました');
define('JSMSG02','メールを送信しました');
define('JSMSG03','登録が完了しました');
define('JSMSG04','プロフィールが変更されました');
define('JSMSG05','クチコミを投稿しました');
define('JSMSG06','クチコミの編集が完了しました');


//=========================================
//グローバル変数
//=========================================
//エラーメッセージ格納用配列
$err_msg = array();

//=========================================
//バリデーション関数
//=========================================
//未入力チェック
function validRequired($str,$key){
  if ($str === '') {
    global $err_msg;
    $err_msg[$key] = MSG01;
    debug('未入力項目がありました');
  }
}

//メール形式チェック
function validEmail($str,$key){
  if (!preg_match('/^[a-zA-Z0-9\.-_]+@([a-zA-Z0-9\.-_])+[a-zA-Z0-9\.-_]+$/',$str)) {
    global $err_msg;
    $err_msg[$key] = MSG02;
    debug('メールの形式に誤りがありました');
  }
}

//半角英数字チェック
function validHalf($str,$key){
  if (!preg_match('/^[a-zA-Z0-9]+$/',$str)){
    global $err_msg;
    $err_msg[$key] = MSG03;
    debug('半角英数字以外の入力がありました');
  }
}

//最小文字数チェック
function validMinLen($str,$key,$min){
  if (mb_strlen($str) < $min) {
    global $err_msg;
    $err_msg[$key] = MSG04;
    debug('文字数に不足がありました');
  }
}

//最大文字数(255)チェック
function validMaxLen($str,$key,$max = 255){
  if (mb_strlen($str) > $max) {
    global $err_msg;
    $err_msg[$key] = $max.MSG05;
    debug('文字数がオーバーしました');
  }
}

//パスワード一致チェック
function validMatch($str,$str2,$key){
  if ($str!==$str2) {
    global $err_msg;
    $err_msg[$key] = MSG06;
    debug('パスワードが一致しませんでした');
  }
}

//メール重複チェック
function validEmailDup($email){
  debug('メール重複のバリデーションチェックを始めます');

  global $err_msg;
  try {
    $dbh = dbConnect(); //$dbhが返り値
    $sql ='SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email'=> $email);
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty(array_shift($result))) {
      $err_msg['email'] = MSG07;
      debug('メールアドレスが登録済みです');
    }
  } catch (\Exception $e) {
    error_log('エラー発生：'. $e -> getMessage());
    $err_msg['common']= MSG08;
  }
}

//電話番号形式チェック
function validTel($str, $key){
  if(!preg_match('/^0[0-9]{9,10}$/',$str)) {
    global $err_msg;
    $err_msg[$key] = MSG10;
    debug('電話番号の形式に誤りがありました');
  }
}

//年齢形式チェック
function validAge($str, $key){
  if(!preg_match('/^[0-9]{1,3}$/', $str)) {
      global $err_msg;
      $err_msg[$key] = MSG11;
      debug('年齢の入力形式に誤りがありました');
  }
}

// セレクトボックスチェック
function validSelect($str, $key){
  if (!preg_match('/^[1-9][0-9]{0,1}$/', $str)) {
    global $err_msg;
    $err_msg[$key] = MSG19;
    debug($key.'のセレクトボックスに誤りがありました');
  }
}

// 任意のセレクトボックスチェック
function validOptSelect($str, $key){
  if (!preg_match('/^[0-9]{1,2}$/', $str)) {
    global $err_msg;
    $err_msg[$key] = MSG19;
    debug($key.'のセレクトボックスに誤りがありました');
  }
}

//市区町村形式チェック
function validCity($str, $key){
  if(!preg_match('/.+[市区町村]$/',$str)){
    global $err_msg;
    $err_msg[$key] = MSG12;
    debug('市区町村の入力形式に誤りがありました');
  }
}

// 任意の市区町村形式チェック
function validOptCity($str, $key){
  if(!empty($str) && !preg_match('/.+[市区町村]$/',$str)){
    global $err_msg;
    $err_msg[$key] = MSG12;
    debug('市区町村の入力形式に誤りがありました');
  }
}

//パスワードチェック
function validPass($str, $key){
  validMaxLen($str,$key,$max = 256);
  validMinLen($str,$key,$max = 6);
  validHalf($str,$key);
}

//固定長チェック
function validLength($str, $key, $length){
  if (mb_strlen($str) !== $length) {
    global $err_msg;
    $err_msg[$key] = $length.MSG16;
    debug('文字の長さに誤りがありました');
  }
}

// URLチェック
function validURL($str, $key){
  if ($str !=='' && !preg_match('/^http.*+/', $str)) {
    global $err_msg;
    $err_msg[$key] = MSG20;
    debug('URLに誤りがありました');
  }
}

// 半角数字チェック
function validPurpose($str, $key){
  if (!preg_match('/^[0-9,]*$/', $str)) {
    global $err_msg;
    $err_msg[$key] = MSG21;
  }
}

//エラーメッセージ表示関数
function showErrMsg($key){
  global $err_msg;
  if (!empty($err_msg[$key])) {
    return $err_msg[$key];
  }
}

//=========================================
//データベース接続
//=========================================

//DB接続情報を用意する関数
function dbConnect(){
  //DB接続情報を定義
  $dsn = 'mysql:dbname=concent-rate;host=localhost;charset=utf8';
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
  //prepareメソッドでSQL文をセット
  $stmt = $dbh->prepare($sql);
  //executeでprepareが用意したSQL文の実行し、結果を返す
  $stmt -> execute($data);
  return $stmt;
}

//=========================================
//メール送信
//=========================================

function sendMail($from, $to, $subject, $message){
  //最低限の引数が揃っているか確認
  if (!empty($to) && !empty($subject) && !empty($message)) {
    // 基本設定
    mb_language('Japanese');
    mb_internal_encoding('UTF-8');

    $result = mb_send_mail($to, $subject, $message, 'From:'.$from);

    if ($result) {
      debug('メール送信成功');
    }else {
      debug('メール送信失敗');
    }
  }
}


//=========================================
//データベースからのデータ取得
//=========================================

//ユーザーデータを取得
function getUserData($u_id){
  debug('ユーザーデータの取得を始めます');
  global $err_msg;

  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM users WHERE id =:u_id';
    $data = array(':u_id' => $u_id );
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    debug(print_r($stmt,true));

    if($stmt){
      debug('クエリ成功');
    }else {
      debug('クエリ失敗');
      $err_msg['common'] = MSG08;
    }
  } catch (\Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG08;
  }
  return $stmt->fetch(PDO::FETCH_ASSOC);
  debug('DB情報の取得に成功しました');
}

// 施設情報編集用データ取得
function getInstData($i_id){
  debug('施設データを取得します');
  try {
    // DB接続
    $dbh = dbConnect();
    $sql = 'SELECT * FROM institution WHERE id = :i_id AND delete_flg = 0';
    $data = array(':i_id' => $i_id);
    // SQL実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      debug('クエリ成功');
      return $stmt -> fetch(PDO::FETCH_ASSOC);
    }else {
      debug('クエリ失敗');
      global $err_msg;
      $err_msg['common'] = MSG08;
    }

  } catch (\Exception $e) {
    error_log('エラー発生：'. $e -> getMessage());
    global $err_msg;
    $err_msg['common'] = MSG08;
  }
}

// searchListページ用施設データの一覧を取得
// 合計データ数とページ数、10件ごとのデータが欲しい
function getInstList($listSpan, $currentMinNum, $area, $purpose,
              $type, $concent, $c_num, $wifi, $w_rate, $stay, $silence){
  debug('施設データの一覧を取得します');
  try {

    $dbh = dbConnect();
    $sql = 'SELECT id FROM institution WHERE delete_flg = 0';

    // 検索条件が複数あるか判断
    // 条件分岐によってSQLを変更
    if (!empty($area)) {
        $sql .= " AND city LIKE '%".$area."%'";
    }
    // if (!empty($purpose)) {
    //    $sql .= ' AND purpose = '.$purpose;
    // }
    if (!empty($type)) {
      $sql .= ' AND type_id = '.$type;
    }
    if ($concent !== '') {
      $sql .= ' AND concent = '.$concent;
    }
    if ($wifi !== '') {
      $sql .= ' AND wifi = '.$wifi;
    }
    // debug('実行するSQL：'.$sql);

    $data = array();
    // SQL実行
    $stmt = queryPost($dbh, $sql, $data);
    // データ数を取得
    $rst['total_data'] = $stmt -> rowCount();
    $rst['total_page'] = ceil($rst['total_data'] / $listSpan);

    if (!$stmt -> fetchAll()) {
      debug('クエリに失敗しました');
      debug('失敗したSQL'.$sql);
      global $err_msg;
      $err_msg = MSG08;
    }
  } catch (\Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }

  // 2つ目の処理
  debug($currentMinNum.'から'.$listSpan.'件のデータを取得します');
  try {
    $dbh = dbConnect();
    $sql = 'SELECT i.*, p.name AS prefecture, t.name AS type FROM institution AS i LEFT JOIN prefecture AS p ON i.prefecture_id = p.id LEFT JOIN type AS t ON i.type_id = t.id WHERE i.delete_flg = 0';
    // 条件分岐によってSQLを変更
    if (!empty($area)) {
        $sql .= " AND city LIKE '%".(string)$area."%'";
    }
    // if (!empty($purpose)) {
    //    $sql .= ' AND purpose = '.$purpose;
    // }
    if (!empty($type)) {
      $sql .= ' AND type_id = '.$type;
    }
    if ($concent !== '') {
      $sql .= ' AND concent = '.$concent;
    }
    if ($wifi !== '') {
      $sql .= ' AND wifi = '.$wifi;
    }

    $sql = $sql.' LIMIT :list OFFSET :minNum';

    // debug('実行するSQL：'.$sql);

    $stmt = $dbh -> prepare($sql);
    $stmt -> bindParam(':list', $listSpan, PDO::PARAM_INT);
    $stmt -> bindParam(':minNum', $currentMinNum, PDO::PARAM_INT);
    $stmt -> execute();

    if ($stmt) {
      $rst['list_data'] = $stmt -> fetchAll();
      return $rst;
    }else {
      debug('クエリに失敗しました');
      debug('失敗したSQL'.$sql);
      global $err_msg;
      $err_msg = MSG08;
    }

  } catch (\Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
    }
}

// 施設詳細表示用データ取得
function getInstDetail($i_id){
  debug('施設詳細のデータを取得します');

  try {
    $dbh = dbConnect();
    // 施設情報データに都道府県、施設タイプ、評価項目の各平均点を取得
    $sql1 = 'SELECT i.id, i.name, i.city, i.address, i.access, i.hours, i.holidays, i.concent, i.wifi, i.homepage, p.name AS prefecture, t.name AS type, t_avg, c_avg, w_avg, s_avg, purpose, stay
              FROM institution AS i
              LEFT JOIN prefecture AS p ON i.prefecture_id = p.id
              LEFT JOIN type AS t ON i.type_id = t.id
              LEFT JOIN
                  (SELECT institution_id, AVG(total_pt) AS t_avg , AVG(concent_pt) AS c_avg, AVG(wifi_pt) AS w_avg, AVG(silence_pt) AS s_avg
                    FROM review WHERE delete_flg = 0 GROUP BY institution_id) AS avg
              ON i.id = avg.institution_id
              LEFT JOIN
                  (SELECT p_i_r.institution_id, pu.name AS purpose
                      FROM purpose_in_review AS p_i_r LEFT JOIN purpose AS pu ON p_i_r.purpose_id = pu.id
                      WHERE institution_id = :i_id GROUP BY purpose_id HAVING count(*) >= ALL (SELECT count(*) FROM purpose_in_review WHERE institution_id = 33 GROUP BY purpose_id)) AS pu_i
              ON i.id = pu_i.institution_id
              LEFT JOIN
                  (SELECT institution_id, stay
                    FROM review WHERE institution_id = :i_id GROUP BY stay HAVING count(*) >= ALL (SELECT count(*) FROM review WHERE institution_id = :i_id GROUP BY stay)) AS s
              ON i.id = s.institution_id
              WHERE i.id = :i_id AND i.delete_flg = 0';

    // 施設情報に紐づくクチコミデータも取得
    $sql2 = 'SELECT * FROM review WHERE institution_id = :i_id AND delete_flg = 0 ORDER BY create_date DESC';
    $data = array(':i_id' => $i_id);
    // 3種類のクエリを実行
    $stmt1 = queryPost($dbh, $sql1, $data);
    $stmt2 = queryPost($dbh, $sql2, $data);

    if ($stmt1 && $stmt2) {

    $rst['inst'] = $stmt1 -> fetch(PDO::FETCH_ASSOC);
    $rst['review-num'] = $stmt2 -> rowCount();
    $rst['review'] = $stmt2 -> fetchAll();

    debug('クエリ成功');
    return $rst;

    }else{
      debug('クエリに失敗しました');
      global $err_msg;
      $err_msg['common'] = MSG08;
    }

  } catch (\Exception $e) {
    error_log('エラー発生'.$e->getMessage());
    global $err_msg;
    $err_msg['common'] = MSG08;
  }
}

// 都道府県データ取得
function getPrefData(){
  debug('都道府県データを取得します');
  try {
    // DB接続
    $dbh = dbConnect();
    $sql = 'SELECT id, name FROM prefecture ORDER BY id';
    $data = array();
    // SQL実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      debug('クエリ成功');
      return $stmt -> fetchAll();
    }else{
      debug('クエリ失敗');
      global $err_msg;
      $err_msg['common'] = MSG08;
    }

  } catch (\Exception $e) {
    error_log('エラー発生：'. $e -> getMessage());
    global $err_msg;
    $err_msg['common'] = MSG08;
  }
}

// ジャンルデータ取得
function getTypeData(){
  try {
    debug('ジャンルデータを取得します');
    $dbh = dbConnect();
    $sql = 'SELECT id, name FROM type ORDER BY id';
    $data = array();
    // SQL実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      debug('クエリ成功');
      return $stmt -> fetchAll();
    }else {
      debug('クエリ失敗');
      global $err_msg;
      $err_msg['common'] = MSG08;
    }

  } catch (\Exception $e) {
    error_log('エラー発生：'. $e -> getMessage());
    global $err_msg;
    $err_msg['common'] = MSG08;
  }
}

// 利用目的データ取得
function getPurposeData(){
  debug('利用目的データを取得します');
  try {
    $dbh = dbConnect();
    $sql = 'SELECT id, name FROM purpose WHERE delete_flg = 0 ORDER BY id';
    $data = array();
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      return $stmt -> fetchAll();

    }else {
      debug('クエリ失敗');
      global $err_msg;
      $err_msg['common'] = MSG08;
    }

  } catch (\Exception $e) {
    error_log('エラー発生：'. $e -> getMessage());
    global $err_msg;
    $err_msg['common'] = MSG08;
  }
}

// レビューデータ全件取得
function getReviewData($r_id){
  debug('レビューデータを取得します');
  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM review WHERE id = :id AND delete_flg = 0';
    $data = array(':id' => $r_id);

    $stmt = queryPost($dbh, $sql, $data);
    if ($stmt) {
      return $stmt -> fetchAll();

    }else {
      debug('失敗したSQL：'.$sql);
      global $err_msg;
      $err_msg['common'] = MSG08;
    }

  } catch (\Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
    global $err_msg;
    $err_msg['common'] = MSG08;
  }
}

// 検索結果用レビューデータ取得
// 何件のレビューがあるか
// 総合得点はいくらか
// 最新クチコミの
function getInstListReview($i_id){
  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM review WHERE institution_id = :i_id AND delete_flg =0 ORDER BY create_date DESC';
    $data = array(':i_id' => $i_id);

    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      $rst['total_review'] = $stmt -> rowCount();
      $rst['all_review'] = $stmt -> fetchAll();

      return $rst;
    }else {
      debug('クエリ失敗：'.$sql);
    }
  } catch (\Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}

//=========================================
//その他
//=========================================
//inputフォームにDBデータor入力データを表示させる
function getFormData($str, $flg = 1){
  global $dbFormData;
  global $err_msg;

  if ($flg === 1) {
    $method = $_POST;
  }else {
    $method = $_GET;
  }

  //データベースにデータがある
  if(!empty($dbFormData)){
    //エラーメッセージが出ている
    if(!empty($err_msg)) {
      //送信がある = このフォームは送信したがエラーになった
      if(isset($method[$str])){
        return $method[$str];
      //送信がない = 送信してないのにエラーになった（あり得ない）
      }else {
        return $dbFormData[$str];
      }
    //エラーメッセージが出ていない
    }else{
      //送信がある = このフォームはして問題ないが、他フォームでエラーがある
      if(isset($method[$str])){
        return $method[$str];
      //送信がない = そもそも送信をしていない（初期状態）
      }else{
        return $dbFormData[$str];
      }
    }
  //データベースにデータがない
  }else{
    //送信があるならデータを表示
    if (isset($method[$str])) {
      return $method[$str];
    }
  }
}

//js用メッセージ表示関数
function getSessionMsg($key){
  if (!empty($_SESSION[$key])) {
    $msg = $_SESSION[$key];
    $_SESSION[$key] = "";
    return $msg;
  }
}

//ランダムキー発行関数
function makeRandkey($num){
  $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
  $str = '';
  for ($i=0; $i < $num ; $i++) {
    $str = $str.$chars[mt_rand(0,61)];
  }
  return $str;
}

// 画像表示
function showImg($path){
  if (empty($path)) {
    return 'img/noimage.jpeg';
  }else {
    return($path);
  }
}

// お気に入り検索
function isLike($u_id, $i_id){
  // debug('お気に入り登録状況を確認します');
  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM favorite WHERE user_id = :u_id AND institution_id = :i_id';
    $data = array(':u_id'=> $u_id, 'i_id'=> $i_id);

    $stmt = queryPost($dbh, $sql, $data);
    $result = $stmt -> fetchAll();

    if (!empty($result)) {
      // debug('お気に入り登録があります');
      return true;
    }else {
      // debug('お気に入り登録はありません');
      return false;
    }
  } catch (\Exception $e) {
    error_log('エラー発生：'.$e-> getMessage());
  }
}



?>
