<?php 
session_start();

if(!isset($_SESSION['id'])){
    header('Location: top.php'); 
    exit();
}


if(!empty($_POST['check'])){

    if($_POST['title']== ''){
        $error['title'] ='blank'; 
    }

    if($_POST['content']== ''){
        $error['content'] ='blank'; 
    }

    if(mb_strlen($_POST['title']) > 100) {
        $error['title'] = 'length';
    }

    if(mb_strlen($_POST['content']) > 500) {
        $error['content'] = 'length';
    }

    if (!isset($error)) {
        $_SESSION['thread'] = $_POST;  
        header('Location: thread_confirmation.php'); 
        exit();
    }

}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/stylesheet.css">
    <title>スレッド作成フォーム</title>
</head>
<body>
    <h2>スレッド作成フォーム</h2>
    <form  action="" method="post">
    <input type="hidden" name="check" value="checked">
    <div class="form_item">
        <p class="form_item_label">スレッドタイトル</p>
        <div class="form_item_input">
            <input class="title" type="text" name="title" value="<?php if( !empty($_POST['title']) ){ echo $_POST['title']; } ?>">
        </div>
    </div>
    <?php if(!empty($error["title"]) && $error['title'] == 'blank'): ?>
                <p class="error" >※スレッドタイトルは必須入力です。</p>
    <?php elseif(!empty($error["title"]) &&  $error['title'] == 'length'): ?>
                <p class="error">※スレッドタイトルは100文字以内で入力してください。</p>
    <?php endif ?>
    <div class="form_item">
        <p class="form_item_label">コメント</p>
        <div class="form_item_input">
            <textarea name="content" rows="5" ><?php if( !empty($_POST['content']) ){ echo $_POST['content']; } ?></textarea>
        </div>
    </div>
    <?php if(!empty($error["content"]) && $error['content'] == 'blank'): ?>
                <p class="error" >※コメントは必須入力です。</p>
    <?php elseif(!empty($error["content"]) &&  $error['content'] == 'length'): ?>
                <p class="error">※コメントは500文字以内で入力してください。</p>
    <?php endif ?>
    <div>
        <input class="btn confirm" type="submit"  value="確認画面へ">
      </div>
      <div>
        <button class="btn top" type="button" onclick="location.href='./top.php'">トップに戻る</button>
      </div>
    </form>
</body>

</html>