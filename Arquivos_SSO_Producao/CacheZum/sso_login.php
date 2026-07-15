<?php
// sso.php - Recebe o token do Control_SADMIN e gera um token JWT válido para a API do CacheZum
$control_sadmin_secret = 'cachezum_secret_super_safe_123!@#';
$cachezum_api_secret = 'giromax_super_secret_key_123';

$token = isset($_GET['token']) ? $_GET['token'] : '';

if (!$token) {
    die("Token SSO não fornecido.");
}

// 1. Verificar o token do Control_SADMIN
$parts = explode('.', $token);
if (count($parts) !== 3) {
    die("Token SSO inválido.");
}

list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;
$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $control_sadmin_secret, true);
$expectedSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

if ($expectedSignature !== $base64UrlSignature) {
    die("Assinatura do Token SSO inválida.");
}

$payload = json_decode(base64_decode($base64UrlPayload), true);
if ($payload['exp'] < time()) {
    die("Token SSO expirado.");
}

// 2. Gerar novo token JWT para a API do CacheZum (NestJS)
$newHeader = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
$newPayload = json_encode([
    'sub' => 'sso_admin_' . $payload['user_id'],
    'email' => 'admin@controlsadmin.com',
    'role' => 'superadmin',
    'planStatus' => 'active',
    'iat' => time(),
    // Token longo para a sessão do admin (ex: 24h)
    'exp' => time() + (24 * 60 * 60)
]);

$base64UrlNewHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($newHeader));
$base64UrlNewPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($newPayload));

$newSignature = hash_hmac('sha256', $base64UrlNewHeader . "." . $base64UrlNewPayload, $cachezum_api_secret, true);
$base64UrlNewSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($newSignature));

$cachezumJwt = $base64UrlNewHeader . "." . $base64UrlNewPayload . "." . $base64UrlNewSignature;
$adminUserJson = json_encode([
    'role' => 'superadmin',
    'name' => $payload['user_name'] ?? 'SSO Admin',
    'email' => 'admin@controlsadmin.com'
]);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Autenticando SSO...</title>
</head>
<body style="background: #111827; color: #fff; display: flex; justify-content: center; align-items: center; height: 100vh; font-family: sans-serif;">
    <h2>Conectando ao CacheZum...</h2>
    <script>
        // Salvar as credenciais no localStorage do admin
        localStorage.setItem('cachezum_admin_token', '<?php echo $cachezumJwt; ?>');
        localStorage.setItem('cachezum_admin_user', '<?php echo $adminUserJson; ?>');
        
        // Redirecionar para o Dashboard Global
        window.location.href = '/';
    </script>
</body>
</html>
