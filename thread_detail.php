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
$member_id=$thread_detail['member_id'];

//メンバーとコメントテーブルを結合し、該当のスレッドIDを持つレコードを取得
$sql3 = 
'SELECT 
comments.id as id,
comments.comment as comment,
comments.created_at as created_at,
members.name_sei as name_sei,
members.name_mei as name_mei
FROM comments INNER JOIN members ON comments.member_id = members.id WHERE comments.thread_id=:thread_id order by comments.created_at desc';
    $stmt3 = $db->prepare($sql3);
    $stmt3->bindValue(':thread_id',$id, PDO::PARAM_INT);
    $stmt3->execute();

$comments=array();
while($row = $stmt3->fetch(PDO::FETCH_ASSOC)){
 $comments[]=array(
 'comment' =>$row['comment'],
 'comment_id' =>$row['id'],
 'name_sei'=>$row['name_sei'],
 'name_mei'=>$row['name_mei'],
 'created_at'=>$row['created_at']
 );
}
//コメントの数を取得OK
$sql4 = "SELECT COUNT(*) AS cnt FROM comments WHERE thread_id=:thread_id";
$statement4 = $db->prepare($sql4);
$statement4->bindValue(':thread_id',$id, PDO::PARAM_INT);
$statement4->execute();
$record = $statement4->fetch();
$comment_count=$record['cnt'];


//ページング
$num=5;//表示するコメント件数
$totalPages=ceil($comment_count/$num);

//指定件数ごとにコメント配列を分割
$comments=array_chunk($comments,$num);
$page=1;
if(isset($_GET['page']) && is_numeric($_GET['page'])){
    $page=intval($_GET['page']);
    if(!isset($comments[$page-1])){
          $page=1;
        }
    }


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
       $comment_content=$_POST['comment'];
       $sql="INSERT INTO comments(member_id,thread_id,comment,created_at,updated_at)
       VALUES(:member_id,:thread_id,:comment,:created_at,:updated_at)";
       $statement = $db->prepare($sql);
    
       $statement->bindParam(':member_id', $member_id,PDO::PARAM_INT);
       $statement->bindParam(':thread_id', $id,PDO::PARAM_INT);
       $statement->bindParam(':comment', $comment_content,PDO::PARAM_STR);
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
            <h3>[<?php echo $comment_count ?>コメント]</h3>
        </div>
        <div class="paging">
                <?php if ($page > 1) : ?>
	                <a href="./thread_detail.php?id=<?php echo $id;?>&page=<?php echo $page-1; ?>">前へ</a>
                <?php else: ?>
                    <span>前へ</span>
                <?php endif; ?>
                <?php if ($page < $totalPages) : ?>
                    <a href="./thread_detail.php?id=<?php echo $id;?>&page=<?php echo $page+1; ?>">次へ</a>
                <?php else: ?>
                    <span>次へ</span>
                <?php endif; ?>
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
                <?php if($comment_count>0): ?>
                <?php foreach($comments[$page-1] as $comment): ?>
                <dt>
                <?php echo $comment['comment_id'].'  '.$comment['name_sei'].' '.$comment['name_mei'].'  '.$comment['created_at'];?><br>
                <?php echo nl2br($comment['comment']); ?>
                </dt>
                <?php endforeach; ?>
                <?php else: ?>
                    <p>コメントはまだありません</P>
                <?php endif ?>
                </div>
                <div class="paging">
                <?php if ($page > 1) : ?>
	                <a href="./thread_detail.php?id=<?php echo $id;?>&page=<?php echo $page-1; ?>">前へ</a>
                <?php else: ?>
                    <span>前へ</span>
                <?php endif; ?>
                <?php if ($page < $totalPages) : ?>
                    <a href="./thread_detail.php?id=<?php echo $id;?>&page=<?php echo $page+1; ?>">次へ</a>
                <?php else: ?>
                    <span>次へ</span>
                <?php endif; ?>
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