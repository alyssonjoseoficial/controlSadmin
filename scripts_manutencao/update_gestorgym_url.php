<?php
include_once 'C:\xampp\htdocs\Control_SADMIN\api\config\database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("UPDATE saas_systems SET url='https://gestorgym.com.br/admin_master/sso_login.php' WHERE slug='gestorgym'");
$stmt->execute();
echo "URL do GestorGym atualizada com sucesso no banco de dados!";
?>
