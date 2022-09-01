<?php 
require("./dbconnect.php");
session_start();

$id=$_GET['id'];

if(empty($id)){
    header('Location: thread.php'); 
    exit();
}

//スレッドの中身
$sql="SELECT * FROM threads where id=:id";
$stmt = $db->prepare($sql);
$stmt->bindValue(':id',$id, PDO::PARAM_STR);
$stmt->execute();
$thread_detail=$stmt->fetch();

//スレッド投稿者の名前取得
$member_id=$thread_detail['member_id'];
$sql2="SELECT * FROM members where id=:id";
$statement=$db->prepare($sql2);
$statement->bindValue(':id',$member_id, PDO::PARAM_INT);
$statement->execute();
$thread_author=$statement->fetch();
$name=$thread_author['name_sei'].'  '.$thread_author['name_mei'];

//var_dump($thread_author);
//var_dump($thread_detail);


//コメントのバリデーション
if(!empty($_POST['check'])){
    if($_POST['comment']== ''){
        $error['comment'] ='blank'; 
    }

    if(mb_strlen($_POST['comment']) > 500) {
        $error['comment'] = 'length';
    }

    if (!isset($error)) {
        //コメントをDBに登録
        
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>スレッド詳細</title>
    <link rel="stylesheet" href="css/stylesheet2.css">
</head>
<body>
    <header>
        <div class="hwrapper">
        <ul class="bottun-list">
        <li class="bottun"><a class="btn" href="./thread.php">スレッド一覧</a></li>
        </ul>   
        </div>
    </header>
    <main>
        <div class="index-item">
            <h2><?php echo $thread_detail['title'] ?></h2>
            <h3><?php echo $thread_detail['created_at'] ?></h3>
        </div>
        <div class="index-item">
                <dl class="index-box">
                    <dt class="index-title">
                        投稿者：<?php echo ' '.$name.'  '.$thread_detail['created_at'] ?>
                    </dt>
                    <dd class="index-content">
                        <?php echo nl2br($thread_detail['content']) ?>
                    </dd>
                </dl>
        </div>
        <?php if(isset($_SESSION['id'])):?>
        <form action="" method="post">
        <input type="hidden" name="check" value="checked">
            <textarea class="comment" name="comment"><?php if( !empty($_POST['comment']) ){ echo $_POST['comment']; } ?></textarea>
        <div>
        <?php if(!empty($error["comment"]) && $error['comment'] == 'blank'): ?>
                <p class="error" >※コメントを入力してください。</p>
        <?php elseif(!empty($error["comment"]) &&  $error['comment'] == 'length'): ?>
                <p class="error">※コメントは500文字以内で入力してください。</p>
        <?php endif ?>
            <input class="comment-bottun" type="submit"  value="コメントする">
        </div>
        </form>
        <?php endif ?>
    </main>
    
</body>





</html>