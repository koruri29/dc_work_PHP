<a href="./index.php">商品一覧へ</a>
<br>
<?php
showProductInCart($db);
?>
<form action="./thankyou.php" method="post">
    <input type="submit" value="購入する">
</form>