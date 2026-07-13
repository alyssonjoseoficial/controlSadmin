<?php
// api/config/database.php

class Database {
    // Altere para as credenciais reais do seu MySQL Remoto na HostGator
    private $host = "108.179.241.221"; // IP da hospedagem HostGator
    private $db_name = "alyss340_kirontech_db";
    private $username = "alyss340_teste";
    private $password = "Gja@4367k";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Em ambiente de teste local, pode ser interessante usar mock caso não conecte.
            // Aqui estamos configurando a conexão real com a HostGator.
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Caso ocorra erro, exibimos para debug inicial. No projeto final, deve-se registrar no log.
            die(json_encode(["error" => "Erro de Conexão com o Banco de Dados: " . $exception->getMessage()]));
        }
        return $this->conn;
    }
}
?>
