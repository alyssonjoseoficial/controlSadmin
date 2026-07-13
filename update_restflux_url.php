<?php
include_once 'api/config/database.php';
$db = (new Database())->getConnection();

$stmt = $db->prepare("UPDATE saas_systems SET url = 'http://localhost:5173/#/sso' WHERE slug = 'restflux'");
$stmt->execute();

echo "URL do RestFlux atualizada.\n";
?>
