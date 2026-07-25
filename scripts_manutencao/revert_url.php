<?php
include_once 'api/config/database.php';
$db = (new Database())->getConnection();

$stmt = $db->prepare("UPDATE saas_systems SET url = 'https://admin.cachezum.com.br/sso.php' WHERE slug = 'giromax'");
$stmt->execute();

echo "URL revertida para Produção: https://admin.cachezum.com.br/sso.php\n";
?>
