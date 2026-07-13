<?php
// api/webhook/update_stats.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, X-API-KEY");

include_once '../config/database.php';

// Pegar os cabeçalhos
$headers = apache_request_headers();
$api_key = isset($headers['X-API-KEY']) ? $headers['X-API-KEY'] : (isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : '');

if(empty($api_key)) {
    http_response_code(401);
    echo json_encode(["message" => "API Key ausente."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Verificar qual sistema é o dono desta API Key
$query = "SELECT id, slug, status FROM saas_systems WHERE api_key = :api_key LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':api_key', $api_key);
$stmt->execute();

if($stmt->rowCount() == 0) {
    http_response_code(403);
    echo json_encode(["message" => "API Key inválida."]);
    exit();
}

$system = $stmt->fetch(PDO::FETCH_ASSOC);

if($system['status'] !== 'active') {
    http_response_code(403);
    echo json_encode(["message" => "Sistema inativo."]);
    exit();
}

// Ler os dados JSON recebidos no corpo
$data = json_decode(file_get_contents("php://input"));

if(!empty($data)) {
    $active_users = isset($data->active_users) ? intval($data->active_users) : 0;
    $revenue_monthly = isset($data->revenue_monthly) ? floatval($data->revenue_monthly) : 0.00;
    $support_notifications = isset($data->support_notifications) ? intval($data->support_notifications) : 0;
    $auto_blocks_today = isset($data->auto_blocks_today) ? intval($data->auto_blocks_today) : 0;
    $new_subscriptions_today = isset($data->new_subscriptions_today) ? intval($data->new_subscriptions_today) : 0;
    $last_sync = date('Y-m-d H:i:s');

    $updateQuery = "UPDATE saas_systems 
                    SET active_users = :au, 
                        revenue_monthly = :rm, 
                        support_notifications = :sn, 
                        auto_blocks_today = :abt, 
                        new_subscriptions_today = :nst, 
                        last_sync = :ls 
                    WHERE id = :id";
                    
    $updateStmt = $db->prepare($updateQuery);
    
    $updateStmt->bindParam(':au', $active_users);
    $updateStmt->bindParam(':rm', $revenue_monthly);
    $updateStmt->bindParam(':sn', $support_notifications);
    $updateStmt->bindParam(':abt', $auto_blocks_today);
    $updateStmt->bindParam(':nst', $new_subscriptions_today);
    $updateStmt->bindParam(':ls', $last_sync);
    $updateStmt->bindParam(':id', $system['id']);
    
    if($updateStmt->execute()) {
        http_response_code(200);
        echo json_encode([
            "message" => "Estatísticas atualizadas com sucesso.", 
            "system" => $system['slug'],
            "last_sync" => $last_sync
        ]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Não foi possível atualizar as estatísticas."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Dados ausentes."]);
}
?>
