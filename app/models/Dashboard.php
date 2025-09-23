<?php
class Dashboard {
    private $conn;
    public function __construct() {
        require_once 'config/database.php';
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }
    }
    public function getPatientCount() {
        $sql = "SELECT COUNT(*) as count FROM patients";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc()['count'];
    }
    public function getTodayVisitsCount() {
        $sql = "SELECT COUNT(*) as count FROM encounters WHERE DATE(created_at) = CURDATE()";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc()['count'];
    }
    public function getDrugStockCount() {
        $sql = "SELECT SUM(stock) as total_stock FROM pharmacy_stock";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc()['total_stock'] ?? 0;
    }
    public function getActiveDoctorCount() {
        $sql = "SELECT COUNT(*) as count FROM users WHERE role = 'dokter'";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc()['count'];
    }
    public function __destruct() {
        $this->conn->close();
    }
}
?>