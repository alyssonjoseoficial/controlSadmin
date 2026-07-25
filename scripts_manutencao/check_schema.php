<?php
include_once 'C:\xampp\htdocs\Control_SADMIN\api\config\database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("DESCRIBE saas_systems");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
