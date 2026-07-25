<?php
include_once 'C:\xampp\htdocs\Control_SADMIN\api\config\database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT * FROM saas_systems WHERE slug='giromax'");
print_r($stmt->fetch(PDO::FETCH_ASSOC));
?>
