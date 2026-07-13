<?php
// api/dashboard/upload_logo.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';

session_start();

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['system_id']) && isset($_FILES['logo'])) {
        $system_id = intval($_POST['system_id']);
        $file = $_FILES['logo'];

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Tipo de arquivo não permitido."]);
            exit;
        }

        $uploadDir = '../../assets/images/logos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = 'logo_sys_' . $system_id . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $newFilename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Caminho que será salvo no BD e lido pelo frontend
            $logoUrl = 'assets/images/logos/' . $newFilename;
            $query = "UPDATE saas_systems SET logo_url = :logo_url WHERE id = :system_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':logo_url', $logoUrl);
            $stmt->bindParam(':system_id', $system_id);
            
            if($stmt->execute()) {
                http_response_code(200);
                echo json_encode(["success" => true, "message" => "Logo atualizada com sucesso!", "logo_url" => $logoUrl]);
            } else {
                http_response_code(500);
                echo json_encode(["success" => false, "message" => "Erro ao salvar no banco de dados."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Erro ao fazer upload do arquivo para a pasta."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID do sistema ou imagem não enviada."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Método não permitido."]);
}
?>
