<a href="./product.php">商品一覧へ</a>
<br>
<h2>カート内商品</h2>
<div id="show-error">
    <?php
        if (! empty($error)) {
            foreach ($error as $error_msg) {
                print '<p class="error">' . $error_msg . '</p>';
            }
        }
    ?>
    <?php
        if (! empty($msg)) {
            foreach ($msg as $message) {
                print '<p class="msg">' . $message . '</p>';
            }
        }
    ?>
</div>
<form name="form1" action="./cart.php" method="post">
    <?php showProductInCart($db); ?>
    <input type="hidden" name="product-num" value="<?php print $product_num; ?>">
    <?php if ($show_change_btn): ?>
        <input id="qty-change" type="submit" name="send" value="数量変更">
    <?php endif; ?>
</form>
<p>合計金額：<?php print $total; ?>円</p>
<?php if ($show_purchase_btn): ?>
    <form name="form2" action="./thankyou.php" method="post">
        <input id="purchase" type="submit" value="購入する">
    </form>
<?php endif; ?>
<script src="../../0006/js/search.js"></script>
<script src="../../0006/js/cart.js"></script>