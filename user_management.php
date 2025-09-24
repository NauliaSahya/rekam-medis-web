<?php
session_start();
require_once 'config/database.php';
require_once 'app/models/User.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$userModel = new User();

// Logika untuk menghapus pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $user_id = $_POST['user_id'];
    if ($userModel->deleteUser($user_id)) {
        $success = "Pengguna berhasil dihapus.";
    } else {
        $error = "Gagal menghapus pengguna.";
    }
}

// Logika untuk menambahkan pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    if ($userModel->addUser($username, $password, $role)) {
        $success = "Pengguna berhasil ditambahkan.";
    } else {
        $error = "Gagal menambahkan pengguna.";
    }
}

// Ambil data pengguna terbaru
$users = $userModel->getAllUsers();

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
    <title>Manajemen Pengguna</title>
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
                            <i class="fas fa-user-circle"></i>
                            <?php echo htmlspecialchars($_SESSION['role']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Profil</a></li>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { ?>
                                <li><a class="dropdown-item" href="user_management.php">Manajemen Pengguna</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php } ?>
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
                    <a href="dashboard.php" class="btn btn-secondary mb-4">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                    </a>
                    <h1 class="mb-4">Manajemen Pengguna</h1>
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h4 class="card-title">Tambah Pengguna Baru</h4>
                            <form action="user_management.php" method="POST">
                                <input type="hidden" name="action" value="add">
                                <div class="row g-3">
                                    <div class="col-md-4"><label class="form-label">Username</label><input type="text" class="form-control" name="username" required></div>
                                    <div class="col-md-4"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div>
                                    <div class="col-md-4"><label class="form-label">Peran</label>
                                        <select class="form-control" name="role" required>
                                            <option value="admin">Admin</option>
                                            <option value="dokter">Dokter</option>
                                            <option value="farmasi">Farmasi</option>
                                            <option value="laboratorium">Laboratorium</option>
                                            <option value="radiologi">Radiologi</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-3 d-flex justify-content-end"><button type="submit" class="btn btn-primary">Simpan Pengguna</button></div>
                            </form>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title">Daftar Pengguna</h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead><tr><th>ID</th><th>Username</th><th>Peran</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?php echo $user['user_id']; ?></td>
                                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                                <td>
                                                    <form action="user_management.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>