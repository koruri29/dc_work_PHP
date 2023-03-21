<nav class="global-nav">
    <ul>
        <li class="nav-item">
            ようこそ　<?php if (isset($_SESSION['user_name'])) print $_SESSION['user_name'] . '様'; ?>
        </li>

        <?php if (isset($_SESSION['user_name'])): ?>
            <li class="nav-item">
                <form action="./product.php" method="post">
                    <input type="search" name="search">
                    <input type="radio" name="and-or" value="and" checked>and検索
                    <input type="radio" name="and-or" value="or">or検索
                    <input type="submit" name="submit" value="検索">
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