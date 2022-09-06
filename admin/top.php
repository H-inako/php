<?php
session_start();

if(isset($_SESSION['id'])){
    $name= $_SESSION['name'];
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>管理者管理画面メインメニュー</title>
    <link rel="stylesheet" href="../css/stylesheet3.css">
</head>
<body>
    <header>
        <div class="hwrapper">
        <?php if(isset($_SESSION['id'])):?>
        <div class="menu">
            <h2>管理者管理画面メインメニュー</h2>  
        </div>
        <div class="greet">
        ようこそ<?php echo $name ;?>さん
        </div>
        <ul class=bottun-list>
        <li class="bottun"><a class="btn" href="./logout.php">ログアウト</a></li>
        </ul> 
        <?php endif ?>
        </div>
    </header>
    <main>
    </main>
</body>
<footer>

</footer>

</html>
