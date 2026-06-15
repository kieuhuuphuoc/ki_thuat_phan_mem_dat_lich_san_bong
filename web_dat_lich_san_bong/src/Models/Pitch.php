<?php
require_once __DIR__ . '/../Utils/Database.php';

class Pitch
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM pitches ORDER BY name");
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM pitches WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}