<nav class="global-nav">
    <ul>
        <li class="nav-item">
            ようこそ　<?php if (isset($_SESSION['user_name'])) print $_SESSION['user_name'] . '様'; ?>
        </li>

        <?php if (isset($_SESSION['user_name'])): ?>
            <li class="nav-item">
                <?php if ($_SERVER['REQUEST_URI'] == '/omiya/0006/ec_site/edit.php'):?>
                    <form name="form" action="./edit.php" method="post">
                <?php else:?>
                    <form name="form" action="./product.php" method="post">
                <?php endif;?>
                    <input type="search" name="search" placeholder="商品名">
                    <input type="radio" name="and-or" value="and" checked>and検索
                    <input type="radio" name="and-or" value="or">or検索
                    <button id="search" type="submit" name="search">検索</button>
                </form>
            </li>
            <li class="nav-item">
                <form action="./logout.php">
                    <input type="submit" value="ログアウト">
                </form>
            </li>
        <?php endif ;?>
    </ul>
</nav>