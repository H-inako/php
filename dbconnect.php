<?php
try {
    $db = new PDO('mysql:dbname=mydb;host=153.127.18.207;charset=utf8mb4', 'root', '6N2BCDzJ');
}   catch (PDOException $e) {
    echo "データベース接続エラー　：".$e->getMessage();
}


?>