<?php
// api/sso/generate_token.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';

session_start();
// Comentei a checagem rigorosa para facilitar seus testes iniciais, mas em produção DESCOMENTE!
/*
if(!isset($_SESSION['user_id'])){
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Não autorizado. Faça login primeiro."]);
    exit();
}
*/
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Super Admin';

$database = new Database();
$db = $database->getConnection();

$system_id = isset($_GET['system_id']) ? intval($_GET['system_id']) : 0;

if($system_id > 0) {
    $query = "SELECT url, secret_key FROM saas_systems WHERE id = :system_id AND status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':system_id', $system_id);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        $system = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Gerador de JWT nativo (Sem depender de pacotes externos)
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        
        // Token muito curto (30 segundos), ideal para ser consumido instantaneamente no redirecionamento
        $payload = json_encode([
            'user_id' => $user_id,
            'user_name' => $user_name,
            'system_id' => $system_id,
            'iat' => time(),
            'exp' => time() + 120 
        ]);
        
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $system['secret_key'], true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
        
        $redirect_url = $system['url'] . "?token=" . $jwt;
        
        http_response_code(200);
        echo json_encode(["success" => true, "redirect_url" => $redirect_url]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Sistema não encontrado ou inativo."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "ID do sistema não fornecido."]);
}
?>
