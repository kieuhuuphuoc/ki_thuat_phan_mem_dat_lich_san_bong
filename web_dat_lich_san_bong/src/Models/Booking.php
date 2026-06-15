<?php
require_once __DIR__ . '/../Utils/Database.php';

class Booking
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($userId, $pitchId, $date, $startTime, $endTime)
    {
        $stmt = $this->db->prepare("
            INSERT INTO bookings (user_id, pitch_id, booking_date, start_time, end_time, status) 
            VALUES (?, ?, ?, ?, ?, 'pending')
        ");
        return $stmt->execute([$userId, $pitchId, $date, $startTime, $endTime]);
    }

    public function checkConflict($pitchId, $date, $startTime, $endTime)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM bookings 
            WHERE pitch_id = ? 
            AND booking_date = ? 
            AND status != 'cancelled'
            AND (start_time < ? AND end_time > ?)
        ");
        $stmt->execute([$pitchId, $date, $endTime, $startTime]);
        return $stmt->fetchColumn() > 0;
    }

    public function getByUser($userId)
    {
        $stmt = $this->db->prepare("
            SELECT b.*, p.name as pitch_name 
            FROM bookings b 
            JOIN pitches p ON b.pitch_id = p.id 
            WHERE b.user_id = ? 
            ORDER BY b.booking_date DESC, b.start_time ASC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function cancel($bookingId, $userId)
    {
        $stmt = $this->db->prepare("
            UPDATE bookings 
            SET status = 'cancelled' 
            WHERE id = ? AND user_id = ? AND booking_date >= CURDATE()
        ");
        return $stmt->execute([$bookingId, $userId]);
    }
}