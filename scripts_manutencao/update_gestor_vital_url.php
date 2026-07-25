<?php
include_once 'api/config/database.php';
$db = (new Database())->getConnection();

$stmt = $db->prepare("UPDATE saas_systems SET url = 'http://localhost/gestor_vital_new/public/sso_login.php' WHERE slug = 'gestor_vital_new'");
$stmt->execute();

echo "URL do Gestor Vital atualizada.\n";
?>
