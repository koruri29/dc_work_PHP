<a href="./product.php">商品一覧へ</a>
<h2>購入完了</h2>
<?php
if (! empty($msg['thankyou'])) print '<p class="msg">' . $msg['thankyou'] . '</p>';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    showPurchasedProducts($stmt);
}
?>
<p>合計金額：<?php print $total; ?>円</p>
<script src="../../0006/js/search.js"></script>