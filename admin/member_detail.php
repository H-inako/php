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

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>会員詳細</title>
        <link rel="stylesheet" href="../css/stylesheet4.css">
        
    </head>
  <body>
  <header>
        <div class="hwrapper">
            <h2>会員詳細</h2>
        <ul class="bottun-list">
        <li class="bottun"><a class="back-bottun" href="./member.php">一覧へ戻る</a></li>
        </ul>   
        </div>
    </header>
    <main>
    <div class="form">
    <input type="hidden" name="check" value="checked">
    <form action="" method="get">
<input type="hidden" name="confirm" value="confirmed">
<div class="form_item"> 
    <P class="form_item_label">ID</P>
    <div class="form_item_input"><span><?php echo $member['id']; ?></span></div>
</div>
<div class="form_item">
    <p class="form_item_label">氏名</p><p class="form_item_input"><?php echo $member['name_sei'].' '.$member['name_mei'] ?></p>
</div>
<div class="form_item">
    <p class="form_item_label">住所</p><p class="form_item_input"><?php echo $member['pref_name'].$member['address']  ?></p>
</div>
<div class="form_item">
    <p class="form_item_label">パスワード</p><p class="form_item_input">セキュリティのため非表示</p>
</div>
<div class="form_item">
    <p class="form_item_label">メールアドレス</p><p class="form_item_input"><?php echo $member['email'] ?></p>
</div>
<div>
    <button class="btn former" type="button" onclick="location.href='member_edit.php?id=<?php echo $member['id']; ?>'">編集</button>
    <button class="btn former" type="button" onclick="location.href='member_delete.php?id=<?php echo $member['id']; ?>'">削除</button>
</div>
</form>
      </main>
  </body>
</html>