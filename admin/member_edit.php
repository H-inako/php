<?php
require("../dbconnect.php");
require_once("../list.php");
session_start();

$id=$_GET['id'];

//会員情報取得
$sql1="SELECT * FROM members where id=:id";
$stmt = $db->prepare($sql1);
$stmt->bindValue(':id',$id, PDO::PARAM_INT);
$stmt->execute();
$member=$stmt->fetch();


$formInputItems = array(
    'name_sei',
    'name_mei',
    'gender',
    'email'
);

if(!empty($_POST['check'])){


    foreach($formInputItems as $formInputItem){
        if($_POST[$formInputItem] == ''){
            $error[$formInputItem]= 'blank';
            }else{
                $error[$formInputItem]!= 'blank';   
            }
        }

    
    if($_POST['gender'] !== "0" && $_POST['gender'] !== "1" && $error['gender'] != 'blank') {
            $error['gender'] = 'invalid';
    }
    
    if(mb_strlen($_POST['name_sei']) > 20) {
        $error['name_sei'] = 'length';
    }
  
    if(mb_strlen($_POST['name_mei']) > 20) {
        $error['name_mei'] = 'length';
    }

    if($_POST['pref_name']== ''){
        $error['pref_name'] ='blank'; 
    }
    elseif((int)$_POST['pref_name']< 1 || (int)$_POST['pref_name']> 47){
        $error['pref_name']='invalid';
    }

    if(mb_strlen($_POST['address']) > 100) {
        $error['address'] = 'length';
    }

    $alpha = "/^[a-zA-Z0-9]{8,20}$/";
    
   
    if(!empty($_POST['password']) && !preg_match($alpha,$_POST['password'])){
        $error['password']= 'format';
    }


    if(!empty($_POST['pass2']) && !preg_match($alpha,$_POST['pass2'])){
        $error['pass2']= 'format';
    }
    

    if($_POST['password'] != $_POST['pass2']){
        $error['password'] = 'mismatch';
    }

    if(strlen($_POST['email']) > 200) {
        $error['email'] = 'length';
    }
   
    //メールアドレス重複チェック
    if (!isset($error)) {
        if($_POST['email'] != $member['email']){
        $sql = "SELECT COUNT(*) AS cnt FROM members WHERE email=?";
        $statement = $db->prepare($sql);
        $statement->execute(array($_POST['email']));
        $record = $statement->fetch();
        if($record['cnt'] > 0) {
            $error['email'] = 'duplicate';
        }
        }
    }

    $emailPattern = "/^[0-9a-z_.\/?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$/";

    if (!preg_match($emailPattern, $_POST['email'] ) && $error['email'] != 'blank') {
        $error['email'] = 'format';
    }

    if (!isset($error)) {
        $_SESSION['join'] = $_POST;
        $_SESSION['id']=$id; 
        header('Location: member_edit_confirmation.php'); 
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
    <div class="form">
    <form  action="" method="post">
    <input type="hidden" name="check" value="checked">
    <div class="form_item"> 
        <P class="form_item_label">ID</P>
        <div class="form_item_input"><span><?php echo $id; ?></span></div>
    </div>
    <div class="form_item">
        <p class="form_item_label">氏名</p>
        <div class="form_item_input">
            <ul>
                <li>姓<input class="name" type="text" name="name_sei" value="<?php if( !empty($_POST['name_sei']) ){ echo $_POST['name_sei']; }else{ echo $member['name_sei'];} ?>" ></li>
                <li>名<input class="name" type="text" name="name_mei" value="<?php if( !empty($_POST['name_mei']) ){ echo $_POST['name_mei']; }else{ echo $member['name_mei'];} ?>"></li>
            </ul>   
        </div>  
    </div>
            <?php if(!empty($error["name_sei"]) && $error['name_sei'] == 'blank'): ?>
                <p class="error">※氏名（姓）は必須入力です。</p>
            <?php elseif(!empty($error["name_sei"]) && $error['name_sei'] == 'length'): ?>
                <p class="error">※氏名（姓）は20文字以内で入力してください。</p>
            <?php endif ?>
            <?php if(!empty($error["name_mei"]) && $error['name_mei'] == 'blank'): ?>
                <p class="error">※氏名（名）は必須入力です。</p>
            <?php elseif(!empty($error["name_mei"]) && $error['name_mei'] = 'length'): ?>
                <p class="error">※氏名（名）は20文字以内で入力してください。</p>
            <?php endif ?> 
    <div class="gender form_item">
        <p class="form_item_label">性別</p>
        <div class="form_item_input">   
            <input type="radio" name="gender" value="0" <?php if( !empty($_POST['gender']) && $_POST['gender'] == "0" ){ echo 'checked'; }elseif($member['gender']=="0"){ echo 'checked';} ?>> 男性
            <input type="radio" name="gender" value="1" <?php if( !empty($_POST['gender']) && $_POST['gender'] == "1" ){ echo 'checked'; }elseif($member['gender']=="1"){ echo 'checked';} ?>> 女性
        </div>
    </div>
        <?php if(!empty($error["gender"]) && $error['gender'] == 'blank'): ?>
                <p class="error" >※性別は必須入力です。</p>
        <?php elseif(!empty($error["gender"]) && $error['gender'] == 'invalid'): ?>
                <p class="error" >※この選択は有効ではありません。</p>
        <?php endif ?>
    <div class="address form_item">
        <p class="form_item_label">住所</p>
        <div class="form_item_input">
        <p>都道府県
        <select name="pref_name" >
        <?php
            foreach($prefNameList as $key => $value){
            if($key == $_POST['pref_name']){
            echo "<option value='$key' selected>".$value."</option>";
            }elseif($value == $member['pref_name']){
            echo "<option value='$key' selected>".$value."</option>";
            }else{
            echo "<option value='$key'>".$value."</option>";
            }
            }      
        ?>
          </select>
      </p>
      <p>それ以降の住所<input type="text" name="address" class="form_item_input" value="<?php if( !empty($_POST['address']) ){ echo $_POST['address']; }else{ echo $member['address'];} ?>"></p>
        </div>
    </div>
    <?php if(!empty($error["pref_name"]) && $error['pref_name'] == 'blank'): ?>
            <p class="error">※都道府県は必須入力です。</p>
    <?php elseif(!empty($error["pref_name"]) && $error['pref_name'] == 'invalid'): ?>
            <p class="error" >※この選択は有効ではありません。</p>
    <?php endif ?>
    <?php if(!empty($error["address"]) && $error['address'] = 'length'): ?>
                <p class="error">※住所は100文字以内で入力してください。</p>
    <?php endif ?> 
    <div>
        <div class="form_item">
            <p class="form_item_label">パスワード</p><input type="password" name="password" class="form_item_input form_item_input2" value="<?php if( !empty($_POST['password']) ){ echo $_POST['password']; }elseif(empty($_POST['password'])){ echo $member['password'];} ?>" >
        </div> 
        <?php if(!empty($error["password"]) && $error['password'] == 'format'): ?>
                <p class="error">※パスワードは半角英数字8～20文字以内で入力してください。</p>
        <?php endif ?> 
        <div class="form_item">
            <p class="form_item_label">パスワード確認</p><input type="password" name="pass2" class="form_item_input form_item_input2" value="<?php if( !empty($_POST['pass2']) ){ echo $_POST['pass2']; }elseif(empty($_POST['pass2'])){ echo $member['password'];} ?>" >
        </div>
        <?php if(!empty($error["pass2"])  && $error['pass2']== 'format'): ?>
                <p class="error">※パスワード確認は半角英数字8～20文字以内で入力してください。</p>
        <?php endif ?>
        <?php if(!empty($_POST["pass2"]) && $error['password'] == 'mismatch' ): ?>
                <p class="error">※パスワードが一致しません。</p>
        <?php endif ?>
    </div>
    <div class="email form_item">
      <p class="form_item_label">メールアドレス</p><input type="text" name="email" class="form_item_input form_item_input2" value="<?php if( !empty($_POST['email']) ){ echo $_POST['email']; }else{ echo $member['email'];} ?>">    
    </div>
      <?php if(!empty($error["email"]) && $error['email'] == 'blank'): ?>
                <p class="error">※メールアドレスは必須入力です。</p>
      <?php elseif(!empty($error["email"]) && $error['email'] == 'format'): ?>
                <p class="error">※不正な形式のメールアドレスです。</p>
      <?php elseif(!empty($error["email"]) &&  $error['email'] == 'length'): ?>
                <p class="error">※メールアドレスは200文字以内で入力してください。</p>
      <?php elseif(!empty($error["email"]) && $error['email'] == 'duplicate'): ?>
                <p class="error">※このメールアドレスは既に登録されています。</p>
      <?php endif ?>  
    <div>
      <input class="btn confirm" type="submit"  value="確認画面へ">
    </div>
    </form>
</div>
      </main>
  </body>
</html>