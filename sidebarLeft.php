<!--　検索サイドバー　-->
<aside id="sidebar" class="baseColor">
  <form>
    <p class="sidebar_title subTitle--visual js-accordion-swich">基本条件</p>
    <ul class="sidebar_contents js-accordion-area">
      <li class="searchBox-listItem">
        <label>
          <p class="listItem_title">エリア</p>
          <input type="text" name="ar" class="listItem_input listItem_input--frame" value="<?php echo getFormData('ar', 0); ?>" placeholder="新宿、渋谷">
        </label>
      </li>
      <li class="searchBox-listItem">
        <label>
          <p class="listItem_title">施設タイプ</p>
          <select name=ty class="listItem_input listItem_input--frame">
            <option value="" <?php if (empty($type)) echo 'selected';?> >選択してください</option>
            <?php foreach ($dbTypeData as $key => $val): ?>
            <option value="<?php echo $val['id'];?>" <?php if($type == $val['id']) echo 'selected';?> ><?php echo $val['name']; ?></option>
            <?php endforeach; ?>
          </select>
        </label>
      </li>
      <li class="searchBox-listItem">
        <label>
          <p class="listItem_title">利用目的</p>
          <select name="pu" class="listItem_input listItem_input--frame">
            <option value="" <?php if (empty($purpose)) echo 'selected'; ?>>選択してください</option>
            <?php foreach ($dbPurposeData as $key => $value): ?>
            <option value="<?php echo $value['id']; ?>" <?php if(!empty($_GET) && $purpose == $value['id']) echo 'selected';?> ><?php echo $value['name']; ?></option>
          <?php endforeach; ?>
          </select>
        </label>
      </li>
    </ul>
    <p class="sidebar_title subTitle--visual js-accordion-swich">こだわり条件</p>
    <ul class="sidebar_contents js-accordion-area">
      <li class="searchBox-listItem">
        <label>
          <p class="listItem_title">コンセント</p>
          <select name="c" class="listItem_input listItem_input--frame" style="border-bottom:dashed #BFBFB3 2px;">
            <option value="" <?php if($concent=='') echo'selected';?>>あり or なし</option>
            <option value="1" <?php if($concent==='1') echo'selected';?> >あり</option>
            <option value="0" <?php if($concent==='0') echo'selected';?> >なし</option>
          </select>
          <select name = "c_r" class="listItem_input listItem_input--frame">
            <option value="" <?php if($c_rate=='') echo'selected';?>>ユーザー評価</option>
            <?php for ($i=1; $i < 6 ; $i++):?>
            <option value="<?php echo $i; ?>" <?php if($c_rate==$i) echo'selected';?> ><?php echo $i.'点 以上'; ?></option>
            <?php endfor; ?>
          </select>
        </label>
      </li>
      <li class="searchBox-listItem">
        <label>
          <p class="listItem_title">Wi-Fi</p>
          <select name="w" class="listItem_input listItem_input--frame" style="border-bottom:dashed #BFBFB3 2px;">
            <option value="" <?php if($wifi==='') echo'selected';?>>あり or なし</option>
            <option value="1" <?php if($wifi==='1') echo'selected';?> >あり</option>
            <option value="0" <?php if($wifi==='0') echo'selected';?> >なし</option>
          </select>
          <select name = "w_r" class="listItem_input listItem_input--frame">
            <option value="" <?php if($w_rate =='') echo'selected';?>>ユーザー評価</option>
            <?php for ($i=1; $i < 6 ; $i++):?>
            <option value="<?php echo $i; ?>" <?php if($w_rate ==$i) echo'selected';?> ><?php echo $i.'点 以上'; ?></option>
            <?php endfor; ?>
          </select>
        </label>
      </li>
      <li class="searchBox-listItem searchBox-listItem--last">
        <label>
          <p class="listItem_title">静かさ</p>
          <select name = "s_r" class="listItem_input listItem_input--frame">
            <option value="" <?php if($s_rate =='') echo'selected';?>>ユーザー評価</option>
            <?php for ($i=1; $i < 6 ; $i++):?>
            <option value="<?php echo $i; ?>" <?php if($s_rate ==$i) echo'selected';?> ><?php echo $i.'点 以上'; ?></option>
            <?php endfor; ?>
          </select>
        </label>
      </li>
    </ul>
    <div class="sidebar_contents">
      <input type="submit" value="検索する" class="btn btn--submit searchBox_btn">
    </div>

  </form>
</aside>
