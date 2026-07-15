<?php
// FORÇAR EXIBIÇÃO DE ERROS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CAMINHO DO BANCO
$configPath = __DIR__ . '/../config/database.php';
if (!file_exists($configPath)) {
    die("ERRO CRÍTICO: O arquivo de configuração não foi encontrado.");
}
require_once $configPath;

// CONFIGURAÇÃO DE SESSÃO ISOLADA
session_name('GYMPRO_MASTER_SESSION');
session_set_cookie_params([
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Chave do gestorgym no Control_SADMIN
$secret_key = '46e70b8ba7d1cf1ab9933f79356598f7bae49502c4631ac83fb4c5d99321f93b';

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

// Token Válido! Buscar o SuperAdmin do Gestor Gym no banco
try {
    $stmt = $pdo->prepare("SELECT id, nome FROM super_admins WHERE status = 'ativo' LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch();

    if ($admin) {
        $_SESSION['user_id']   = $admin['id'];
        $_SESSION['user_name'] = $admin['nome'];
        $_SESSION['is_master'] = true;

        header("Location: dashboard.php");
        exit();
    } else {
        die("Nenhum SuperAdmin ativo encontrado no Gestor Gym.");
    }
} catch (PDOException $e) {
    die("Erro no banco de dados: " . $e->getMessage());
}
?>
