<body>
    <a href="./cart.php">カートを見る（<?php print countTotalProduct($db); ?>点）</a>
    <?php if ($_POST['submit'] == '検索' || $product = null): ?>
        <a href="./product.php">商品一覧へ</a>
        <h2>検索結果</h2>
        <?php if (! empty($msg)) print '<p class="msg">' . $msg . '</p>'; ?>
        <?php showPublicProduct($products); ?>
        <?php if (! empty($error)) print '<p class="error">' . $error . '</p>'; ?>
    <?php else: ?>
        <h2>商品一覧</h2>
        <?php if (! empty($msg)) print '<p class="msg">' . $msg . '</p>'; ?>
        <?php showPublicProduct($products); ?>
    <?php endif; ?>