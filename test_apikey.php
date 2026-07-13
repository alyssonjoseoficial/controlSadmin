<?php
include 'c:/xampp/htdocs/Control_SADMIN/api/config/database.php';
$stmt = (new Database())->getConnection()->query("SELECT api_key FROM saas_systems WHERE slug = 'educa_saas'");
print_r($stmt->fetch(PDO::FETCH_ASSOC));
?>
