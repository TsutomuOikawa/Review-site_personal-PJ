<?php
debug('ページネーション');
// ページネーションに表示する項目数。ここを変えるだけでOK
$listItemNum = 5;

// トータルページが表示数以上の場合
if ($dbInstList['total_page'] >= $listItemNum) {
  // 1P〜表示数の半分までのPでは、一番左が1からlistItemNum数のアイテムを表示で固定。
  if ($currentPageNum <= ceil($listItemNum /2)) {
    $maxPageNum = $listItemNum;
    $minPageNum = 1;
  // アイテムが変動するエリア。$listItemNumが偶数の場合は、12(3)456のように表示。
}elseif (ceil($listItemNum /2) < $currentPageNum && $currentPageNum < ($dbInstList['total_page'] - floor($listItemNum /2))) {
    $maxPageNum = $currentPageNum + floor($listItemNum /2);
    $minPageNum = $maxPageNum - $listItemNum + 1;
  // 表示数の終わりの半分以降~最終Pまでは、一番右を最終ページで固定
  }elseif ($currentPageNum >= floor($listItemNum /2)) {
    $maxPageNum = $dbInstList['total_page'];
    $minPageNum = $maxPageNum - $listItemNum + 1;
  }
// その他の場合
// ページネーションの数字は変動しない
}else {
  $maxPageNum = $dbInstList['total_page'];
  $minPageNum = 1;
}

?>
<div class="pagination">
  <ul class="pagination_list">
  <?php if ($currentPageNum != 1): ?>
    <li class="pagination_item sp-delete"><a href="?p=<?php echo 1; echo $link;?>">&lt;&lt;</a></li>
    <li class="pagination_item"><a href="?p=<?php echo ($currentPageNum -1).$link; ?>">&lt; 前へ</a></li>
  <?php endif; ?>
  <?php for ($i = $minPageNum; $i <= $maxPageNum ; $i++):?>
    <li class="pagination_item <?php if($currentPageNum == $i) echo 'active'; ?>"><a href="?p=<?php echo $i.$link; ?>"><?php echo $i; ?></a></li>
  <?php endfor; ?>
  <?php if($currentPageNum != $dbInstList['total_page']): ?>
    <li class="pagination_item"><a href="?p=<?php echo ($currentPageNum +1).$link; ?>">次へ &gt;</a></li>
    <li class="pagination_item sp-delete"><a href="?p=<?php echo $dbInstList['total_page'].$link; ?>">&gt;&gt;</a></li>
  <?php endif; ?>
  </ul>
</div>
