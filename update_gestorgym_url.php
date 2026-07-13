<?php
include_once 'api/config/database.php';
$db = (new Database())->getConnection();

$stmt = $db->prepare("UPDATE saas_systems SET url = 'http://localhost/gestorgym/admin_master/sso_login.php' WHERE slug = 'gestorgym'");
$stmt->execute();

echo "URL do Gestor Gym atualizada.\n";
?>
