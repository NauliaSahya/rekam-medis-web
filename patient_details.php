<?php
session_start();
require_once 'config/database.php';
require_once 'app/models/Patient.php';

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
$encounters = $patientModel->getEncountersByMedRecordNumber($med_record_number);

if (!$patient) {
    echo "Data pasien tidak ditemukan.";
    exit();
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
    <title>Detail Pasien - <?php echo htmlspecialchars($patient['full_name']); ?></title>
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
                    <h1 class="mb-4">Detail Pasien</h1>
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h4 class="card-title"><?php echo htmlspecialchars($patient['full_name']); ?></h4>
                            <p class="card-text"><strong>Nomor RM:</strong> <?php echo htmlspecialchars($patient['med_record_number']); ?></p>
                            <p class="card-text"><strong>Tanggal Lahir:</strong> <?php echo htmlspecialchars($patient['date_of_birth']); ?></p>
                            <p class="card-text"><strong>Jenis Kelamin:</strong> <?php echo htmlspecialchars($patient['gender']); ?></p>
                            <a href="add_encounter.php?rm=<?php echo htmlspecialchars($patient['med_record_number']); ?>" class="btn btn-success mt-3">Tambah Rekam Medis Baru</a>
                        </div>
                    </div>

                    <h2 class="mt-5">Riwayat Rekam Medis</h2>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nomor Registrasi</th>
                                    <th>Tanggal Kunjungan</th>
                                    <th>Diagnosis</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($encounters)): ?>
                                    <?php foreach ($encounters as $encounter): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($encounter['registration_number']); ?></td>
                                        <td><?php echo htmlspecialchars($encounter['visit_date']); ?></td>
                                        <td><?php echo htmlspecialchars($encounter['diagnosis']); ?></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-info text-white">Lihat Detail</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada riwayat rekam medis.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>