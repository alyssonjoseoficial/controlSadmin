<?php
include_once 'api/config/database.php';
$db = (new Database())->getConnection();

// 1. Atualizar a URL do CacheZum para apontar para o novo script de recepção sso.php
// 2. Garantir que a secret_key que o Control_SADMIN usa para assinar o JWT seja a esperada pelo nosso sso.php (que é cachezum_secret_super_safe_123!@#)
$stmt = $db->prepare("UPDATE saas_systems SET url = 'https://admin.cachezum.com.br/sso.php', secret_key = 'cachezum_secret_super_safe_123!@#' WHERE slug = 'giromax'");
$stmt->execute();

echo "URL do CacheZum atualizada com sucesso para apontar para o novo Painel (https://admin.cachezum.com.br/sso.php)!\n";
?>
