<?php
require("./dbconnect.php");
session_start();

if( !empty($_SESSION['page']) && $_SESSION['page'] === true ) {

    // セッションの削除
    unset($_SESSION['page']);
}

$sql = "SELECT * FROM threads order by created_at desc";
$statement = $db->prepare($sql);
$statement->execute();

$searchword= $_POST['search'];

if (!empty($_POST['search'])){

        $error['search']='true';
        $sql="SELECT * FROM threads where title like :title or content like :content order by created_at desc";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':title',"%{$searchword}%", PDO::PARAM_STR);
        $stmt->bindValue(':content',"%{$searchword}%", PDO::PARAM_STR);
        $stmt->execute();
    
}




?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>新規スレッド作成</title>
    <link rel="stylesheet" href="css/stylesheet2.css">
</head>
<body>
    <header>
        <div class="hwrapper">
        <ul class="bottun-list">
        <?php if(isset($_SESSION['id'])):?>
        <li class="bottun"><a class="btn" href="./thread_regist.php">新規スレッド作成</a></li>
        <?php endif ?>
        </ul>   
        </div>
    </header>
<main>
    <form action="" method="post">
    
    <div class="search-form">

            <input class="search-box" type="text" name="search" value="<?php if (!empty($_POST['search'])) { echo $_POST['search'];} ?>">
            <input class="search-bottun" type="submit" name="" value="スレッド検索">
        
    </div>
    <div class="thread">
    <?php if(empty($_POST['search'])): ?>
    <dl class="thread-box">
        <?php while($thread = $statement->fetch()): ?>
        <dt>
            ID: <?php echo $thread['id'].'  ';?>
            <a href="thread.php?id=<?php echo $thread['id'];?>"><?php echo $thread['title'];?></a>
            <?php echo '  '.$thread['created_at'];?>
        </dt>
        <?php endwhile ?>
    </dl>
    <?php endif ?>
    <?php if(!empty($_POST['search'])): ?>
    <dl class="thread-box">
        <?php while($searched = $stmt->fetch()): ?>
        <dt>
            ID: <?php echo $searched['id'].'  ';?>
            <a href="thread.php?id=<?php echo $searched['id'];?>"><?php echo $searched['title'];?></a>
            <?php echo '  '.$searched['created_at'];?>
        </dt>
        <?php endwhile ?>
    </dl>
    <?php endif ?>
    </div>
    <div>
        <button class="top" type="button" onclick="location.href='./top.php'">トップに戻る</button>
    </div>
    </form>
</main>
</body>


</html>
