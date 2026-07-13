<?php
include 'c:/xampp/htdocs/Control_SADMIN/api/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->query('SELECT slug, url FROM saas_systems');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
