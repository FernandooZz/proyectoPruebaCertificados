<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        // Cargar variables desde .env
        $env = parse_ini_file(__DIR__ . '/.env');

        if (!$env) {
            throw new Exception('Archivo .env no encontrado');
        }

        $this->host = $env['DB_HOST'];
        $this->db_name = $env['DB_NAME'];
        $this->username = $env['DB_USER'];
        $this->password = $env['DB_PASS'];
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $ca = __DIR__ . '/ca.pem';

            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=12569;dbname=" . $this->db_name,
                $this->username,
                $this->password,
                [
                    PDO::MYSQL_ATTR_SSL_CA => $ca,
                    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true
                ]
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");

        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>