<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard' ?> - WebGIS Wisata</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            --sidebar-bg: #0f172a;
            --sidebar-hover: rgba(255, 255, 255, 0.1);
            --sidebar-active: #6366f1;
            --bg-body: #f8fafc;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius-xl: 1rem;
            --radius-lg: 0.75rem;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-body);
            color: #1e293b;
            overflow-x: hidden;
        }

        /* Sidebar Wrapper */
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

        #sidebar {
            min-width: 280px;
            max-width: 280px;
            background: var(--sidebar-bg);
            color: #94a3b8;
            transition: all 0.3s ease;
            min-height: 100vh;
            position: fixed;
            z-index: 1001;
        }

        #sidebar.active {
            margin-left: -280px;
        }

        #sidebar .sidebar-header {
            padding: 2rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #fff;
        }

        #sidebar .sidebar-header i {
            font-size: 1.75rem;
            color: var(--sidebar-active);
        }

        #sidebar ul.components {
            padding: 0 1rem;
        }

        #sidebar ul li a {
            padding: 0.875rem 1rem;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: var(--radius-lg);
            transition: all 0.2s ease;
            margin-bottom: 0.25rem;
        }

        #sidebar ul li a:hover {
            background: var(--sidebar-hover);
            color: #fff;
            transform: translateX(5px);
        }

        #sidebar ul li.active > a {
            background: var(--primary-gradient);
            color: #fff;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        }

        #sidebar ul li a i {
            font-size: 1.25rem;
        }

        /* Page Content */
        #content {
            width: 100%;
            padding-left: 280px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        #content.active {
            padding-left: 0;
        }

        /* Topbar */
        .topbar {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .toggle-btn {
            background: transparent;
            border: none;
            font-size: 1.5rem;
            color: #64748b;
            cursor: pointer;
        }

        /* User Profile in Topbar */
        .user-nav {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #1e293b;
            font-weight: 500;
        }

        .user-nav img {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        /* Main Content Area */
        .main-content {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Modern Card */
        .card {
            border: none;
            border-radius: var(--radius-xl);
            box-shadow: var(--card-shadow);
            background: #fff;
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.5rem;
            font-weight: 600;
            font-size: 1.125rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Buttons */
        .btn {
            border-radius: 0.5rem;
            padding: 0.625rem 1.25rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        }

        /* Table Styles */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            #sidebar {
                margin-left: -280px;
            }
            #sidebar.active {
                margin-left: 0;
            }
            #content {
                padding-left: 0;
            }
            #content.active {
                padding-left: 0;
            }
            .main-content {
                padding: 1rem;
            }
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Badges */
        .badge {
            padding: 0.5em 0.75em;
            border-radius: 0.375rem;
            font-weight: 500;
        }

        .bg-primary-soft { background-color: rgba(99, 102, 241, 0.1); color: #4f46e5; }
        .bg-success-soft { background-color: rgba(34, 197, 94, 0.1); color: #16a34a; }
        .bg-warning-soft { background-color: rgba(245, 158, 11, 0.1); color: #d97706; }
        .bg-danger-soft { background-color: rgba(239, 68, 68, 0.1); color: #dc2626; }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>

<div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-geo-fill"></i>
            <span class="fs-4 fw-bold">AdminGIS</span>
        </div>

        <ul class="list-unstyled components">
            <li class="<?= (current_url() == site_url('admin/dashboard')) ? 'active' : '' ?>">
                <a href="<?= site_url('admin/dashboard') ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            </li>
            <li class="<?= strpos(current_url(), '/admin/kategori') !== false ? 'active' : '' ?>">
                <a href="<?= site_url('admin/kategori') ?>"><i class="bi bi-tags-fill"></i> Kategori</a>
            </li>
            <li class="<?= strpos(current_url(), '/admin/wisata') !== false ? 'active' : '' ?>">
                <a href="<?= site_url('admin/wisata') ?>"><i class="bi bi-geo-alt-fill"></i> Data Wisata</a>
            </li>
            <li class="<?= strpos(current_url(), '/admin/galeri') !== false ? 'active' : '' ?>">
                <a href="<?= site_url('admin/galeri') ?>"><i class="bi bi-images"></i> Galeri</a>
            </li>
            <li class="mt-5">
                <a href="<?= site_url('admin/logout') ?>" class="text-danger-emphasis"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </li>
        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
        <nav class="topbar">
            <button type="button" id="sidebarCollapse" class="toggle-btn">
                <i class="bi bi-list"></i>
            </button>
            
            <div class="user-nav">
                <span class="d-none d-sm-inline">Halo, <?= ucfirst(session()->get('admin_name') ?? 'Admin') ?></span>
                <img src="https://ui-avatars.com/api/?name=<?= session()->get('admin_name') ?? 'A' ?>&background=6366f1&color=fff" alt="User">
            </div>
        </nav>

        <div class="main-content fade-in">
            <?php if(session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebarCollapse = document.getElementById('sidebarCollapse');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');

        sidebarCollapse.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            content.classList.toggle('active');
        });

        // Close sidebar on mobile when clicking outside or resizing
        window.addEventListener('resize', function() {
            if (window.innerWidth <= 992) {
                sidebar.classList.remove('active');
                content.classList.remove('active');
            }
        });
    });
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
