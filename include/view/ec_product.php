<body>
    <a href="./cart.php">カートを見る</a>
    <h2>商品一覧</h2>
    <?php if (! empty($msg)) print '<p class="msg">' . $msg . '</p>'; ?>
    <?php showPublicProduct($db); ?>