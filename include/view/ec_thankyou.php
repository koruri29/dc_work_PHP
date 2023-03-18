<a href="./product.php">商品一覧へ</a>
<h2>購入完了</h2>
<?php
if (! empty($msg['thankyou'])) print $msg['thankyou'];

showPurchasedProducts($db, $stmt);
?>
<p>合計金額：<?php print $total; ?>円</p>
