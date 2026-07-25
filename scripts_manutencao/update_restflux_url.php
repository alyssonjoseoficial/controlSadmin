<?php
include_once 'C:\xampp\htdocs\Control_SADMIN\api\config\database.php';
$db = (new Database())->getConnection();
$db->query("UPDATE saas_systems SET url='https://mesaki.com.br/#/sso' WHERE slug='restflux'");
echo "URL updated successfully.";
?>
