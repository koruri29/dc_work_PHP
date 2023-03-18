<a href="./product.php">商品一覧へ</a>
<br>
<h2>カート内商品</h2>
<?php
    if (! empty($error)) {
        foreach ($error as $error_msg) {
            print '<p class="error">' . $error_msg . '</p>';
        }
    }
?>
<form action="./cart.php" method="post">
    <?php showProductInCart($db); ?>
    <input type="hidden" name="product-num" value="<?php print $product_num; ?>">
    <?php if ($does_show_button): ?>
        <input type="submit" name="submit" value="数量変更">
    <?php endif; ?>
</form>
<p>合計金額：<?php print $total; ?>円</p>
<?php if ($does_show_button): ?>
    <form action="./thankyou.php" method="post">
        <input type="submit" value="購入する">
    </form>
<?php endif; ?>