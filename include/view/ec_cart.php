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
<?php showProductInCart($db); ?>
<p>合計金額：<?php print $total; ?>円</p>
<?php if ($does_show_button): ?>
    <form action="./thankyou.php" method="post">
        <input type="submit" value="購入する">
    </form>
<?php endif; ?>