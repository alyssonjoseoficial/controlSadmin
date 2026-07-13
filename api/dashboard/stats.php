<?php
// api/dashboard/stats.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT id, name, slug, logo_url, status, active_users, revenue_monthly, support_notifications, auto_blocks_today, new_subscriptions_today, last_sync FROM saas_systems ORDER BY name ASC";
$stmt = $db->prepare($query);
$stmt->execute();

$systems = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    // Formatar faturamento
    $row['revenue_monthly'] = number_format($row['revenue_monthly'], 2, ',', '.');
    
    // Mapear colunas reais para o JSON
    $row['notifications'] = $row['support_notifications']; // O front-end usa 'notifications'

    
    array_push($systems, $row);
}

http_response_code(200);
echo json_encode(["success" => true, "systems" => $systems]);
?>
