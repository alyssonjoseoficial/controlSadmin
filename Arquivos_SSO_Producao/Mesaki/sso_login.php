<?php
/**
 * Arquivo de Login SSO para o sistema: Mesaki
 * Faça o upload deste exato arquivo (sso_login.php) para a RAIZ (public_html) do seu sistema na HostGator.
 */
$secret_key = "mesaki_secret_key_2026!@#"; 

if (isset($_GET["token"])) {
    $jwt = $_GET["token"];
    $tokenParts = explode(".", $jwt);
    
    if(count($tokenParts) == 3) {
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];
        
        $base64UrlHeader = str_replace(["+", "/", "="], ["-", "_", ""], base64_encode($header));
        $base64UrlPayload = str_replace(["+", "/", "="], ["-", "_", ""], base64_encode($payload));
        $signature = hash_hmac("sha256", $base64UrlHeader . "." . $base64UrlPayload, $secret_key, true);
        $base64UrlSignature = str_replace(["+", "/", "="], ["-", "_", ""], base64_encode($signature));
        
        if ($base64UrlSignature === $signature_provided) {
            $data = json_decode($payload);
            if(time() <= $data->exp) {
                session_start();
                $_SESSION["admin_logged_in"] = true;
                $_SESSION["admin_id"] = $data->user_id;
                
                echo "<h1>Autenticado com Sucesso no Mesaki!</h1>";
                echo "<p>Você está logado através do Hub Central.</p>";
                echo "<p><em>*Atenção programador: Altere este arquivo para redirecionar para o painel de admin real do Mesaki (ex: header(\"Location: /admin\"))</em></p>";
                exit();
            } else {
                die("Erro SSO: Token expirado.");
            }
        } else {
            die("Erro SSO: Assinatura invalida. A chave secreta nao bate com a do Hub.");
        }
    } else {
        die("Erro SSO: Token malformado.");
    }
} else {
    die("Erro SSO: Acesso negado. Token ausente.");
}
?>