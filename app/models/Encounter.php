<?php
class Encounter {
    private $conn;

    public function __construct() {
        require_once 'config/database.php';
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }
    }

    public function addEncounter($data) {
        $stmt = $this->conn->prepare("INSERT INTO encounters (registration_number, med_record_number, visit_date, diagnosis, vitals_systolic, vitals_diastolic, vitals_heart_rate, vitals_respiratory_rate, vitals_temperature, vitals_oxygen_saturation, vitals_weight, treatment, disposition, chronic_diseases, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiiiiidissisi", 
            $data['registration_number'],
            $data['med_record_number'],
            $data['visit_date'],
            $data['diagnosis'],
            $data['vitals_systolic'],
            $data['vitals_diastolic'],
            $data['vitals_heart_rate'],
            $data['vitals_respiratory_rate'],
            $data['vitals_temperature'],
            $data['vitals_oxygen_saturation'],
            $data['vitals_weight'],
            $data['treatment'],
            $data['disposition'],
            $data['chronic_diseases'],
            $data['created_by']
        );

        if ($stmt->execute()) {
            return $stmt->insert_id;
        } else {
            return false;
        }
    }

    public function addPrescription($encounter_id, $drug_name, $dosage, $frequency, $duration) {
        $stmt = $this->conn->prepare("INSERT INTO prescriptions (encounter_id, drug_name, dosage, frequency, duration) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $encounter_id, $drug_name, $dosage, $frequency, $duration);
        return $stmt->execute();
    }

    public function generateNextRegistrationNumber() {
        $query = "SELECT MAX(registration_number) AS max_num FROM encounters";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        $lastNumber = $row['max_num'];
        $nextNumber = $lastNumber ? intval($lastNumber) + 1 : 1;
        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
    
    public function __destruct() {
        $this->conn->close();
    }
}
?>