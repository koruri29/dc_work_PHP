<a href="./index.php">商品一覧へ</a>
<h2>購入完了</h2>
<?php
if (! empty($msg['thankyou'])) print $msg['thankyou'];

showPurchasedProducts($db, $cart_id);