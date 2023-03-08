<body>
    <h3>商品登録</h3>
    <?php if (!empty($msg['inserted'])) print '<p class="msg">' . $msg['inserted'] . '</p>'; ?>
    <form action="./product.php" method="post" enctype="multipart/form-data">
        商品名<input type="text" name="name"><br>
        価格<input type="number" name="price"><br>
        在庫数<input type="number" name="qty"><br>
        公開ステータス：
        公開<input type="radio" name="public_flag" value="1" checked>
        非公開<input type="radio" name="public_flag" value="0"><br>
        画像ファイル<input type="file" name="image"><br>
        <?php
            if (!empty($error)) {
                foreach ($error as $key => $val) {
                    print '<p class="error">' . $val . '</p>';
                }
            }
        ?>
        <input type="submit" value="登録">
    </form>
<?php showProductData($db); ?>
