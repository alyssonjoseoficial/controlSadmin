<?php
// api/auth/login.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(isset($data->email) && isset($data->password)){
    $query = "SELECT id, name, password_hash FROM users WHERE email = :email LIMIT 0,1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $data->email);
    $stmt->execute();
    
    $num = $stmt->rowCount();
    
    if($num > 0){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Em um cenário real, validaremos com password_verify
        // Se a senha estiver mockada como texto no DB para testes locais, trocamos.
        // Considerando que no setup SQL colocamos o hash para "password".
        if(password_verify($data->password, $row['password_hash'])){
            session_start();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            
            http_response_code(200);
            echo json_encode(array(
                "success" => true,
                "message" => "Login realizado com sucesso.",
                "user" => array(
                    "id" => $row['id'],
                    "name" => $row['name']
                )
            ));
        } else {
            http_response_code(401);
            echo json_encode(array("success" => false, "message" => "Login falhou. Senha incorreta."));
        }
    } else {
        http_response_code(401);
        echo json_encode(array("success" => false, "message" => "Login falhou. Usuário não encontrado."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Dados incompletos. Informe email e senha."));
}
?>
