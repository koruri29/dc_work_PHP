<body>
    <a href="./cart.php">カートを見る（<?php print countTotalProduct($db); ?>点）</a>
    <?php if (isset($_POST['search']) || $product = null): ?>
        <a href="./product.php">商品一覧へ</a>
        <h2>検索結果</h2>
        <?php
            if (! empty($msg)) {
                foreach ($msg as $message) {
                    print '<p class="msg">' . $message . '</p>';
                }
            }
        ?>
        <?php showPublicProduct($products); ?>
        <?php if (! empty($error)) print '<p class="error">' . $error . '</p>'; ?>
    <?php else: ?>
        <h2>商品一覧</h2>
        <?php
            if (! empty($msg)) {
                foreach ($msg as $message) {
                    print '<p class="msg">' . $message . '</p>';
                }
            }
        ?>
        <?php showPublicProduct($products); ?>
    <?php endif; ?>
    <script src="../../0006/js/search.js"></script>
	<script src="../../0006/js/product.js"></script>    