<?php
require("./dbconnect.php");
require_once("./list.php");
session_start();

$_SESSION['page']=true;

$name_sei=$_SESSION['join']['name_sei'];
$name_mei=$_SESSION['join']['name_mei'];
$gender=$_SESSION['join']['gender'];
$address=$_SESSION['join']['address'];
$password=$_SESSION['join']['password'];
$email=$_SESSION['join']['email'];

$pref_num = $_SESSION['join']['pref_name'];
$pref_name = $prefNameList["$pref_num"];



 
if (!empty($_POST['confirm'])){

       
    $sql="INSERT INTO members(name_sei,name_mei,gender,pref_name,address,password,email,created_at,updated_at)
    VALUES(:name_sei,:name_mei,:gender,:pref_name,:address,:password,:email,:created_at,:updated_at)";
    $statement = $db->prepare($sql);
  
    $statement->bindParam(':name_sei', $name_sei,PDO::PARAM_STR);
    $statement->bindParam(':name_mei', $name_mei,PDO::PARAM_STR);
    $statement->bindParam(':gender', $gender,PDO::PARAM_INT);
    $statement->bindParam(':pref_name', $pref_name,PDO::PARAM_STR);
    $statement->bindParam(':address', $address,PDO::PARAM_STR);
    $statement->bindParam(':password', $password,PDO::PARAM_STR);
    $statement->bindParam(':email', $email,PDO::PARAM_STR);
    $statement->bindParam(':created_at', $created_at,PDO::PARAM_STR);
    $statement->bindParam(':updated_at', $updated_at,PDO::PARAM_STR);

    $statement->execute();
           unset($_SESSION['join']); 
           header('Location: registered.php');
           exit();

    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>会員情報確認</title>
    <link rel="stylesheet" href="css/stylesheet.css">
</head>

<body>
<h2>会員情報確認画面</h2>
<form action="" method="POST">
<input type="hidden" name="confirm" value="confirmed">
<div class="form_item">
    <p class="form_item_label">氏名</p><p class="form_item_input"><?php echo $_SESSION['join']['name_sei'].' '.$_SESSION['join']['name_mei'] ?></p>
</div>
<div class="form_item">
    <p class="form_item_label">住所</p><p class="form_item_input"><?php echo $pref_name.$_SESSION['join']['address']  ?></p>
</div>
<div class="form_item">
    <p class="form_item_label">パスワード</p><p class="form_item_input">セキュリティのため非表示</p>
</div>
<div class="form_item">
    <p class="form_item_label">メールアドレス</p><p class="form_item_input"><?php echo $_SESSION['join']['email'] ?></p>
</div>
    <input type="hidden" name="ticket" value="<?php echo $token;?>" >
<div>
    <input class="btn submit" type="submit" value="登録完了">
</div>
<div>
    <button class="btn former" type="button" onclick="history.back()">前に戻る</button>
</div>
</form>
</body>
</html>