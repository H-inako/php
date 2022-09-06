<?php
require("../dbconnect.php");
session_start();

$email=$_POST['email'];

if(!empty($_POST['check'])){

    $alpha_id = "/^[a-zA-Z0-9]{7,10}$/";
    
    if($_POST['id']== ''){
        $error['id'] ='blank'; 
    }elseif(!preg_match($alpha_id,$_POST['id'])){
        $error['id']= 'format';
    }

    $alpha = "/^[a-zA-Z0-9]{8,20}$/";
    
    if($_POST['password']== ''){
        $error['password'] ='blank'; 
    }elseif(!preg_match($alpha,$_POST['password'])){
        $error['password']= 'format';
    }

    if (!isset($error)) {

        //ログインIDが登録されている
        $sql = "SELECT COUNT(*) AS cnt FROM administers WHERE login_id=?";
        $statement = $db->prepare($sql);
        $statement->execute(array($_POST['id']));
        $record = $statement->fetch();
       
        if($record['cnt'] > 0){
           //されていたら、パスワードが一致するか
           $sql = "SELECT * FROM administers WHERE login_id =:login_id";
           $statement = $db->prepare($sql);
           $statement->bindValue(':login_id',$_POST['id']);
           $statement->execute();
           $administer = $statement->fetch();
           
   
            if ($_POST['password'] === $administer['password']) {
                $_SESSION['id'] = $administer['id'];
                $_SESSION['name'] = $administer['name'];

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
    <title>管理者ログインページ</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body>
   <h2>管理画面</h2>
   <form  action="" method="post">
   <input type="hidden" name="check" value="checked">
    <div class="form_item">
        <p class="form_item_label">ログインID</p><input type="text" name="id" class="form_item_input form_item_input2" value="<?php if( !empty($_POST['id']) ){ echo $_POST['id']; } ?>">
    </div>
        <?php if(!empty($error["id"]) && $error['id'] == 'blank'): ?>
                <p class="error">※ログインIDを入力してください。</p>
        <?php elseif(!empty($error["id"]) && $error['id'] != 'blank' && $error['id'] == 'format'): ?>
                <p class="error">※ログインIDは半角英数字7〜10文字以内で入力してください。</p>
        <?php endif ?>
    <div class="form_item">
        <p class="form_item_label">パスワード</p><input type="password" name="password" class="form_item_input form_item_input2">
    </div>
        <?php if(!empty($error["password"]) && $error['password'] == 'blank'): ?>
                <p class="error">※パスワードを入力してください。</p>
        <?php elseif(!empty($error["password"]) && $error['password'] != 'blank' && $error['password'] == 'format'): ?>
                <p class="error">※パスワードは半角英数字8～20文字以内で入力してください。</p>
        <?php endif ?>
        <?php if(!empty($_POST['check']) && $error['login'] == 'fail'): ?>
                <p class="error">※IDもしくはパスワードが間違っています。</p>
        <?php endif ?>
    <div>
        <input class="btn submit" type="submit" value="ログイン">
    </div>
   </form>
</body>


</html>