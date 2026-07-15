<?php
session_start();
require_once "../../config/db.php";

// A chave secreta exata do Educa SaaS no banco Control_SADMIN
$secret_key = '8881ce3e1b9042690f1811a09a09f7cc9c826553c55eb739dceb672a8e14d935';

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

// Token Válido! Buscar o SuperAdmin do Educa SaaS no banco
$sql = "SELECT id, nome FROM usuarios WHERE nivel_acesso = 'superadmin' LIMIT 1";
$resultado = $conn->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    $admin = $resultado->fetch_assoc();
    
    $_SESSION['usuario_id'] = $admin['id'];
    $_SESSION['usuario_nome'] = $admin['nome'];
    $_SESSION['nivel_acesso'] = 'superadmin';
    
    // Redireciona para o painel do superadmin
    header("Location: index.php");
    exit;
} else {
    die("Nenhum SuperAdmin encontrado no Educa SaaS.");
}
?>
