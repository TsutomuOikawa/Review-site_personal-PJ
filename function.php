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
$debug_flg = false;
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
define('JSMSG06','ログアウトが完了しました');

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

// 配列数字チェック
function validIsArrayNum($str, $key){
  $str = implode(',', $str);
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

//////////////////////////////
// DB接続情報を用意する関数
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

//////////////////////////////
// SQL実行関数
function queryPost($dbh,$sql,$data){
  //prepareメソッドでSQL文をセット
  $stmt = $dbh->prepare($sql);
  //executeでprepareが用意したSQL文の実行し、結果を返す
  $stmt -> execute($data);

  if ($stmt) {
    debug('クエリ成功');
    return $stmt;
  }else {
    debug('クエリ失敗');
    return false;
  }
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

//////////////////////////////
// ユーザーデータを取得
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

//////////////////////////////
// 画像データ取得
function getImgData($i_id, $limit){
  try {
    $dbh = dbConnect();
    $sql = 'SELECT review_id, `path` from image_in_review WHERE institution_id = :i_id ORDER BY id ASC LIMIT :l';

    $stmt = $dbh -> prepare($sql);
    $stmt -> bindParam(':i_id', $i_id, PDO::PARAM_INT);
    $stmt -> bindParam(':l', $limit, PDO::PARAM_INT);
    $stmt -> execute();

    if ($stmt) {
      if ($limit === 1) {
        return $stmt -> fetch(PDO::FETCH_ASSOC);
      }else{
        return $stmt -> fetchAll();
      }

    }else{
      debug('失敗したSQL：'.$sql);
      global $err_msg;
      $err_msg['common'] = MSG08;
    }

  } catch (\Exception $e) {
    debug($e-> getMessage());
  }
}

//////////////////////////////
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

//////////////////////////////
// 施設データに都道府県、施設タイプ、各評価平均点、利用目的、滞在時間を加えて全部網羅したデータ
function getInstAll($i_id){
  try {
    $dbh = dbConnect();
    $sql = 'SELECT i.id, i.name, i.city, i.address, i.access, i.hours, i.holidays, i.concent, i.wifi, i.homepage, p.name AS prefecture, t.name AS type, total_review, t_avg, c_avg, w_avg, s_avg, purpose, purpose_id, stay_id, stay
              FROM institution AS i
              LEFT JOIN prefecture AS p ON i.prefecture_id = p.id
              LEFT JOIN type AS t ON i.type_id = t.id
              LEFT JOIN
                  (SELECT institution_id, COUNT(id) AS total_review, AVG(total_pt) AS t_avg , AVG(concent_pt) AS c_avg, AVG(wifi_pt) AS w_avg, AVG(silence_pt) AS s_avg
                    FROM review WHERE delete_flg = 0 GROUP BY institution_id) AS avg
              ON i.id = avg.institution_id

              LEFT JOIN
                  (SELECT p_i_r.institution_id, pu.name AS purpose, pu.id AS purpose_id
                      FROM purpose_in_review AS p_i_r LEFT JOIN purpose AS pu ON p_i_r.purpose_id = pu.id
                      WHERE institution_id = :i_id GROUP BY purpose_id HAVING count(*) >= ALL (SELECT count(*) FROM purpose_in_review WHERE institution_id = :i_id GROUP BY purpose_id)) AS pu_i
              ON i.id = pu_i.institution_id

              LEFT JOIN
                  (SELECT institution_id, stay_id, s.name AS stay
                    FROM review AS r LEFT JOIN stay AS s ON r.stay_id = s.id WHERE institution_id = :i_id GROUP BY stay_id HAVING count(*) >= ALL (SELECT count(*) FROM review WHERE institution_id = :i_id GROUP BY stay_id)) AS s2
              ON i.id = s2.institution_id

              WHERE i.id = :i_id AND i.delete_flg = 0';

    $data = array('i_id' => $i_id);
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      // 利用目的と滞在時間は、max(count(X))の結果が2つ以上の結果を返す時がある
      // その場合は、滞在時間の長い方をデータとして返すために、array_popにする
      // 利用目的においては問わない
      $result = $stmt -> fetchAll();
      $result = array_pop($result);

      return $result;

    }else {
      debug('失敗したSQL：'.$sql);
      global $err_msg;
      $err_msg = MSG08;
    }
  } catch (\Exception $e) {
    error_log('エラー発生.'.$e->getMessage());
    global $err_msg;
    $err_msg = MSG08;
  }
}

//////////////////////////////
// searchListページ用施設データの一覧を取得
// 1つ目の処理：検索条件に該当する施設数を取得し、そこからページ数を計算
function getInstList($listSpan, $currentMinNum, $area, $purpose,
              $type, $concent, $c_rate, $wifi, $w_rate, $s_rate){
  debug('該当する施設データの全件数を取得します');
  try {

    $dbh = dbConnect();
    $sql = 'SELECT DISTINCT id FROM institution AS i
                      LEFT JOIN (SELECT r1.institution_id AS r1_institution_id, r1.purpose_id AS r1_purpose_id FROM
                                                                                                                (SELECT institution_id, purpose_id, count(*) AS num FROM purpose_in_review GROUP BY institution_id, purpose_id) AS r1 INNER JOIN (SELECT institution_id, max(num) AS m FROM (SELECT institution_id, purpose_id, count(*) AS num FROM purpose_in_review GROUP BY institution_id, purpose_id)AS r GROUP BY institution_id) AS r2 ON r1.institution_id = r2.institution_id AND r1.num = m) AS p
                              ON i.id = p.r1_institution_id
                      LEFT JOIN (SELECT institution_id ,AVG(total_pt) AS t_rate, AVG(concent_pt) AS c_rate, AVG(wifi_pt) AS w_rate, AVG(silence_pt) AS s_rate FROM review GROUP BY institution_id) AS avg
                              ON i.id = avg.institution_id
                      WHERE delete_flg = 0';
    // SQLに検索条件を追加
    if (!empty($area)) {
        $sql .= " AND (name LIKE '%" .$area. "%' OR city LIKE '%" .$area. "%' OR address LIKE '%". $area . "%' OR access LIKE '%". $area . "%')";
    }
    if (!empty($purpose)) {
       $sql .= ' AND r1_purpose_id = '.$purpose;
    }
    if (!empty($type)) {
      $sql .= ' AND type_id = '.$type;
    }
    if ($concent !== '') {
      $sql .= ' AND concent = '.$concent;
    }
    if ($c_rate !== '') {
      $swl .= ' AND c_rate >= '.$c_rate;
    }
    if ($wifi !== '') {
      $sql .= ' AND wifi = '.$wifi;
    }
    if ($w_rate !== '') {
      $sql .= ' AND w_rate >= '.$w_rate;
    }
    if ($s_rate !== '') {
      $sql .= ' AND s_rate >= '.$s_rate;
    }
    debug('実行するSQL1：'.$sql);

    $data = array();
    // SQL実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
    // データ数を取得
      $rst['total_data'] = $stmt -> rowCount();
      $rst['total_page'] = ceil($rst['total_data'] / $listSpan);

      // もし検索結果が0件なら、以下に続く2つ目の処理は行わない
      if ($rst['total_data'] === 0) {
        $rst['inst_id_list'] = '';
        return $rst;
        debug('2つ目の処理をスキップしました');
        exit;
      }

    }else{
      debug('クエリに失敗しました');
      debug('失敗したSQL'.$sql);
      global $err_msg;
      $err_msg = MSG08;
    }
  } catch (\Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }

  // 2つ目の処理：nページ目に表示する施設IDを取得
  debug($currentMinNum.'から'.$listSpan.'件の施設idを取得します');
  try {

    $sql = $sql.' LIMIT :list OFFSET :minNum';

    debug('実行するSQL2：'.$sql);

    $stmt = $dbh -> prepare($sql);
    $stmt -> bindParam(':list', $listSpan, PDO::PARAM_INT);
    $stmt -> bindParam(':minNum', $currentMinNum, PDO::PARAM_INT);
    $stmt -> execute();

    if ($stmt) {
      $rst['inst_id_list'] = $stmt -> fetchAll();
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

//////////////////////////////
// 検索結果用レビューデータ取得 各施設のレビュー件数、画像3枚、最新のクチコミコメント2件
function getInstListReview($i_id){
  // まずは該当施設の全データを格納
  $rst['inst'] = getInstAll($i_id);

  // 画像を4枚取得
  $rst['image'] = getImgData($i_id, 4);
  // 4枚に満たない分はサンプル画像を挿入
  $num = count($rst['image']);
  if ($num === 0) {
    for ($i=$num; $i < 4 ; $i++) {
      $rst['image'][$i] = array('review_id' => '', 'path' => 'img/noimage.png');
    }
  }elseif ($num !== 0 && $num < 4) {
    for ($i=$num; $i < 4 ; $i++) {
      $rst['image'][$i] = array('review_id' => $rst['image'][0]['review_id'], 'path' => 'img/noimage.png');
    }
  }

  // 加えて最新のコメントと登録日を2件取得
  try {
    $dbh = dbConnect();
    $sql = 'SELECT title, create_date FROM review WHERE institution_id = :i_id AND delete_flg =0 ORDER BY create_date DESC LIMIT 2';
    $data = array(':i_id' => $i_id);

    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      $rst['latest_review'] = $stmt -> fetchAll();
      return $rst;

    }else {
      debug('クエリ失敗：'.$sql);
    }
  } catch (\Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}

//////////////////////////////
// 施設詳細表示用データ取得
function getInstDetail($i_id){
  debug('施設詳細のデータを取得します');
  // まずは該当施設の全データを格納
  $rst['inst'] = getInstAll($i_id);
  // 最新の画像も9件取得
  $rst['image'] = getImgData($i_id, 9);
  // 施設情報に紐づくクチコミデータも取得
  try {
    $dbh = dbConnect();
    $sql = 'SELECT r.*, s.name AS stay FROM review AS r
                    LEFT JOIN stay AS s ON r.stay_id = s.id
                     WHERE r.institution_id = :i_id AND r.delete_flg = 0 ORDER BY create_date DESC';

    $data = array(':i_id' => $i_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      $rst['review'] = $stmt -> fetchAll();

      // 各レビューにおける利用目的も回収
      $sql2 = 'SELECT name FROM purpose AS p RIGHT JOIN purpose_in_review AS p_i_r ON p.id = p_i_r.purpose_id
                WHERE p_i_r.review_id = :r_id AND p_i_r.institution_id = :i_id';

      foreach ($rst['review'] as $id => $value) {

        $data2 = array(':r_id' => $value['id'] , ':i_id' => $i_id);
        $stmt2 = queryPost($dbh, $sql2, $data2);

        if ($stmt2) {
          $result = $stmt2 -> fetchAll();
          debug('$resultの値：'.print_r($result,true));
          $purposes = '';

          foreach ($result as $key => $p) {
            $purposes .=  array_shift($p).'、';
          }
          $rst['review'][$id] += array('purpose' => substr($purposes, 0 ,-3));
        }
      }

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

//////////////////////////////
// マイページデータ取得
function getMypageData($u_id){
  debug('マイページ表示用データを取得します');
  $rst = array();

  try {
    $dbh = dbConnect();

    // 1,お気に入り施設の名前、タイプ、市区町村、総合評価、写真1枚
    $sql = 'SELECT institution_id FROM favorite WHERE user_id = :u_id';
    $data = array(':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      $fav_array = $stmt -> fetchAll();

      foreach ($fav_array as $key => $id) {
        $rst['favorite'][$key] = getInstAll($id['institution_id']);
        $rst['favorite'][$key]['image'] = getImgData($id['institution_id'], 1);

        if(empty($rst['favorite'][$key]['image'])) {
          $rst['favorite'][$key]['image'] = 'img/noimage.png';
        }
      }

    }else {
      debug('失敗したSQL：'.$sql);
      global $err_msg;
      $err_msg['common'] = MSG08;
    }

    // 2,投稿したクチコミデータに施設名をプラス
    $sql2 = 'SELECT r.*, i.name, s.name AS stay FROM review AS r
                              LEFT JOIN institution AS i ON r.institution_id = i.id
                              LEFT JOIN stay AS s ON r.stay_id = s.id
              WHERE r.user_id = :u_id ORDER BY r.create_date DESC';
    $stmt2 = queryPost($dbh, $sql2, $data);

    if ($stmt2) {
      $rst['review'] = $stmt2 -> fetchAll();

      // 利用目的も取得
      foreach ($rst['review'] as $key => $val) {
        $sql2_2 = 'SELECT p.name FROM purpose_in_review AS pir LEFT JOIN purpose AS p ON pir.purpose_id = p.id WHERE pir.review_id = :r_id';
        $data2_2 = array(':r_id'=> $val['id']);
        $stmt2_2 = queryPost($dbh, $sql2_2, $data2_2);

        if ($stmt2_2) {
          $result2_2 = $stmt2_2 -> fetchAll();
          $purpose = '';

          foreach ($result2_2 as $value) { //$valueは単純配列
            $purpose .= array_shift($value).'、';
          }
          $rst['review'][$key]['purpose'] =  substr($purpose, 0, -3);
        }

      // 画像データも取得
        $sql2_3 = 'SELECT `path` FROM image_in_review WHERE review_id = :r_id';
        $data2_3 = array(':r_id'=> $val['id']);
        $stmt2_3 = queryPost($dbh, $sql2_3, $data2_3);

        if ($stmt2_3) {
          $rst['review'][$key]['image'] = $stmt2_3 -> fetchAll();
        }
      }

    } else {
      debug('失敗したSQL：'.$sql2);
      global $err_msg;
      $err_msg['common'] = MSG08;
    }

    // 3,施設の名前、タイプ、市区町村、総合評価、写真1枚
    $sql3 = 'SELECT id FROM institution WHERE user_id = :u_id';
    $stmt3 = queryPost($dbh, $sql3, $data);

    if ($stmt3) {
      $regi_array = $stmt3 -> fetchAll();

      foreach ($regi_array as $key => $id) {
        $rst['registration'][$key] = getInstAll($id['id']);
        $rst['registration'][$key]['image'] = getImgData($id['id'], 1);

        if(empty($rst['registration'][$key]['image'])) {
          $rst['registration'][$key]['image'] = 'img/noimage.png';
        }
      }

    }else {
      debug('失敗したSQL：'.$sql3);
      global $err_msg;
      $err_msg['common'] = MSG08;
    }

    // 全ての結果を返す
    return $rst;


  } catch (\Exception $e) {
    debug('エラー発生：'.$e-> getMessage());
    global $err_msg;
    $err_msg['common'] = MSG08;
  }
}

// トップページ用クチコミデータ取得
function getLatestReview(){
  // 写真がある最新のクチコミデータ4件を取得
  try {
    $dbh = dbConnect();
    $sql = 'SELECT id, total_pt, title, create_date FROM review WHERE image_post = 1 ORDER BY id DESC LIMIT 4 ';
    $data = array();
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      $rst = $stmt -> fetchAll();

      // レビューIDごとに写真を1件取得
      foreach ($rst as $key => $val) {
        $sql2 = 'SELECT `path` FROM image_in_review WHERE review_id = :r_id LIMIT 1';
        $data2 = array(':r_id' => $val['id']);
        $stmt2 = queryPost($dbh, $sql2, $data2);

        if ($stmt2) {
          $result = $stmt2 -> fetch(PDO::FETCH_ASSOC);
          $rst[$key]['path'] = array_shift($result);
        }
      }
      // 結果を返す
      return $rst;

    }else {
      debug('失敗したSQL：'.$sql);
    }

  } catch (\Exception $e) {
    error_log('エラー発生：'.$e-> getMessage());
  }
}

//=========================================
//サブテーブル項目取得
//=========================================
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

// タイプデータ取得
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

// 滞在時間データ取得
function getStayData(){
  try {
    $dbh = dbConnect();
    $sql = 'SELECT id, name From stay WHERE delete_flg = 0 ORDER BY id';
    $data = array();

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


// 画像アップロードパス生成用関数
function uploadImg($file, $key){
  // 画像のバリデーションチェック（エラーメッセージと拡張子のタイプ）
  if (isset($file['error']) && is_int($file['error'])) {

    try {
      // エラーメッセージでのバリデーション
      switch ($file['error']) {
        case UPLOAD_ERR_OK:
          break;
        case UPLOAD_ERR_INI_SIZE:
          throw new RuntimeException('ファイルがphp.ini指定のサイズをオーバーしています');
          break;
        case UPLOAD_ERR_FORM_SIZE:
          throw new RuntimeException('ファイルがフォーム指定のサイズをオーバーしています');
          break;
        case UPLOAD_ERR_NO_FILE:
          throw new RuntimeException('ファイルがアップロードされませんでした');
          break;
        default:
          throw new RuntimeException('その他のエラーが発生しました');
          break;
      }

      // MIMEタイプのチェック
      $type = @exif_imagetype($file['tmp_name']); //ファイルの型を取得
      if(!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)){
        throw new RuntimeException('画像タイプに誤りがあります');
      }

      // 画像をハッシュ化、パスを生成して返す
      $path = 'review_pic/'.sha1($file['tmp_name']).'.'.substr(image_type_to_mime_type($type), mb_strpos(image_type_to_mime_type($type), '/')+1);
      if (!move_uploaded_file($file['tmp_name'], $path)) {
        throw new RuntimeException('ファイルの移動に失敗しました');
      }

      debug('ファイルのアップロード完了');
      debug('ファイルパス：'.print_r($path, true));
      chmod($path, 0644);

      return $path;

    } catch (RuntimeException $e) {
      debug('画像アップエラー：'.$e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();
    }
   }
 }

function isEmpty($data){
  if (!isset($data)) {
    return '--';
  }elseif (isset($data) && is_int($data)) {
    return number_format($data, 2);
  }else {
    return $data;
  }
}

?>
