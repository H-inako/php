<?php 
require("./dbconnect.php");
session_start();

$id=$_GET['id'];
//echo $id;

if(empty($id)){
    header('Location: thread.php'); 
    exit();
}

//スレッドの中身OK
$sql1="SELECT * FROM threads where id=:id";
$stmt = $db->prepare($sql1);
$stmt->bindValue(':id',$id, PDO::PARAM_STR);
$stmt->execute();
$thread_detail=$stmt->fetch();

//スレッド投稿者の名前取得OK
$member_id=$thread_detail['member_id'];
$sql2="SELECT * FROM members where id=:id";
$statement2=$db->prepare($sql2);
$statement2->bindValue(':id',$member_id, PDO::PARAM_INT);
$statement2->execute();
$thread_author=$statement2->fetch();
$name=$thread_author['name_sei'].'  '.$thread_author['name_mei'];

//コメントを取得して表示
$sql3 = "SELECT * FROM comments WHERE thread_id=:thread_id order by created_at desc";
$statement3 = $db->prepare($sql3);
$statement3->bindValue(':thread_id',$id, PDO::PARAM_INT);
$statement3->execute();
$thread_comment = $statement3->fetch();



//var_dump($thread_comment);
//var_dump($thread_author);
//var_dump($thread_detail);


//コメントのバリデーションOK
if(!empty($_POST['check'])){
    if($_POST['comment']== ''){
        $error['comment'] ='blank'; 
    }

    if(mb_strlen($_POST['comment']) > 500) {
        $error['comment'] = 'length';
    }

    if (!isset($error)) {
    //コメントをDBに登録OK
       $comment=$_POST['comment'];
       $sql="INSERT INTO comments(member_id,thread_id,comment,created_at,updated_at)
       VALUES(:member_id,:thread_id,:comment,:created_at,:updated_at)";
       $statement = $db->prepare($sql);
    
       $statement->bindParam(':member_id', $member_id,PDO::PARAM_INT);
       $statement->bindParam(':thread_id', $id,PDO::PARAM_INT);
       $statement->bindParam(':comment', $comment,PDO::PARAM_STR);
       $statement->bindParam(':created_at', $created_at,PDO::PARAM_STR);
       $statement->bindParam(':updated_at', $updated_at,PDO::PARAM_STR);
       $statement->execute();

       header("Location: thread_detail.php?id=$id");
       exit();
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
                <div class="comment-box">
                <?php while($thread_comment = $statement3->fetch()): ?>
                <?php $sql5="SELECT * FROM members where id=:id";
                    $statement5=$db->prepare($sql5);
                    $statement5->bindValue(':id',$thread_comment['member_id'], PDO::PARAM_INT);
                    $statement5->execute();
                    $comment_author=$statement5->fetch();
                    $comment_name=$comment_author['name_sei'].'  '.$comment_author['name_mei']; ?>
                <dt>
                <?php echo $thread_comment['id'].'  '.$comment_name.'  '.$thread_comment['created_at'];?><br>
                <?php echo nl2br($thread_comment['comment']) ?>
                </dt>
                <?php endwhile ?>
                </div>
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