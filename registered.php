<?php 
session_start();
if( !empty($_SESSION['page']) && $_SESSION['page'] === true ) {

    // セッションの削除
    unset($_SESSION['page']);
}
?>
<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/stylesheet.css">
    
    <title>会員登録完了</title>
    </head>
    <body>
        <h2>会員登録完了</h2>
        <h3>会員登録が完了しました。</h3>
        <button class="btn top" type="button" onclick="location.href='./top.php'">トップに戻る</button>
    </body>
</html>