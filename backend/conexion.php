<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
    $env = file_exists(__DIR__ . '/.env')
        ? parse_ini_file(__DIR__ . '/.env')
        : [];

    $this->host = getenv('DB_HOST') ?: ($env['DB_HOST'] ?? null);
    $this->db_name = getenv('DB_NAME') ?: ($env['DB_NAME'] ?? null);
    $this->username = getenv('DB_USER') ?: ($env['DB_USER'] ?? null);
    $this->password = getenv('DB_PASS') ?: ($env['DB_PASS'] ?? null);
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