<?php
require_once("../list.php");
require("../dbconnect.php");
session_start();
//ini_set('display_errors', 1);

//検索窓に何もない場合、全ての会員を表示
if(empty($_GET['check'])||$_GET['id']==''&&$_GET['gender']==''&&$_GET['pref_name']==''&&$_GET['search']==''){

$sql="SELECT * FROM members";

if(empty($_GET['sort'])){
    $sql.=' ORDER BY id DESC';
}else{

    switch($_GET['sort']){
        case "ASC":
            $sql.=' ORDER BY id ASC';
            break;
        case "DESC":
            $sql.=' ORDER BY id DESC';
            break;
        default:
            $sql.=' ORDER BY id DESC';
            break;
    }
}

//print $sql;

$stmt=$db->prepare($sql);
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

$query_string = http_build_query($_GET);
}
//echo $member_count;

//検索窓ごとにwhere句を作る
if(count($_GET)>0){
    $sql = "SELECT * FROM members where 1 ";
    $bindParam_args = [];

if (isset($_GET['id']) && $_GET['id'] != ""){
    $sql.=" AND id=?";
    $bindParam_args[]=$_GET['id'];
}
if (isset($_GET['gender']) && is_array($_GET['gender'])){
    //var_dump($_POST['gender']);
    $arr1 = array();
    foreach($_GET['gender'] as $gender){
    $arr1[] = " gender = ? ";
    $bindParam_args[]=$gender;
    }
    $where =implode(" OR ",$arr1);
    $sql.=" AND ($where)";
}

if (isset($_GET['pref_name']) && $_GET['pref_name'] != ""){
    $pref_num = $_GET['pref_name'];
    $pref_name = $prefNameList["$pref_num"];
    $sql.=" AND pref_name = ?";
    $bindParam_args[]=$pref_name;
}

if (isset($_GET['search']) && $_GET['search'] != ""){
    $searchword=$_GET['search'];
    $sql.=" AND ( name_sei like ?";
    $bindParam_args[]="%$searchword%";
    $sql.=" OR name_mei like ?";
    $bindParam_args[]="%$searchword%";
    $sql.=" OR email like ? )";
    $bindParam_args[]="%$searchword%";
}

if(empty($_GET['sort'])){
    $sql.=' ORDER BY id DESC';
}else{

    switch($_GET['sort']){
        case "ASC":
            $sql.=' ORDER BY id ASC';
            break;
        case "DESC":
            $sql.=' ORDER BY id DESC';
            break;
        default:
            $sql.=' ORDER BY id DESC';
            break;
    }
}
//var_dump($bindParam_args);

//print $sql;
$stmt3=$db->prepare($sql);
//bind配列
$index=1;
//foreach ($bindParam_args as $params){
    foreach ($bindParam_args as $param_id => $value) {
        $bindParam_args[$index] = $value; 
        $index++;
    }
//}
unset($bindParam_args[0]);
//var_dump($bindParam_args);

foreach ($bindParam_args as $param_id => $value) {

    switch (gettype($value)) {
        case 'integer':
            $param_type = PDO::PARAM_INT;
            break;

        case 'string':
            $param_type = PDO::PARAM_STR;
            break;

        default:
            $param_type = PDO::PARAM_STR;
    }

    $stmt3->bindValue($param_id, $value, $param_type);
}

$stmt3->execute();

$members=array();
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

$member_count=$stmt3->rowCount();
$query_string = http_build_query($_GET);

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
    <script src="https://kit.fontawesome.com/88a524cdd2.js" crossorigin="anonymous"></script>
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
    <form action="" method="get">
    <input type="hidden" name="check" value="checked">
    
    <div class="search-form">
    <div>
      <button class="member-btn" type="button" onclick="location.href='./member_regist.php'">会員登録</button>
    </div>
        <table class="search-box">
            <tr>
                <td class="form-label">ID</td><td><input class="search-box" type="text" name="id" value="<?php if (!empty($_GET['id'])) { echo $_GET['id'];} ?>"></td>
            </tr>
            <tr>
                <td class="form-label">性別</td><td><div class="form_item_input">
                    <input type="checkbox" name="gender[]" value="0" <?php if (!empty($_GET['gender'])&& in_array("0", $_GET['gender'])){echo "checked";}?>> 男性
                    <input type="checkbox" name="gender[]" value="1" <?php if (!empty($_GET['gender'])&& in_array("1", $_GET['gender'])){echo "checked";}?>> 女性
                </td>
            </tr>
            <tr>
                <td class="form-label">都道府県</td>
            <td>
            <select name="pref_name" >
            <?php
            foreach($prefNameList as $key => $value){
            if($key == $_GET['pref_name']){
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
                <td class="form-label">フリーワード</td><td><input class="search-box" type="text" name="search" value="<?php if (!empty($_GET['search'])) { echo $_GET['search'];} ?>"></td>
            </tr>
        </table>
            <input class="search-bottun" type="submit" name="" value="検索する">
    </div>
    </form>

    <div class="member">
        <?php if($member_count>0): ?>
        <table class="member-box">
            <tr>
                <th class="form-label id">ID 
                    <?php if(empty($_GET['sort']) || $_GET['sort']=='DESC'): ?>
                    <a href="./member.php?<?php echo $query_string; ?>&page=<?php echo $page; ?>&sort=ASC"><span class="fa-solid fa-caret-up"></span></a>
                    <?php elseif($_GET['sort']=='ASC'): ?>
                    <a href="./member.php?<?php echo $query_string; ?>&page=<?php echo $page; ?>&sort=DESC"><span class="fa-solid fa-caret-down"></span></a>
                    <?php endif; ?>
                </th>
                <th class="form-label name">氏名</th>
                <th class="form-label gender">性別</th>
                <th class="form-label address">住所</th>
                <th class="form-label datetime">登録日時 
                    <?php if(empty($_GET['sort']) || $_GET['sort']=='DESC'): ?>
                    <a href="./member.php?<?php echo $query_string; ?>&page=<?php echo $page; ?>&sort=ASC"><span class="fa-solid fa-caret-up"></span></a>
                    <?php elseif($_GET['sort']=='ASC'): ?>
                    <a href="./member.php?<?php echo $query_string; ?>&page=<?php echo $page; ?>&sort=DESC"><span class="fa-solid fa-caret-down"></span></a>
                    <?php endif; ?>
                <th class="form-label">編集</th>
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
                <td><a href="./member_edit.php?id=<?php echo $member['id']; ?>">編集</a></td>
            </tr>
        <?php endforeach; ?>
        <?php //echo $query_string; ?>
        </table>
        <?php endif; ?>
        <?php if(count($_GET)>0 && $member_count<1):?>
            <span>検索結果：０件</span>
        <?php endif; ?>
    </div>
    <div class="paging">
    <?php if ($page > 1) : ?>
      <a href="./member.php?<?php echo $query_string; ?>&page=<?php echo $page-1; ?>">前へ</a>
    <?php else: ?>
      <span></span>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
    <?php if($i >= $page - $range && $i <= $page + $range) : ?>
       <?php if($i == $page) : ?>
           <span class="now_page_number"><?php echo $i; ?></span>
       <?php else: ?>
           <a href="?<?php echo $query_string; ?>&page=<?php echo $i; ?>" class="page_number"><?php echo $i; ?></a>
       <?php endif; ?>
    <?php endif; ?>
    <?php endfor; ?>

    <?php if ($page < $totalPages) : ?>
      <a href="./member.php?<?php echo $query_string; ?>&page=<?php echo $page+1; ?>">次へ</a>
    <?php else: ?>
      <span></span>
    <?php endif; ?>
    </div>
</main>
</body>


</html>