<?php
include_once 'api/config/database.php';
$db = (new Database())->getConnection();

// Atualiza a URL do Vepix para apontar para a porta do Next.js local
$stmt = $db->prepare("UPDATE saas_systems SET url = 'http://localhost:3000/api/sso' WHERE slug = 'projeto_loja'");
$stmt->execute();

echo "URL do Vepix atualizada para o Next.js (http://localhost:3000/api/sso).\n";
?>
