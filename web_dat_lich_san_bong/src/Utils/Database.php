<?php
class Database
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
        $this->conn = $this->connect();
    }

    private function connect()
    {
        try {
            $conn = new PDO(
                "mysql:host=localhost;dbname=web_dat_lich_san_bong;charset=utf8",
                "root",
                ""
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $conn;
        } catch (PDOException $e) {
            die(json_encode(['success' => false, 'message' => 'Lỗi DB: ' . $e->getMessage()]));
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}