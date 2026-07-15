<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$secret_key = '06d07d13c12b8fb0a0ea6175fe1662a050045c2c6bdfa830be99acd1371a9b84';

if (!isset($_GET['token'])) {
    die("Acesso Negado: Token não fornecido.");
}

$jwt = $_GET['token'];
$tokenParts = explode('.', $jwt);

if (count($tokenParts) != 3) {
    die("Acesso Negado: Token inválido.");
}

$header = base64_decode($tokenParts[0]);
$payload = base64_decode($tokenParts[1]);
$signature_provided = $tokenParts[2];

// Recriar a assinatura para verificar
$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret_key, true);
$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

if ($base64UrlSignature !== $signature_provided) {
    die("Acesso Negado: Assinatura do Token inválida.");
}

$payloadData = json_decode($payload, true);

if ($payloadData['exp'] < time()) {
    die("Acesso Negado: Token expirado.");
}

// Token Válido! Buscar o SuperAdmin do Gestor Vital
try {
    $db = Database::getInstance()->getConnection();
    
    // Procura pela clínica MASTER ou superadmin principal
    $stmt = $db->prepare("SELECT u.*, c.nome as clinica_nome, c.logo as clinica_logo, c.cor_primaria 
                          FROM usuarios u 
                          JOIN clinicas c ON u.clinica_id = c.id 
                          WHERE u.nivel_acesso = 'Administrador Geral' AND u.ativo = 1 
                          LIMIT 1");
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nome'] = $user['nome'];
        $_SESSION['user_nivel'] = $user['nivel_acesso'];
        $_SESSION['clinica_id'] = $user['clinica_id'];
        $_SESSION['clinica_nome'] = $user['clinica_nome'];
        $_SESSION['clinica_logo'] = $user['clinica_logo'];
        $_SESSION['clinica_cor'] = $user['cor_primaria'];

        header("Location: " . BASE_URL . "/public/index.php");
        exit();
    } else {
        die("Nenhum Administrador Geral ativo encontrado no Gestor Vital.");
    }
} catch (PDOException $e) {
    die("Erro no banco de dados: " . $e->getMessage());
}
?>
