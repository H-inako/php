<?php
require_once("../list.php");
require("../dbconnect.php");
session_start();

//検索窓に何もない場合、全ての会員を表示
if(empty($_POST['check'])||$_POST['id']==''&&$_POST['gender']==''&&$_POST['pref_name']==''&&$_POST['search']==''){
$stmt=$db->prepare("SELECT * FROM members ORDER BY id desc");
$stmt->execute();

$members=array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
  $members[]=array(
  'id' =>$row['id'],
  'name_sei'=>$row['name_sei'],
  'name_mei'=>$row['name_mei'],
  'gender'=>$row['gender'],
  'pref_name'=>$row['pref_name'],
  'address'=>$row['address'],
  'created_at'=>$row['created_at']
  );
 }

$stmt2=$db->prepare("SELECT COUNT(*) as cnt FROM members");
$stmt2->execute();
$record=$stmt2->fetch();
$member_count=$record['cnt'];
}
//echo $member_count;

//検索窓ごとにwhere句を作る
if(!empty($_POST['id'])||!empty($_POST['gender'])||!empty($_POST['pref_name'])||!empty($_POST['search'])){
$where=array();
if (isset($_POST['id']) && $_POST['id'] != ""){
    $where[] = 'id = :id';
}

if (isset($_POST['gender']) && is_array($_POST['gender'])){
    $arr1 = array();
    foreach($_POST['gender'] as $gender){
    $arr1[] = " gender = ':gender' ";
    }

    $where[]=implode(" OR ",$arr1);
}

if (isset($_POST['pref_name']) && $_POST['prefname'] != ""){
    $pref_num = $_POST['pref_name'];
    $pref_name = $prefNameList["$pref_num"];
    $where[] = "pref_name = :pref_name";
}

if (isset($_POST['search']) && $_POST['search'] != ""){
    $where[] = "name_sei like :name_sei or name_mei like :name_mei or email like :email";
}

if(count($where) > 0){
  $wheresql=implode("AND",$where);
  $sql = 'SELECT * FROM members WHERE'.$wheresql.'ORDER BY id DESC';
}

if(count($where) > 0){
    $sql4 = 'SELECT * FROM members WHERE '.$wheresql;
  }
 $searchword=$_POST['search'];
$stmt3->$db->prepare($sql);
$stmt3->bindParam(':id',$_POST['id'], PDO::PARAM_INT);
$stmt3->bindParam(':gender',$_POST['gender'], PDO::PARAM_INT);
$stmt3->bindParam(':pref_name',$pref_name, PDO::PARAM_STR);
$stmt3->bindParam(':name_sei',"%{$searchword}%", PDO::PARAM_STR);
$stmt3->bindParam(':name_mei',"%{$searchword}%", PDO::PARAM_STR);
$stmt3->bindParam(':email',"%{$searchword}%", PDO::PARAM_STR);
$stmt3->execute();
$members=array();

$stmt4=$db->prepare($sql4);
$stmt4->bindParam(':id',$_POST['id'], PDO::PARAM_INT);
$stmt4->bindParam(':gender',$_POST['gender'], PDO::PARAM_INT);
$stmt4->bindParam(':pref_name',$pref_name, PDO::PARAM_STR);
$stmt4->bindParam(':search_word',"%{$searchword}%", PDO::PARAM_STR);
$stmt4->bindParam(':name_mei',"%{$searchword}%", PDO::PARAM_STR);
$stmt4->bindParam(':email',"%{$searchword}%", PDO::PARAM_STR);
$stmt4->execute();
$record=$stmt4->fetch();
$member_count=$record['cnt'];
while($row = $stmt3->fetch(PDO::FETCH_ASSOC)){
    $members[]=array(
    'id' =>$row['id'],
    'name_sei'=>$row['name_sei'],
    'name_mei'=>$row['name_mei'],
    'gender'=>$row['gender'],
    'pref_name'=>$row['pref_name'],
    'address'=>$row['address'],
    'created_at'=>$row['created_at']
    );
   }
}

