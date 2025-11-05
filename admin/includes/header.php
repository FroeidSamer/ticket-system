<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['login_id'])) {
    header('location:login.php');
    die();
}
$current_page = isset($_GET['page']) ? $_GET['page'] : 'home';

if ($_SESSION['login_type'] == 2 &&  ($current_page != 'staff'  && $current_page != 'other_staff')) {
    $stmt = $conn->query("SELECT 
    tw.id AS window_id,
    tw.transaction_id AS window_transaction_id,
    tw.transaction_ids AS window_transaction_ids,
    tw.name AS window_name,
    tw.status AS window_status,
    tr.name AS transaction_name,
    tr.type AS transaction_type
FROM transaction_windows tw 
INNER JOIN transactions tr ON tw.transaction_ids = tr.id 
WHERE tw.id = " . (int)$_SESSION['login_window_id']);

    $result = $stmt->fetch_assoc();
    $tr_type = $result['transaction_type'];
    if ($tr_type == 'sorting') {
        header('location: index.php?page=staff');
        die();
    } else {
        header('location: index.php?page=other_staff');
        die();
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Queueing System - Dashboard</title>
    <!-- <link href="assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet"> -->
    <link href="assets/DataTables/datatables.min.css" rel="stylesheet">
    <link href="assets/css/jquery.datetimepicker.min.css" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <script src="assets/vendor/jquery/jquery.min.js"></script>

    <script src="assets/DataTables/datatables.min.js"></script>
    <!-- <script src="assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script> -->
    <script type="text/javascript" src="assets/js/select2.min.js"></script>
    <!-- <script type="text/javascript" src="assets/js/jquery.datetimepicker.full.min.js"></script> -->

</head>

<style>
    .modal-dialog.large {
        width: 80% !important;
        max-width: unset;
    }

    .modal-dialog.mid-large {
        width: 50% !important;
        max-width: unset;
    }

    #viewer_modal .btn-close {
        position: absolute;
        z-index: 999999;
        right: -4.5em;
        background: unset;
        color: white;
        border: unset;
        font-size: 27px;
        top: 0;
    }

    #viewer_modal .modal-dialog {
        width: 80%;
        max-width: unset;
        height: calc(90%);
        max-height: unset;
    }

    #viewer_modal .modal-content {
        background: black;
        border: unset;
        height: calc(100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #viewer_modal img,
    #viewer_modal video {
        max-height: calc(100%);
        max-width: calc(100%);
    }
</style>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Q SYSTEM <sup>admin</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.php?page=home">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span><?= tr('dashboard') ?></span></a>
            </li>
            <?php if ($_SESSION['login_type'] == 1) : ?>
                <li class="nav-item active">
                    <a class="nav-link" href="index.php?page=stats">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span><?= tr('stats') ?></span></a>
                </li>
                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Heading -->
                <div class="sidebar-heading">
                    <?= tr('systemData') ?>
                </div>

                <!-- Nav Item - Pages Collapse Menu -->
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                        <i class="fas fa-fw fa-cog"></i>
                        <span><?= tr('sections') ?></span>
                    </a>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header"><?= tr('customsections') ?>:</h6>
                            <a class="collapse-item" href="index.php?page=transactions"><?= tr('sectionList') ?></a>
                            <a class="collapse-item" href="index.php?page=windows"><?= tr('windowList') ?></a>
                            <a class="collapse-item" href="index.php?page=conditions"><?= tr('patientsConditions') ?></a>
                        </div>
                    </div>
                </li>

                <!-- Nav Item - Users -->

                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=users">
                        <i class="fas fa-fw fa-wrench"></i>
                        <span><?= tr('users') ?></span></a>
                </li>
                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Heading -->
                <div class="sidebar-heading">
                    <?= tr('systemEdit') ?>
                </div>

                <!-- Nav Item - Pages Collapse Menu -->
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
                        <i class="fas fa-fw fa-folder"></i>
                        <span><?= tr('settings') ?></span>
                    </a>
                    <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header"><?= tr('general') ?>:</h6>
                            <a class="collapse-item" href="index.php?page=site_settings"><?= tr('systemInfo') ?></a>
                            <a class="collapse-item" href="index.php?page=uploads"><?= tr('mediaUploads') ?></a>
                            <div class="collapse-divider"></div>
                            <h6 class="collapse-header"><?= tr('ticket') ?>:</h6>
                            <a class="collapse-item" href="index.php?page=ticket"><?= tr('editTicket') ?></a>
                            <h6 class="collapse-header"><?= tr('others') ?>:</h6>
                            <a class="collapse-item" href="index.php?page=password"><?= tr('closePassword') ?></a>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBackup"
                        aria-expanded="false" aria-controls="collapseBackup">
                        <i class="fas fa-fw fa-database"></i>
                        <span><?= tr('Maintenance') ?></span>
                    </a>
                    <div id="collapseBackup" class="collapse" aria-labelledby="headingBackup" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header"><?= tr('Backup') ?>:</h6>
                            <a href="#" class="collapse-item" id="exportLink" onclick="exportDatabase(event)">
                                <i class="fas fa-database"></i><?= tr('Export_SQL_database') ?>
                            </a>
                            <h6 class="collapse-header"><?= tr('waiting_counter') ?>:</h6>
                            <a href="#" class="collapse-item" id="exportLink" onclick="(event)">
                                </i> <?= tr('Reset_Counter') ?>
                            </a>
                        </div>
                    </div>
                </li>



                <!-- Divider -->
                <hr class="sidebar-divider d-none d-md-block">
            <?php endif; ?>

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>



        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>



                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="langDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-language mr-2 text-black-900" style="font-size: 25px; color: black;"></i>

                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="langDropdown">
                                <a class="dropdown-item langBtn" href="#" id="arBtn" data-lang='ar'>
                                    <i class="fas fa-language fa-sm fa-fw mr-2 text-gray-400"></i>
                                    العربية
                                </a>
                                <a class="dropdown-item langBtn" href="#" id="enBtn" data-lang='en'>
                                    <i class="fas fa-language fa-sm fa-fw mr-2 text-gray-400"></i>
                                    English
                                </a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $_SESSION['login_name'] ?></span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    <?= tr('logout') ?>
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <script>
                        function exportDatabase(event) {
                            event.preventDefault(); // prevent link navigation

                            const btn = document.getElementById('exportLink');
                            const originalText = btn.innerHTML;

                            if (!confirm('هل تريد تصدير قاعدة البيانات كاملة؟\n\nسيتم تحميل ملف SQL يحتوي على:\n✓ جميع الجداول\n✓ جميع البيانات\n✓ الهيكل الكامل للقاعدة')) {
                                return;
                            }

                            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التصدير... الرجاء الانتظار';
                            btn.style.pointerEvents = 'none';

                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = 'export_database.php';
                            form.style.display = 'none';

                            document.body.appendChild(form);
                            form.submit();

                            setTimeout(() => {
                                btn.innerHTML = originalText;
                                btn.style.pointerEvents = 'auto';
                                document.body.removeChild(form);
                            }, 3000);
                        }
                    </script>