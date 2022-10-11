
<!--　検索サイドバー　-->
<aside id="left-sidebar">
  <form class="sidebar-inner align_center">
    <div class="main_search">
      <ul>
        <li>
          <label class="search_detail">
            <p>エリアから探す</p>
            <div class="search_select_box">
              <input type="text" name="ar" value="<?php echo getFormData('ar', 0); ?>" placeholder="新宿、渋谷">
            </div>
          </label>
        </li>
        <li>
          <label class="search_detail">
            <p>利用目的から探す</p>
            <select name="pu" class="search_select_box">
              <option value="" <?php if (empty($purpose)) echo 'selected'; ?>>選択してください</option>
              <?php foreach ($dbPurposeData as $key => $value): ?>
              <option value="<?php echo $value['id']; ?>" <?php if(!empty($_GET) && $purpose == $value['id']) echo 'selected';?> ><?php echo $value['name']; ?></option>
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
            <select name=ty class="search_select_box">
              <option value="" <?php if (empty($type)) echo 'selected';?> >選択してください</option>
              <?php foreach ($dbTypeData as $key => $val): ?>
              <option value="<?php echo $val['id'];?>" <?php if($type == $val['id']) echo 'selected';?> ><?php echo $val['name']; ?></option>
              <?php endforeach; ?>
            </select>
          </label>
        </li>
        <li>
          <label class="search_detail">
            <p>コンセントの有無と評価</p>
            <select style="border-bottom:dashed #ededed 3px;" name="c">
              <option value="" <?php if($concent=='') echo'selected';?>>選択してください</option>
              <option value="1" <?php if($concent==='1') echo'selected';?> >あり</option>
              <option value="0" <?php if($concent==='0') echo'selected';?> >なし</option>
            </select>
            <select name = "c_r">
              <option value="" <?php if($c_rate=='') echo'selected';?>>選択してください</option>
              <?php for ($i=1; $i < 6 ; $i++):?>
              <option value="<?php echo $i; ?>" <?php if($c_rate==$i) echo'selected';?> ><?php echo $i.'点 以上'; ?></option>
              <?php endfor; ?>
            </select>
          </label>
        </li>
        <li>
          <label class="search_detail">
            <p>Wi-Fiの有無と評価</p>
            <select style="border-bottom:dashed #ededed 3px;" name="w">
              <option value="" <?php if($wifi==='') echo'selected';?>>選択してください</option>
              <option value="1" <?php if($wifi==='1') echo'selected';?> >あり</option>
              <option value="0" <?php if($wifi==='0') echo'selected';?> >なし</option>
            </select>
            <select name = "w_r">
              <option value="" <?php if($w_rate =='') echo'selected';?>>選択してください</option>
              <?php for ($i=1; $i < 6 ; $i++):?>
              <option value="<?php echo $i; ?>" <?php if($w_rate ==$i) echo'selected';?> ><?php echo $i.'点 以上'; ?></option>
              <?php endfor; ?>
            </select>
          </label>
        </li>
        <li>
          <label class="search_detail">
            <p>静かさ</p>
            <select name = "s_r">
              <option value="" <?php if($s_rate =='') echo'selected';?>>選択してください</option>
              <?php for ($i=1; $i < 6 ; $i++):?>
              <option value="<?php echo $i; ?>" <?php if($s_rate ==$i) echo'selected';?> ><?php echo $i.'点 以上'; ?></option>
              <?php endfor; ?>
            </select>
          </label>
        </li>
      </ul>
    </div>
    <div class="search_btn main_search">
      <input type="submit" name="" value="検索">
    </div>
  </form>
</aside>
