<!--　検索サイドバー　-->
<aside id="sidebar" class="sidebar sidebar--left">
  <form>
    <p class="sidebar_title js-accordion-switch">基本条件</p>
    <ul class="sidebar_contents js-accordion-area">
      <li class="sidebar_listItem">
        <label>
          <p class="form_name">エリア</p>
          <input type="text" name="ar" class="form_input form_input--sidebar" value="<?php echo getFormData('ar', 0); ?>" placeholder="新宿、渋谷">
        </label>
      </li>
      <li class="sidebar_listItem">
        <label>
          <p class="form_name">施設タイプ</p>
          <select name=ty class="form_input form_input--sidebar">
            <option value="" <?php if (empty($type)) echo 'selected';?> >選択してください</option>
            <?php foreach ($dbTypeData as $key => $val): ?>
            <option value="<?php echo $val['id'];?>" <?php if($type == $val['id']) echo 'selected';?> ><?php echo $val['name']; ?></option>
            <?php endforeach; ?>
          </select>
        </label>
      </li>
      <li class="sidebar_listItem">
        <label>
          <p class="form_name">利用目的</p>
          <select name="pu" class="form_input form_input--sidebar">
            <option value="" <?php if (empty($purpose)) echo 'selected'; ?>>選択してください</option>
            <?php foreach ($dbPurposeData as $key => $value): ?>
            <option value="<?php echo $value['id']; ?>" <?php if(!empty($_GET) && $purpose == $value['id']) echo 'selected';?> ><?php echo $value['name']; ?></option>
          <?php endforeach; ?>
          </select>
        </label>
      </li>
    </ul>
    <p class="sidebar_title js-accordion-switch2 close">こだわり条件</p>
    <ul class="sidebar_contents js-accordion-area close">
      <li class="sidebar_listItem">
        <label>
          <p class="form_name">コンセント</p>
          <select name="c" class="form_input form_input--sidebar" style="border-bottom:dashed #BFBFB3 2px;">
            <option value="" <?php if($concent=='') echo'selected';?>>あり or なし</option>
            <option value="1" <?php if($concent==='1') echo'selected';?> >あり</option>
            <option value="0" <?php if($concent==='0') echo'selected';?> >なし</option>
          </select>
          <select name = "c_r" class="form_input form_input--sidebar">
            <option value="" <?php if($c_rate=='') echo'selected';?>>ユーザー評価</option>
            <?php for ($i=1; $i < 6 ; $i++):?>
            <option value="<?php echo $i; ?>" <?php if($c_rate==$i) echo'selected';?> ><?php echo $i.'点 以上'; ?></option>
            <?php endfor; ?>
          </select>
        </label>
      </li>
      <li class="sidebar_listItem">
        <label>
          <p class="form_name">Wi-Fi</p>
          <select name="w" class="form_input form_input--sidebar" style="border-bottom:dashed #BFBFB3 2px;">
            <option value="" <?php if($wifi==='') echo'selected';?>>あり or なし</option>
            <option value="1" <?php if($wifi==='1') echo'selected';?> >あり</option>
            <option value="0" <?php if($wifi==='0') echo'selected';?> >なし</option>
          </select>
          <select name = "w_r" class="form_input form_input--sidebar">
            <option value="" <?php if($w_rate =='') echo'selected';?>>ユーザー評価</option>
            <?php for ($i=1; $i < 6 ; $i++):?>
            <option value="<?php echo $i; ?>" <?php if($w_rate ==$i) echo'selected';?> ><?php echo $i.'点 以上'; ?></option>
            <?php endfor; ?>
          </select>
        </label>
      </li>
      <li class="sidebar_listItem">
        <label>
          <p class="form_name">静かさ</p>
          <select name = "s_r" class="form_input form_input--sidebar">
            <option value="" <?php if($s_rate =='') echo'selected';?>>ユーザー評価</option>
            <?php for ($i=1; $i < 6 ; $i++):?>
            <option value="<?php echo $i; ?>" <?php if($s_rate ==$i) echo'selected';?> ><?php echo $i.'点 以上'; ?></option>
            <?php endfor; ?>
          </select>
        </label>
      </li>
    </ul>
    <div class="sidebar_contents">
      <button type="submit" class="btn btn--submit btn--sidebar">
        <i class="fa-solid fa-magnifying-glass" style="padding-right:8px;"></i>検索する
      </button>
    </div>

  </form>
</aside>
