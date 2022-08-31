<?php
session_start();

if( !empty($_SESSION['page']) && $_SESSION['page'] === true ) {

    // セッションの削除
    unset($_SESSION['page']);
}

//ログイン状態の時
if(isset($_SESSION['id'])){
    $name= $_SESSION['name_sei'].' '.$_SESSION['name_mei'];
}



?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>トップページ</title>
    <link rel="stylesheet" href="css/stylesheet2.css">
</head>
<body>
    <header>
        <div class="hwrapper">
        <?php if(isset($_SESSION['id'])):?>
        <div class="greet">ようこそ<?php echo $name ;?>様</div>
        <?php endif ?>
        <ul class=bottun-list>
        <li class="bottun"><a class="btn" href="./thread.php">スレッド一覧</a></li>
        <?php if(!isset($_SESSION['id'])):?>
        <li class="bottun"><a class="btn" href="./member_regist.php" >新規会員登録</a></li>
        <li class="bottun"><a class="btn" href="./login.php">ログイン</a></li>
        <?php endif ?>
        <?php if(isset($_SESSION['id'])):?>
        <li class="bottun"><a class="btn" href="./thread_regist.php">新規スレッド作成</a></li>
        <li class="bottun"><a class="btn" href="./logout.php">ログアウト</a></li>
        <?php endif ?>
        </ul>   
        </div>
    </header>
    <main>
        <h1>セルバ掲示板</h1>
    </main>
</body>


</html>
