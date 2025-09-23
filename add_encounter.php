<?php
session_start();
require_once 'config/database.php';
require_once 'app/models/Patient.php';
require_once 'app/models/Encounter.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'dokter')) {
    header('Location: login.php');
    exit();
}

$med_record_number = $_GET['rm'] ?? '';
if (empty($med_record_number)) {
    header('Location: patients.php');
    exit();
}

$patientModel = new Patient();
$patient = $patientModel->getPatientByMedRecordNumber($med_record_number);

if (!$patient) {
    echo "Data pasien tidak ditemukan.";
    exit();
}

$encounterModel = new Encounter();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration_number = $encounterModel->generateNextRegistrationNumber();
    $visit_date = $_POST['visit_date'];
    $diagnosis = $_POST['diagnosis'];
    
    $vitals = [
        'systolic' => $_POST['vitals_systolic'],
        'diastolic' => $_POST['vitals_diastolic'],
        'heart_rate' => $_POST['vitals_heart_rate'],
        'respiratory_rate' => $_POST['vitals_respiratory_rate'],
        'temperature' => $_POST['vitals_temperature'],
        'oxygen_saturation' => $_POST['vitals_oxygen_saturation'],
        'weight' => $_POST['vitals_weight']
    ];

    $encounterData = [
        'registration_number' => $registration_number,
        'med_record_number' => $med_record_number,
        'visit_date' => $visit_date,
        'diagnosis' => $diagnosis,
        'vitals_systolic' => $vitals['systolic'],
        'vitals_diastolic' => $vitals['diastolic'],
        'vitals_heart_rate' => $vitals['heart_rate'],
        'vitals_respiratory_rate' => $vitals['respiratory_rate'],
        'vitals_temperature' => $vitals['temperature'],
        'vitals_oxygen_saturation' => $vitals['oxygen_saturation'],
        'vitals_weight' => $vitals['weight'],
        'treatment' => $_POST['treatment'] ?? null,
        'disposition' => $_POST['disposition'] ?? null,
        'chronic_diseases' => $_POST['chronic_diseases'] ?? null,
        'created_by' => $_SESSION['user_id']
    ];

    $encounter_id = $encounterModel->addEncounter($encounterData);

    if ($encounter_id) {
        if (isset($_POST['drug_name'])) {
            foreach ($_POST['drug_name'] as $index => $drug_name) {
                if (!empty($drug_name)) {
                    $dosage = $_POST['dosage'][$index] ?? '';
                    $frequency = $_POST['frequency'][$index] ?? '';
                    $duration = $_POST['duration'][$index] ?? '';
                    $encounterModel->addPrescription($encounter_id, $drug_name, $dosage, $frequency, $duration);
                }
            }
        }
        $success = "Rekam medis berhasil ditambahkan dengan Nomor Registrasi: " . $registration_number;
    } else {
        $error = "Gagal menambahkan rekam medis.";
    }
}