//ページング
$num=10;//表示するコメント件数
$totalPages=ceil($member_count/$num);

//指定件数ごとにコメント配列を分割
$members=array_chunk($members,$num);
$page=1;
if(isset($_GET['page']) && is_numeric($_GET['page'])){
    $page=intval($_GET['page']);
    if(!isset($members[$page-1])){
          $page=1;
        }
}

//ページ番号
if($page == 1 || $page == $totalPages) {
    $range = 2;
} elseif ($page == 2 || $page == $totalPages - 1) {
    $range = 1;
} else {
    $range = 1;
}


?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>会員一覧</title>
    <link rel="stylesheet" href="../css/stylesheet3.css">
</head>
<body>
    <header>
        <div class="hwrapper">
        <ul class="bottun-list">
        <li class="bottun"><a class="btn" href="./top.php">トップへ戻る</a></li>
        </ul>   
        </div>
    </header>
<main>
    <form action="" method="post">
    <input type="hidden" name="check" value="checked">
    
    <div class="search-form">
        <table class="search-box">
            <tr>
                <td class="form-label">ID</td><td><input class="search-box" type="text" name="id"></td>
            </tr>
            <tr>
                <td class="form-label">性別</td><td><div class="form_item_input"><input type="checkbox" name="gender[]" value="0"> 男性<input type="checkbox" name="gender[]" value="1"> 女性</div></td>
            </tr>
            <tr>
                <td class="form-label">都道府県</td>
            <td>
            <select name="pref_name" >
            <?php
            foreach($prefNameList as $key => $value){
            if($key == $_POST['pref_name']){
            echo "<option value='$key' selected>".$value."</option>";
            }else{
            echo "<option value='$key'>".$value."</option>";
            }
            }      
            ?>
            </select>
            </td>
            </tr>
            <tr>
                <td class="form-label">フリーワード</td><td><input class="search-box" type="text" name="search"></td>
            </tr>
        </table>
            <input class="search-bottun" type="submit" name="" value="検索する">
    </div>
    </form>

    <div class="member">
        <table class="member-box">
            <tr>
                <th class="form-label id">ID</th>
                <th class="form-label name">氏名</th>
                <th class="form-label gender">性別</th>
                <th class="form-label address">住所</th>
                <th class="form-label datetime">登録日時</th>
            </tr>
  
        <?php foreach($members[$page-1] as $member): ?>
        <?php if($member['gender']==0){
                $gender='男性';
              }else{
                $gender='女性';
              }

         ?>
            <tr>
                <td><?php echo $member['id']; ?></td>
                <td><?php echo $member['name_sei'].' '.$member['name_mei']; ?></td>
                <td><?php echo $gender; ?></td>
                <td><?php echo $member['pref_name'].$member['address']; ?></td>
                <td><?php echo $member['created_at']; ?></td>
            </tr>
        <?php endforeach; ?>
        </table>
    </div>
    <div class="paging">
    <?php if ($page > 1) : ?>
      <a href="./member_test.php?page=<?php echo $page-1; ?>">前へ</a>
    <?php else: ?>
      <span></span>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
    <?php if($i >= $page - $range && $i <= $page + $range) : ?>
       <?php if($i == $page) : ?>
           <span class="now_page_number"><?php echo $i; ?></span>
       <?php else: ?>
           <a href="?page=<?php echo $i; ?>" class="page_number"><?php echo $i; ?></a>
       <?php endif; ?>
    <?php endif; ?>
    <?php endfor; ?>

    <?php if ($page < $totalPages) : ?>
      <a href="./member_test.php?page=<?php echo $page+1; ?>">次へ</a>
    <?php else: ?>
      <span></span>
    <?php endif; ?>
    </div>
</main>
</body>


</html>