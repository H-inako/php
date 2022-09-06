<?php
require("./dbconnect.php");
session_start();

if(empty($_SESSION['id'])){
    header('Location: top.php'); 
    exit();
}

//退会ボタンが押されたら
if(!empty($_POST['confirm'])){
    $sql="UPDATE members SET deleted_at=now() WHERE id=:id";
    $withdrawal=$db->prepare($sql);
    //$withdrawal->bindParam(':deleted_at',$deleted_at,PDO::PARAM_INT);
    $withdrawal->bindParam(':id', $_SESSION['id'],PDO::PARAM_INT);
    $withdrawal->execute();

    unset($_SESSION['id']); 
    header('Location: top.php');
    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>退会</title>
    <link rel="stylesheet" href="css/stylesheet2.css">
</head>
<body>
    <header>
        <div class="hwrapper">
        <ul class="bottun-list">
        <li class="bottun"><a class="btn" href="./top.php">トップへ戻る</a></li>
        </ul>   
        </div>
    </header>
<main>
    <h2>退会</h2>
    <p>退会しますか？</p>
    <form action="" method="post">
        <button class="top" type="submit">退会する</button>
        <input type="hidden" name="confirm" value="confirmed">
    </div>
    </form>
</main>
</body>


</html>
