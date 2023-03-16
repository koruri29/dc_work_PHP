<a href="./product.php">商品一覧へ</a>
<br>
<h2>カート内商品</h2>
<?php
showProductInCart($db);
?>
<form action="./thankyou.php" method="post">
    <input type="submit" value="購入する">
</form>