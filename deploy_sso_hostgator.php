<?php
/**
 * deploy_sso_hostgator.php
 * Script para automatizar o envio dos arquivos SSO para as pastas raiz de cada sistema no servidor HostGator.
 * Como o Control_SADMIN está no mesmo servidor, podemos copiar os arquivos diretamente!
 */

$baseHostgatorDir = '/home1/alyss340';
$ssoFilesDir = __DIR__ . '/Arquivos_SSO_Producao';

$systems = [
    'CacheZum' => [
        'source' => $ssoFilesDir . '/CacheZum/sso_login.php',
        // O CacheZum usa React, a raiz na web é a pasta onde o app roda
        'destination' => $baseHostgatorDir . '/admin.cachezum.com.br/sso.php' 
    ],
    'EducaSaaS' => [
        'source' => $ssoFilesDir . '/EducaSaaS/sso_login.php',
        'destination' => $baseHostgatorDir . '/educasaas.com.br/sso_login.php'
    ],
    'GestorGym' => [
        'source' => $ssoFilesDir . '/GestorGym/sso_login.php',
        'destination' => $baseHostgatorDir . '/gestorgym.com.br/sso_login.php'
    ],
    'GestorVital' => [
        'source' => $ssoFilesDir . '/GestorVital/sso_login.php',
        'destination' => $baseHostgatorDir . '/gestorvital.com.br/sso_login.php'
    ],
    'Mesaki' => [
        'source' => $ssoFilesDir . '/Mesaki/sso_login.php',
        'destination' => $baseHostgatorDir . '/mesaki.com.br/sso_login.php'
    ],
    'Vepix' => [
        'source' => $ssoFilesDir . '/Vepix/sso_login.php',
        'destination' => $baseHostgatorDir . '/vepix.com.br/sso_login.php'
    ]
];

echo "<h1>Deploy Automático de SSO - HostGator</h1>";

foreach ($systems as $systemName => $paths) {
    echo "<h3>Processando: $systemName</h3>";
    
    if (!file_exists($paths['source'])) {
        echo "<p style='color:red;'>Erro: Arquivo fonte não encontrado: {$paths['source']}</p>";
        continue;
    }
    
    $destDir = dirname($paths['destination']);
    if (!is_dir($destDir)) {
        echo "<p style='color:orange;'>Aviso: Pasta de destino não encontrada: $destDir (Pode estar executando no localhost ou a pasta não existe no servidor)</p>";
    } else {
        // Tenta copiar
        if (copy($paths['source'], $paths['destination'])) {
            echo "<p style='color:green;'>Sucesso: Arquivo copiado para {$paths['destination']}</p>";
        } else {
            echo "<p style='color:red;'>Erro: Falha ao copiar arquivo para {$paths['destination']}</p>";
        }
    }
}

echo "<hr><p>Deploy finalizado. Teste o acesso pelo Control_SADMIN.</p>";
?>