function isActive($pageName) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    return $currentPage === $pageName ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Rekam Medis Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold ms-3" href="#">APLIKASI REKAM MEDIS</a>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['role']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="d-flex" id="wrapper">
        <nav id="sidebarMenu" class="sidebar">
            <div class="sidebar-top">
                <div class="list-group list-group-flush">
                    <a href="dashboard.php" class="list-group-item list-group-item-action py-3 ripple <?php echo isActive('dashboard.php'); ?>">
                        <i class="fas fa-chart-pie fa-fw me-3"></i><span>Dashboard</span>
                    </a>
                </div>
            </div>
            <div class="sidebar-menu">
                <h6 class="menu-title">MENU</h6>
                <div class="list-group list-group-flush">
                    <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'dokter') { ?>
                        <a href="patients.php" class="list-group-item list-group-item-action py-3 ripple <?php echo isActive('patients.php'); ?>">
                            <i class="fas fa-notes-medical fa-fw me-3"></i><span>Rekam Medis</span>
                        </a>
                    <?php } ?>
                    <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'farmasi') { ?>
                        <a href="pharmacy.php" class="list-group-item list-group-item-action py-3 ripple <?php echo isActive('pharmacy.php'); ?>">
                            <i class="fas fa-pills fa-fw me-3"></i><span>Farmasi</span>
                        </a>
                    <?php } ?>
                    <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'laboratorium') { ?>
                        <a href="lab.php" class="list-group-item list-group-item-action py-3 ripple <?php echo isActive('lab.php'); ?>">
                            <i class="fas fa-microscope fa-fw me-3"></i><span>Laboratorium</span>
                        </a>
                    <?php } ?>
                    <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'radiologi') { ?>
                        <a href="radiology.php" class="list-group-item list-group-item-action py-3 ripple <?php echo isActive('radiology.php'); ?>">
                            <i class="fas fa-x-ray fa-fw me-3"></i><span>Radiologi</span>
                        </a>
                    <?php } ?>
                </div>
            </div>
            <div class="sidebar-bottom">
                <div class="list-group list-group-flush">
                    <a href="logout.php" class="list-group-item list-group-item-action py-3 ripple text-danger">
                        <i class="fas fa-sign-out-alt fa-fw me-3"></i><span>Logout</span>
                    </a>
                </div>
            </div>
        </nav>
        <div id="page-content-wrapper">
            <main class="content">
                <div class="container-fluid py-4">
                    <h1 class="mb-4">Tambah Rekam Medis</h1>
                    <div class="alert alert-info">
                        <strong>Pasien:</strong> <?php echo htmlspecialchars($patient['full_name']); ?> (Nomor RM: <?php echo htmlspecialchars($patient['med_record_number']); ?>)
                    </div>
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form action="add_encounter.php?rm=<?php echo htmlspecialchars($med_record_number); ?>" method="POST">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Informasi Kunjungan</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="visit_date" class="form-label">Tanggal Kunjungan</label>
                                    <input type="date" class="form-control" id="visit_date" name="visit_date" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="diagnosis" class="form-label">Diagnosis</label>
                                    <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Vital Signs</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label">Tekanan Darah (Sistolik)</label><input type="number" class="form-control" name="vitals_systolic"></div>
                                    <div class="col-md-6"><label class="form-label">Tekanan Darah (Diastolik)</label><input type="number" class="form-control" name="vitals_diastolic"></div>
                                    <div class="col-md-6"><label class="form-label">Detak Jantung</label><input type="number" class="form-control" name="vitals_heart_rate"></div>
                                    <div class="col-md-6"><label class="form-label">Respirasi</label><input type="number" class="form-control" name="vitals_respiratory_rate"></div>
                                    <div class="col-md-6"><label class="form-label">Suhu (°C)</label><input type="number" step="0.1" class="form-control" name="vitals_temperature"></div>
                                    <div class="col-md-6"><label class="form-label">Saturasi Oksigen (%)</label><input type="number" class="form-control" name="vitals_oxygen_saturation"></div>
                                    <div class="col-md-6"><label class="form-label">Berat (kg)</label><input type="number" step="0.1" class="form-control" name="vitals_weight"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Resep Obat</h5>
                            </div>
                            <div class="card-body" id="prescriptions-container">
                                <div class="row g-3 prescription-row mb-3">
                                    <div class="col-md-3"><label class="form-label">Nama Obat</label><input type="text" class="form-control" name="drug_name[]"></div>
                                    <div class="col-md-3"><label class="form-label">Dosis</label><input type="text" class="form-control" name="dosage[]"></div>
                                    <div class="col-md-3"><label class="form-label">Frekuensi</label><input type="text" class="form-control" name="frequency[]"></div>
                                    <div class="col-md-2"><label class="form-label">Durasi</label><input type="text" class="form-control" name="duration[]"></div>
                                    <div class="col-md-1 d-flex align-items-end"><button type="button" class="btn btn-danger remove-prescription" style="display:none;">-</button></div>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <button type="button" class="btn btn-outline-primary" id="add-prescription-btn">Tambah Resep</button>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Simpan Rekam Medis</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('add-prescription-btn').addEventListener('click', function() {
            const container = document.getElementById('prescriptions-container');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'prescription-row', 'mb-3');
            newRow.innerHTML = `
                <div class="col-md-3"><label class="form-label">Nama Obat</label><input type="text" class="form-control" name="drug_name[]"></div>
                <div class="col-md-3"><label class="form-label">Dosis</label><input type="text" class="form-control" name="dosage[]"></div>
                <div class="col-md-3"><label class="form-label">Frekuensi</label><input type="text" class="form-control" name="frequency[]"></div>
                <div class="col-md-2"><label class="form-label">Durasi</label><input type="text" class="form-control" name="duration[]"></div>
                <div class="col-md-1 d-flex align-items-end"><button type="button" class="btn btn-danger remove-prescription">-</button></div>
            `;
            container.appendChild(newRow);
            updateRemoveButtons();
        });

        document.getElementById('prescriptions-container').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-prescription')) {
                e.target.closest('.prescription-row').remove();
                updateRemoveButtons();
            }
        });

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.prescription-row');
            rows.forEach((row, index) => {
                const removeBtn = row.querySelector('.remove-prescription');
                if (rows.length > 1) {
                    removeBtn.style.display = 'block';
                } else {
                    removeBtn.style.display = 'none';
                }
            });
        }
        updateRemoveButtons();
    </script>
</body>
</html>