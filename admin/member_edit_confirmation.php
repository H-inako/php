<?php
require("../dbconnect.php");
require_once("../list.php");
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

    if($password==''){
    $sql="UPDATE members SET name_sei = :name_sei ,name_mei = :name_mei,gender = :gender,pref_name = :pref_name,address =:address,email =:email,updated_at=now() WHERE id = :id";
  
    $statement = $db->prepare($sql);
  
    $statement->bindParam(':name_sei', $name_sei,PDO::PARAM_STR);
    $statement->bindParam(':name_mei', $name_mei,PDO::PARAM_STR);
    $statement->bindParam(':gender', $gender,PDO::PARAM_INT);
    $statement->bindParam(':pref_name', $pref_name,PDO::PARAM_STR);
    $statement->bindParam(':address', $address,PDO::PARAM_STR);
    $statement->bindParam(':email', $email,PDO::PARAM_STR);
    $statement->bindParam(':id', $_SESSION['id'],PDO::PARAM_STR);

    $statement->execute();
           unset($_SESSION['join']);
           unset($_SESSION['id']);
           header('Location: member.php');
           exit();
    }else{

    $sql="UPDATE members SET name_sei = :name_sei ,name_mei = :name_mei,gender = :gender, pref_name = :pref_name, address =:address, password = :password, email =:email, updated_at=now() WHERE id = :id";
  
    $statement = $db->prepare($sql);
  
    $statement->bindParam(':name_sei', $name_sei,PDO::PARAM_STR);
    $statement->bindParam(':name_mei', $name_mei,PDO::PARAM_STR);
    $statement->bindParam(':gender', $gender,PDO::PARAM_INT);
    $statement->bindParam(':pref_name', $pref_name,PDO::PARAM_STR);
    $statement->bindParam(':address', $address,PDO::PARAM_STR);
    $statement->bindParam(':password', $password,PDO::PARAM_STR);
    $statement->bindParam(':email', $email,PDO::PARAM_STR);
    $statement->bindParam(':id', $_SESSION['id'],PDO::PARAM_STR);

    $statement->execute();
           unset($_SESSION['join']);
           unset($_SESSION['id']);
           header('Location: member.php');
           exit();

    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>会員編集</title>
    <link rel="stylesheet" href="../css/stylesheet4.css">
</head>

<body>
<header>
<div class="hwrapper">
    <h2>会員編集</h2>
    <ul class="bottun-list">
    <li class="bottun"><a class="back-bottun" href="./member.php">一覧へ戻る</a></li>
    </ul>   
</div>
</header>
<main>
<form action="" method="POST">
<input type="hidden" name="confirm" value="confirmed">
<div class="form_item"> 
    <P class="form_item_label">ID</P>
    <div class="form_item_input"><span><?php echo $_SESSION['id']; ?></span></div>
</div>
<div class="form_item">
    <p class="form_item_label">氏名</p><p class="form_item_input"><?php echo $_SESSION['join']['name_sei'].' '.$_SESSION['join']['name_mei'] ?></p>
</div>
<div class="form_item">
    <p class="form_item_label">住所</p><p class="form_item_input"><?php echo $pref_name.$_SESSION['join']['address']  ?></p>
</div>
<div class="form_item">
<?php if($password==''): ?>
    <p class="form_item_label">パスワード</p><p class="form_item_input"></p>
<?php else: ?>
    <p class="form_item_label">パスワード</p><p class="form_item_input">セキュリティのため非表示</p>
<?php endif; ?>
</div>
<div class="form_item">
    <p class="form_item_label">メールアドレス</p><p class="form_item_input"><?php echo $_SESSION['join']['email'] ?></p>
</div>
    <input class="btn submit" type="submit" value="編集完了">
<div>
    <button class="btn former" type="button" onclick="history.back()">前に戻る</button>
</div>
</form>
</main>
</body>
</html>