<?php
session_start();
require_once 'config/database.php';
require_once 'app/models/Patient.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'dokter')) {
    header('Location: login.php');
    exit();
}

// Fungsi untuk menghasilkan nomor rekam medis otomatis
function generateNextMedRecordNumber($conn) {
    $query = "SELECT MAX(med_record_number) AS max_num FROM patients";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $lastNumber = $row['max_num'];
    $nextNumber = $lastNumber ? intval($lastNumber) + 1 : 1;
    return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $med_record_number = generateNextMedRecordNumber($conn);
    $full_name = $_POST['full_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];

    $stmt = $conn->prepare("INSERT INTO patients (med_record_number, full_name, date_of_birth, gender, address, phone_number) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $med_record_number, $full_name, $date_of_birth, $gender, $address, $phone_number);

    if ($stmt->execute()) {
        $success = "Data pasien berhasil ditambahkan dengan Nomor RM: " . $med_record_number;
    } else {
        $error = "Gagal menambahkan data pasien: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();

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
    <title>Tambah Pasien Baru</title>
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
                    <h1 class="mb-4">Tambah Pasien Baru</h1>
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <?php if (isset($success)): ?>
                                <div class="alert alert-success"><?php echo $success; ?></div>
                            <?php endif; ?>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            <form action="add_patient.php" method="POST">
                                <div class="mb-3"><label for="full_name" class="form-label">Nama Lengkap</label><input type="text" class="form-control" id="full_name" name="full_name" required></div>
                                <div class="row">
                                    <div class="col-md-6 mb-3"><label for="date_of_birth" class="form-label">Tanggal Lahir</label><input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required></div>
                                    <div class="col-md-6 mb-3"><label for="gender" class="form-label">Jenis Kelamin</label><select class="form-control" id="gender" name="gender" required><option value="">Pilih...</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select></div>
                                </div>
                                <div class="mb-3"><label for="address" class="form-label">Alamat</label><textarea class="form-control" id="address" name="address" rows="3"></textarea></div>
                                <div class="mb-3"><label for="phone_number" class="form-label">Nomor Telepon</label><input type="tel" class="form-control" id="phone_number" name="phone_number"></div>
                                <div class="d-flex justify-content-end"><button type="submit" class="btn btn-primary">Simpan</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>