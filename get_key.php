<?php
include_once 'api/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT secret_key FROM saas_systems WHERE slug = 'restflux'");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo trim($row['secret_key']);
?>
