<?php
include_once 'api/config/database.php';
$db = (new Database())->getConnection();

// Atualizar o CacheZum (Giromax) para apontar para o React Local (porta 5173) e rota /sso
$stmt = $db->prepare("UPDATE saas_systems SET url = 'http://localhost:5173/sso' WHERE slug = 'giromax'");
$stmt->execute();

echo "URL do CacheZum (Giromax) atualizada para o Frontend React Local (http://localhost:5173/sso)!\n";
?>
