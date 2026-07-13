<?php
include_once 'api/config/database.php';
$db = (new Database())->getConnection();

$columns = [
    'active_users' => 'INT DEFAULT 0',
    'revenue_monthly' => 'DECIMAL(10,2) DEFAULT 0.00',
    'support_notifications' => 'INT DEFAULT 0',
    'auto_blocks_today' => 'INT DEFAULT 0',
    'new_subscriptions_today' => 'INT DEFAULT 0',
    'last_sync' => 'TIMESTAMP NULL DEFAULT NULL'
];

foreach ($columns as $column => $definition) {
    try {
        $db->exec("ALTER TABLE saas_systems ADD COLUMN {$column} {$definition}");
        echo "Coluna {$column} adicionada.\n";
    } catch (PDOException $e) {
        // Coluna provavelmente já existe
        echo "Coluna {$column} já existe ou ocorreu um erro.\n";
    }
}
echo "Banco atualizado com sucesso!\n";
?>
