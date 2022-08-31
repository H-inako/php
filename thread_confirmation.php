<?php 
require("./dbconnect.php");
session_start();

$_SESSION['page']=true;

$member_id=$_SESSION['id'];
$title=$_SESSION['thread']['title'];
$content=$_SESSION['thread']['content'];


if (!empty($_POST['confirm'])){

    $sql="INSERT INTO threads(member_id,title,content,created_at,updated_at)
    VALUES(:member_id,:title,:content,:created_at,:updated_at)";
    $statement = $db->prepare($sql);
  
    $statement->bindParam(':member_id', $member_id,PDO::PARAM_INT);
    $statement->bindParam(':title', $title,PDO::PARAM_STR);
    $statement->bindParam(':content', $content,PDO::PARAM_STR);
    $statement->bindParam(':created_at', $created_at,PDO::PARAM_STR);
    $statement->bindParam(':updated_at', $updated_at,PDO::PARAM_STR);

    $statement->execute();
           unset($_SESSION['thread']); 
           header('Location: thread.php');
           exit();

}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/stylesheet.css">
    <title>スレッド作成確認画面</title>
</head>
<body>
    <h2>スレッド作成確認画面</h2>
    <form  action="" method="post">
    <input type="hidden" name="confirm" value="confirmed">
    <div class="form_item">
        <p class="form_item_label">スレッドタイトル</p>
        <div class="form_item_input">
            <?php echo $_SESSION['thread']['title']  ?>
        </div>
    </div>
    <div class="form_item">
        <p class="form_item_label">コメント</p>
        <div class="form_item_input">
            <?php echo nl2br($_SESSION['thread']['content']) ?>
        </div>
    </div>

    <div>
        <input class="btn confirm" type="submit"  value="スレッドを作成する">
      </div>
      <div>
        <button class="btn former" type="button" onclick="history.back()">前に戻る</button>
      </div>
    </form>
</body>

</html>