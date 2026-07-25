<?php
include_once 'C:\xampp\htdocs\Control_SADMIN\api\config\database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("UPDATE saas_systems SET url='https://www.gestorvital.com.br/public/sso_login.php' WHERE slug='gestor_vital_new'");
$stmt->execute();
echo "URL do GestorVital atualizada com sucesso no banco de dados!";
?>
