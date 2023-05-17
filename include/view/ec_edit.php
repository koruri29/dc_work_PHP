<body>
    <?php if (isset($_POST['search'])) print '<a href="./edit.php">商品一覧を表示する</a>';?>
    <h2 class="h2">商品登録</h2>
    <?php if (! empty($msg_register)) print '<p class="msg">' . $msg_register . '</p>'; ?>

    <form id="register" name="register" action="./edit.php" method="post" enctype="multipart/form-data">
        商品名<input id="product-name" type="text" name="name"><br>
        価格<input id="price" type="number" name="price"><br>
        在庫数<input id="qty" type="number" name="qty"><br>
        公開ステータス：
        公開<input class="public-flag" type="radio" name="public_flag" value="1" checked>
        非公開<input class="public-flag" type="radio" name="public_flag" value="0"><br>
        画像ファイル<input class="image" type="file" name="image"><br>
        <input type="hidden" name="register" value="register">
        <div id="register-error">
            <?php
                if (! empty($error_register)) {
                    foreach ($error_register as $error) {
                        print '<p class="error">' . $error . '</p>';
                    }
                }
            ?>
        </div>
        <input id="register-product" name="send" type="submit" value="登録">
    </form>
    <div id="update-error">
        <?php
            if (! empty($msg_update)) {
                foreach ($msg_update as $msg) {
                    print '<p class="msg">' . $msg . '</p>';
                }
            }
        ?>
        <?php
            if (! empty($error_update)) {
                foreach ($error_update as $error) {
                    print '<p class="error">' . $error . '</p>';
                }
            }
        ?>
    </div>
    <form id="update" name ="update" action="./edit.php" method="post">
        <?php showProductData($db, $stmt); ?>
        <input type="hidden" name="product-num" value="<?php print $product_num; ?>">
        <input type="hidden" name="update" value="update">
        <input id="update-product" name="send" type="submit" value="設定を変更する">
    </form>
	<script src="../../0006/js/edit.js"></script>
	<script src="../../0006/js/search.js"></script>