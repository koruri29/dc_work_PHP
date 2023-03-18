<body>
    <a href="./cart.php">カートを見る（<?php print countTotalProduct($db); ?>点）</a>
    <h2>商品一覧</h2>
    <?php if (! empty($msg)) print '<p class="msg">' . $msg . '</p>'; ?>
    <?php showPublicProduct($db); ?>