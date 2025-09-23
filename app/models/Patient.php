<?php
class Patient {
    private $conn;

    public function __construct() {
        require_once 'config/database.php';
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }
    }

    public function getAllPatients() {
        $sql = "SELECT * FROM patients ORDER BY created_at DESC";
        $result = $this->conn->query($sql);
        $patients = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $patients[] = $row;
            }
        }
        return $patients;
    }

    public function getPatientByMedRecordNumber($med_record_number) {
        $stmt = $this->conn->prepare("SELECT * FROM patients WHERE med_record_number = ?");
        $stmt->bind_param("s", $med_record_number);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getEncountersByMedRecordNumber($med_record_number) {
        $stmt = $this->conn->prepare("SELECT * FROM encounters WHERE med_record_number = ? ORDER BY visit_date DESC");
        $stmt->bind_param("s", $med_record_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $encounters = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $encounters[] = $row;
            }
        }
        return $encounters;
    }

    public function __destruct() {
        $this->conn->close();
    }
}
?>