<body>
    <h3>商品登録</h3>
    <?php if (! empty($msg_register)) print '<p class="msg">' . $msg_register . '</p>'; ?>

    <form action="./edit.php" method="post" enctype="multipart/form-data">
        商品名<input type="text" name="name"><br>
        価格<input type="number" name="price"><br>
        在庫数<input type="number" name="qty"><br>
        公開ステータス：
        公開<input type="radio" name="public_flag" value="1" checked>
        非公開<input type="radio" name="public_flag" value="0"><br>
        画像ファイル<input type="file" name="image"><br>
        <input type="hidden" name="register">
        <?php
            if (! empty($error_register)) {
                foreach ($error_register as $error) {
                    print '<p class="error">' . $error . '</p>';
                }
            }
        ?>
        <input type="submit" value="登録">
    </form>
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
    <form action="./edit.php" method="post">
        <?php showProductData($db); ?>
        <input type="hidden" name="product-num" value="<?php print $product_num; ?>">
        <input class="submit" name="submit" type="submit" value="設定を変更する">
    </form>
    <script>
        const item = document.getElementsByClassName('item');
        const displayFlag = document.getElementsByClassName('display-flag')
        const productId = document.getElementsByClassName('product-id');
        const submit = document.getElementsByClassName('submit');
        for (let i = 0; i < item.length; i++) {

        }
    </script>