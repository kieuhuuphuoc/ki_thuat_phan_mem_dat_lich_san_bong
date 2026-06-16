<?php
class Database
{
    private $host = "localhost";
    private $dbname = "web_dat_lich_san_bong";
    private $username = "root";
    private $password = "";

    public function connect()
    {
        try {
            $conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                $this->username,
                $this->password
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $conn;
        } catch (PDOException $e) {
            die(json_encode(['success' => false, 'message' => 'Lỗi DB: ' . $e->getMessage()]));
        }
    }
}
?>