<?php
require('function.php');

//デバッグログ
$debug_current_page = basename(__FILE__);
debugLogStart();
 ?>

<?php
// POST通信をキャッチしたら処理スタート
debug('POST通信あり・処理を開始します');
debug('POSTの中身'.print_r($_POST, true));

//  ログインしていたら実行、未ログインならスルー
if (!empty($_SESSION['login_date']) && $_SESSION['login_date'] + $_SESSION['login_limit'] >= time()) {
  debug('ログイン済みユーザーです');

  $u_id = $_SESSION['user_id'];
  $i_id = $_POST['inst_id'];

  try {
    $dbh = dbConnect();

    if (isLike($u_id, $i_id)) {
      debug('DBから削除します');
      $sql = 'DELETE FROM favorite WHERE user_id = :u_id AND institution_id = :i_id';
      $data = array(':u_id'=> $u_id, 'i_id'=> $i_id);

    }else {
      debug('DBに登録します');
      $sql = 'INSERT INTO favorite (user_id, institution_id, create_date) VALUES (:u_id, :i_id, :c_date)';
      $data = array(':u_id'=> $u_id, 'i_id'=> $i_id, ':c_date'=> date('Y/m/d H:i:s'));
    }
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      debug('クエリ成功');

    }else {
      debug('クエリ失敗');
      debug('失敗したクエリ'.$sql);
    }

  } catch (\Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }


}else {
  debug('未ログインユーザーです');
  debug('処理を終了します');
}

?>
