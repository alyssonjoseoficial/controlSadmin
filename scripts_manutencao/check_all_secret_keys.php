<?php
include_once 'C:\xampp\htdocs\Control_SADMIN\api\config\database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT slug, secret_key FROM saas_systems");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
