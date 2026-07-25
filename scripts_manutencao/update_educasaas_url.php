<?php
include_once 'C:\xampp\htdocs\Control_SADMIN\api\config\database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("UPDATE saas_systems SET url='https://educasaas.com.br/public/superadmin/sso_login.php' WHERE slug='educa_saas'");
$stmt->execute();
echo "URL do EducaSaaS atualizada com sucesso no banco de dados!";
?>
