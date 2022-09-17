<?php 
require("../dbconnect.php");
session_start();

$id=$_GET['id'];
if($id == ''){
    header('Location: member.php'); 
    exit();
}else{
$withdrawal=$db->prepare("UPDATE members SET deleted_at= now() WHERE id= :id");
$withdrawal->bindParam(':id', $id, PDO::PARAM_INT);
$withdrawal->execute();
 
header('Location: member.php');
exit();
}
?>