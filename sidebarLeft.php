<?php
// 利用シーンデータ取得
$dbPurposeData = getPurposeData();

// 施設タイプデータ取得


if (!empty($_POST)) {
  debug('POST送信がありました・処理を開始します');

  $area = $_POST['area'];
  $purpose = $_POST['purpose'];
  $type = $_POST['type'];
}

 ?>
<!--　検索サイドバー　-->
<aside id="left-sidebar">
  <div class="sidebar-inner align_center">
    <div class="main_search">
      <ul>
        <li>
          <label class="search_detail">
            <p>エリアから探す</p>
            <div class="search_select_box">
              <input type="text" name="area" value="" placeholder="エリアを指定する">
            </div>
          </label>
        </li>
        <li>
          <label class="search_detail">
            <p>利用目的から探す</p>
            <select name="purpose" class="search_select_box">
              <option value="" <?php if (empty($purpose)) echo 'selected'; ?>>選択してください</option>
              <?php foreach ($dbPurposeData as $key => $value): ?>
              <option value="<?php echo $value['id']; ?>" <?php if(!empty($_POST) && $purpose == $value['id']) echo 'selected';?> ><?php echo $value['name']; ?></option>
            <?php endforeach; ?>
            </select>
          </label>
        </li>
      </ul>
    </div>
    <div class="sub_search">
      <ul>
        <li>
          <label class="search_detail">
            <p>施設タイプ</p>
            <select name=type class="search_select_box">
              <option value="" <?php if (empty($type)) echo 'selected';?> >選択してください</option>
              <?php foreach ($dbTypeData as $key => $val): ?>
              <option value="<?php echo $val['id'];?>" <?php if(!empty($_POST)&&$type == $value['id']) echo 'selected';?> ><?php echo $val['name']; ?></option>
              <?php endforeach; ?>
            </select>
          </label>
        </li>
        <li>
          <label class="search_detail">
            <p>コンセント</p>
            <select class="border_bottom" name="concent">
              <option value="" selected>選択してください</option>
              <option value="1">あり</option>
              <option value="0">なし</option>
            </select>
            <div class="search_select_box">
              <input type="text" name="" value="" placeholder="席数を指定する">
            </div>
          </label>
        </li>
        <li>
          <label class="search_detail">
            <p>Wi-Fi</p>
            <select class="border_bottom" name="wifi">
              <option value="" selected>選択してください</option>
              <option value="1">あり</option>
              <option value="0">なし</option>
            </select>
            <div class="search_select_box">
              <input type="text" name="" value="" placeholder="Wi-Fi強度を指定する">
            </div>
          </label>
        </li>
        <li>
          <label class="search_detail">
            <p>滞在可能時間の目安</p>
            <div class="search_select_box">
              <input type="text" name="" value="">
            </div>
          </label>
        </li>
        <li>
          <label class="search_detail">
            <p>静かさ</p>
            <div class="search_select_box">
              <input type="text" name="" value="">
            </div>
          </label>
        </li>
        <li>
          <label class="search_detail">
            <p>フリーワード</p>
            <div class="search_select_box">
              <input type="text" name="" value="">
            </div>
          </label>
        </li>
      </ul>
    </div>
    <div class="search_btn">
      <input type="submit" name="" value="検索">
    </div>
  </div>
</aside>
