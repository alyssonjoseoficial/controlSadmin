<?php
// api/update_db_systems.php
include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

try {
    // 1. Limpa os sistemas antigos de teste
    $db->exec("DELETE FROM saas_systems");
    
    // Reset auto increment (dependendo do banco pode ignorar se falhar)
    try { $db->exec("ALTER TABLE saas_systems AUTO_INCREMENT = 1"); } catch(Exception $e) {}

    // 2. Novos Sistemas Oficiais
    $sistemas = [
        ['name' => 'Educa SaaS', 'slug' => 'educa_saas', 'url' => 'https://educasaas.com.br', 'logo_url' => 'https://via.placeholder.com/150/2c3e50/fff.png?text=Educa'],
        ['name' => 'Mesaki', 'slug' => 'restflux', 'url' => 'https://mesaki.com.br', 'logo_url' => 'https://via.placeholder.com/150/e74c3c/fff.png?text=Mesaki'],
        ['name' => 'Gestor Gym', 'slug' => 'gestorgym', 'url' => 'https://gestorgym.com.br', 'logo_url' => 'https://via.placeholder.com/150/f39c12/fff.png?text=Gym'],
        ['name' => 'CacheZum', 'slug' => 'giromax', 'url' => 'https://cachezum.com.br', 'logo_url' => 'https://via.placeholder.com/150/8e44ad/fff.png?text=CacheZum'],
        ['name' => 'Gestor Vital', 'slug' => 'gestor_vital_new', 'url' => 'https://gestorvital.com.br', 'logo_url' => 'https://via.placeholder.com/150/16a085/fff.png?text=Vital'],
        ['name' => 'Vepix', 'slug' => 'projeto_loja', 'url' => 'https://vepix.com.br', 'logo_url' => 'https://via.placeholder.com/150/2980b9/fff.png?text=Vepix']
    ];

    $stmt = $db->prepare("INSERT INTO saas_systems (name, slug, url, api_key, secret_key, logo_url) VALUES (:name, :slug, :url, :api_key, :secret_key, :logo_url)");

    foreach ($sistemas as $sys) {
        $sys['api_key'] = bin2hex(random_bytes(16)); // Gera chave única de 32 chars
        $sys['secret_key'] = bin2hex(random_bytes(32)); // Gera chave secreta de 64 chars
        $stmt->execute($sys);
    }
    echo "<h1>Sistemas atualizados no banco de dados com sucesso!</h1><p>Você já pode fechar esta aba e recarregar o seu painel.</p>";
} catch (PDOException $e) {
    echo "Erro ao atualizar: " . $e->getMessage();
}
?>
