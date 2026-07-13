<?php
include_once 'api/config/database.php';
$db = (new Database())->getConnection();

$stmt = $db->prepare("UPDATE saas_systems SET url = 'http://localhost/educa_saas/public/superadmin/sso_login.php' WHERE slug = 'educa_saas'");
$stmt->execute();

echo "URL do Educa SaaS atualizada.\n";
?>
