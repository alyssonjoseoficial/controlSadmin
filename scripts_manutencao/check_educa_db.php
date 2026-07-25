<?php
include_once 'C:\xampp\htdocs\Control_SADMIN\api\config\database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT url FROM saas_systems WHERE slug='educa_saas'");
print_r($stmt->fetch(PDO::FETCH_ASSOC));
?>
