<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }
        
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        .navbar-brand {
            padding-top: .75rem;
            padding-bottom: .75rem;
        }
        
        .navbar .navbar-toggler {
            top: .25rem;
            right: 1rem;
        }
        
        main {
            margin-left: 240px;
            padding: 20px;
        }
        
        .nav-link {
            color: #333;
        }
        
        .nav-link:hover {
            color: #007bff;
        }
        
        .nav-link.active {
            color: #007bff;
            font-weight: bold;
        }
        
        .status-badge {
            font-size: 0.8em;
        }
        
        .priority-badge {
            font-size: 0.8em;
        }
        
        .stats-card {
            border-left: 4px solid #007bff;
        }
        
        @media (max-width: 767.98px) {
            .sidebar {
                top: 5rem;
            }
            main {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <?php if (is_logged_in()): ?>
    <!-- Navigation -->
    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="index.php?page=dashboard">
            <i class="fas fa-users"></i> <?php echo APP_NAME; ?>
        </a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav">
            <div class="nav-item text-nowrap">
                <div class="dropdown">
                    <a class="nav-link px-3 dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="index.php?page=profile"><i class="fas fa-user-cog"></i> Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="index.php?page=logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($_GET['page'] ?? '') === 'dashboard' ? 'active' : ''; ?>" href="index.php?page=dashboard">
                                <i class="fas fa-tachometer-alt"></i> Panel de Control
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($_GET['page'] ?? '') === 'requests' ? 'active' : ''; ?>" href="index.php?page=requests">
                                <i class="fas fa-clipboard-list"></i> Solicitudes
                            </a>
                        </li>
                        <?php if (in_array($_SESSION['user_role'], ['admin', 'representante'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($_GET['page'] ?? '') === 'workers' ? 'active' : ''; ?>" href="index.php?page=workers">
                                <i class="fas fa-hard-hat"></i> Trabajadores
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($_GET['page'] ?? '') === 'users' ? 'active' : ''; ?>" href="index.php?page=users">
                                <i class="fas fa-users-cog"></i> Usuarios del Sistema
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (in_array($_SESSION['user_role'], ['admin', 'representante'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($_GET['page'] ?? '') === 'reports' ? 'active' : ''; ?>" href="index.php?page=reports">
                                <i class="fas fa-chart-bar"></i> Reportes
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($_GET['page'] ?? '') === 'faq' ? 'active' : ''; ?>" href="index.php?page=faq">
                                <i class="fas fa-question-circle"></i> Preguntas Frecuentes
                            </a>
                        </li>
                    </ul>

                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Acceso Rápido</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=requests&action=create">
                                <i class="fas fa-plus"></i> Nueva Solicitud
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=public_form" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Formulario Público
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php
                // Display flash messages
                $flash_messages = get_flash_messages();
                foreach ($flash_messages as $message):
                ?>
                <div class="alert alert-<?php echo $message['type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endforeach; ?>

                <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                    switch ($_GET['error']) {
                        case 'access_denied':
                            echo 'No tienes permisos para acceder a esta sección.';
                            break;
                        case 'invalid_request':
                            echo 'Solicitud inválida.';
                            break;
                        case 'not_found':
                            echo 'El recurso solicitado no fue encontrado.';
                            break;
                        default:
                            echo 'Ha ocurrido un error.';
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    switch ($_GET['success']) {
                        case 'created':
                            echo 'Registro creado exitosamente.';
                            break;
                        case 'updated':
                            echo 'Registro actualizado exitosamente.';
                            break;
                        case 'deleted':
                            echo 'Registro eliminado exitosamente.';
                            break;
                        default:
                            echo 'Operación completada exitosamente.';
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
    <?php else: ?>
    <div class="container-fluid">
    <?php endif; ?>