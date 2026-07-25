<?php
// api/access/get_data.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // Buscar usuários
    $queryUsers = "SELECT id, name, email, created_at FROM users ORDER BY id ASC";
    $stmtUsers = $db->prepare($queryUsers);
    $stmtUsers->execute();
    $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

    // Buscar sistemas
    $querySystems = "SELECT id, name, slug, api_key, secret_key FROM saas_systems ORDER BY name ASC";
    $stmtSystems = $db->prepare($querySystems);
    $stmtSystems->execute();
    $systems = $stmtSystems->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode([
        "success" => true, 
        "users" => $users,
        "systems" => $systems
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro ao buscar dados: " . $e->getMessage()]);
}
?>
