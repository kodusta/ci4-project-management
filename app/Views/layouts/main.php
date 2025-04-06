<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - Proje Yönetim Sistemi' : 'Proje Yönetim Sistemi' ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE 3 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <!-- Frappe Gantt CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
            --background-color: #f8f9fa;
            --text-color: #2c3e50;
            --light-text-color: #7f8c8d;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
        }
        
        .sidebar {
            background-color: var(--primary-color);
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background-color: var(--secondary-color);
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }
        
        .content-header h1 {
            font-size: 1.8rem;
            margin: 0;
            line-height: 1;
        }
        
        .content-header .breadcrumb {
            background-color: transparent;
            margin-bottom: 0;
        }
        
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .table th {
            background-color: rgba(0, 0, 0, 0.03);
        }
        
        .alert {
            border-radius: 0.5rem;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-user"></i> <?= session()->get('username') ?? 'Kullanıcı' ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="<?= base_url('auth/logout') ?>" class="dropdown-item">
                            <i class="fas fa-sign-out-alt mr-2"></i> Çıkış Yap
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="<?= base_url('dashboard') ?>" class="brand-link">
                <span class="brand-text font-weight-light">Proje Yönetimi</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="<?= base_url('dashboard') ?>" class="nav-link <?= current_url() == base_url('dashboard') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('projects') ?>" class="nav-link <?= strpos(current_url(), 'projects') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-project-diagram"></i>
                                <p>Projeler</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('customers') ?>" class="nav-link <?= strpos(current_url(), 'customers') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Müşteriler</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('categories') ?>" class="nav-link <?= strpos(current_url(), 'categories') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>Kategoriler</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('payments') ?>" class="nav-link <?= strpos(current_url(), 'payments') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-money-bill-wave"></i>
                                <p>Ödemeler</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('reports') ?>" class="nav-link <?= strpos(current_url(), 'reports') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Raporlar</p>
                            </a>
                        </li>
                        <!-- logout -->
                        <li class="nav-item">
                            <a href="<?= base_url('auth/logout') ?>" class="nav-link">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Çıkış Yap</p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><?= isset($title) ? $title : 'Dashboard' ?></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                                <?php if (isset($title)): ?>
                                <li class="breadcrumb-item active"><?= $title ?></li>
                                <?php endif; ?>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <footer class="main-footer">
            <div class="float-right d-none d-sm-inline">
                v1.0.0
            </div>
            <strong>Copyright &copy; <?= date('Y') ?> <a href="#">Proje Yönetim Sistemi</a>.</strong> Tüm hakları saklıdır.
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE 3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Frappe Gantt -->
    <script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>
    
    <script>
        // CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Auto-hide alerts after 5 seconds
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert.auto-hide').alert('close');
            }, 5000);
        });

        // Toastr yapılandırması
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Flash mesajlarını Toastr ile göster
        <?php if (session()->getFlashdata('message')) : ?>
            toastr.success('<?= session()->getFlashdata('message') ?>');
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            toastr.error('<?= session()->getFlashdata('error') ?>');
        <?php endif; ?>
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
