<?php
require("./dbconnect.php");
session_start();

$email=$_POST['email'];

if(!empty($_POST['check'])){

    if($_POST['email']== ''){
        $error['email'] ='blank'; 
    }

    if($_POST['password']== ''){
        $error['password'] ='blank'; 
    }

    if ($error['email'] !='blank' && $error['password'] !='blank') {

        //メールアドレスがDBに登録されているかつ、deleted_atがnull
        $sql = "SELECT COUNT(*) AS cnt FROM members WHERE email=? and deleted_at is null";
        $statement = $db->prepare($sql);
        $statement->execute(array($_POST['email']));
        $record = $statement->fetch();
       
        if($record['cnt'] > 0){
           //されていたら、パスワードが一致するか
           $sql = "SELECT * FROM members WHERE email =:email";
           $statement = $db->prepare($sql);
           $statement->bindValue(':email',$email);
           $statement->execute();
           $member = $statement->fetch();
           
   
            if ($_POST['password'] === $member['password']) {
                $_SESSION['id'] = $member['id'];
                $_SESSION['name_sei'] = $member['name_sei'];
                $_SESSION['name_mei'] = $member['name_mei'];

                header('Location:top.php');
                exit();
            } else{
                $error['login']='fail';
            }
        }else{
            $error['login']='fail';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ログインページ</title>
    <link rel="stylesheet" href="css/stylesheet.css">
</head>
<body>
   <h2>ログイン</h2>
   <form  action="" method="post">
   <input type="hidden" name="check" value="checked">
    <div class="form_item">
        <p class="form_item_label">メールアドレス（ID）</p><input type="text" name="email" class="form_item_input form_item_input2" value="<?php if( !empty($_POST['email']) ){ echo $_POST['email']; } ?>">
    </div>
        <?php if(!empty($error["email"]) && $error['email'] == 'blank'): ?>
                <p class="error">※メールアドレスを入力してください。</p>
        <?php endif ?>
    <div class="form_item">
        <p class="form_item_label">パスワード</p><input type="password" name="password" class="form_item_input form_item_input2">
    </div>
        <?php if(!empty($error["password"]) && $error['password'] == 'blank'): ?>
                <p class="error">※パスワードを入力してください。</p>
        <?php endif ?>
        <?php if(!empty($_POST['check']) && $error['login'] == 'fail'): ?>
                <p class="error">※IDもしくはパスワードが間違っています。</p>
        <?php endif ?>
    <div>
        <input class="btn submit" type="submit" value="ログイン">
    </div>
    <div>
        <button class="btn top" type="button" onclick="location.href='./top.php'">トップに戻る</button>
    </div>
   </form>
</body>


</html>