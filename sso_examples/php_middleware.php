<?php
/**
 * Exemplo de Middleware SSO para sistemas SaaS feitos em PHP (Ex: Vepix).
 * Você deve criar um arquivo (ex: sso_login.php) na raiz do seu SaaS e colocar este código.
 * O Control_SADMIN enviará o admin para este arquivo com o token na URL.
 */

// Esta é a MESMA chave secreta cadastrada para este sistema no BD do Control_SADMIN
$secret_key = 'vepix_secret_super_safe_123!@#'; 

if (isset($_GET['token'])) {
    $jwt = $_GET['token'];
    $tokenParts = explode('.', $jwt);
    
    if(count($tokenParts) == 3) {
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];
        
        // Recria a assinatura para validar
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret_key, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        if ($base64UrlSignature === $signature_provided) {
            $data = json_decode($payload);
            // Verifica se o token ainda é válido (tempo)
            if(time() <= $data->exp) {
                // TOKEN VÁLIDO E AUTÊNTICO!
                
                session_start();
                // Aqui você cria a sessão local que o seu SaaS exige para logar o SuperAdmin.
                // Exemplo:
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $data->user_id;
                
                // Redireciona para o painel real do SaaS
                header("Location: /admin/dashboard.php");
                exit();
            } else {
                die("Erro SSO: O Token de acesso expirou. Tente novamente a partir do Control_SADMIN.");
            }
        } else {
            die("Erro SSO: Assinatura do Token inválida. Possível tentativa de fraude.");
        }
    } else {
        die("Erro SSO: Token malformado.");
    }
} else {
    die("Erro SSO: Token ausente.");
}
?>
