<?php
$host = 'mysql-rouguiyatou.alwaysdata.net'; 
$dbname = 'rouguiyatou_mms_manager';
$username = 'rouguiyatou_admin';
$password = 'rougui20';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>