<?php
    require_once 'app/helpers/functions.php';
?>

<nav id="sidebarMenu" class="sidebar">
    <div class="sidebar-header d-flex justify-content-end align-items-center">
        <button class="btn text-white d-lg-none" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="sidebar-top">
        <div class="list-group list-group-flush">
            <a href="dashboard.php" class="list-group-item list-group-item-action ripple <?php echo isActive('dashboard.php'); ?>">
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

    <div class="d-flex justify-content-end align-items-center p-3">
        <button class="btn text-white" id="sidebarClose">
            <i class="fas fa-times fa-lg"></i>
        </button>
    </div>
    
    <div class="sidebar-top">
        <div class="list-group list-group-flush">
            <a href="dashboard.php" class="list-group-item list-group-item-action py-3 ripple <?php echo isActive('dashboard.php'); ?>">
                <i class="fas fa-chart-pie fa-fw me-3"></i><span>Dashboard</span>
            </a>
        </div>
    </div>
</nav>