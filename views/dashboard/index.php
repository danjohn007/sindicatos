<?php
require_once __DIR__ . '/../../includes/functions.php';
// Ensure current_user is properly initialized to prevent undefined variable warnings
if (!isset($current_user) || !$current_user) {
    $current_user = get_logged_user();
    if (!$current_user) {
        // Redirect to login if user data is not available
        header('Location: index.php?page=login');
        exit;
    }
}

// Ensure configuration constants are available (should be included via config/config.php)
if (!defined('USER_ROLES')) {
    require_once __DIR__ . '/../../config/config.php';
}
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-tachometer-alt"></i> Panel de Control
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="index.php?page=requests&action=create" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Nueva Solicitud
            </a>
        </div>
    </div>
</div>

<!-- Welcome Message -->
<div class="alert alert-info" role="alert">
    <h4 class="alert-heading">
        <i class="fas fa-hand-wave"></i> Bienvenido, <?php echo htmlspecialchars($current_user['full_name']); ?>
    </h4>
    <p>Este es su panel de control del Sistema CRM de Sindicatos. Desde aquí puede monitorear todas las solicitudes, quejas y sugerencias de los trabajadores.</p>
    <hr>
    <p class="mb-0">
        <strong>Rol:</strong> <?php echo USER_ROLES[$current_user['role']]; ?>
        <?php if ($current_user['department']): ?>
        | <strong>Departamento:</strong> <?php echo DEPARTMENTS[$current_user['department']]; ?>
        <?php endif; ?>
    </p>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 stats-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de Solicitudes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total_requests'] ?? 0); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2" style="border-left: 4px solid #f6c23e;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Nuevas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['new_requests'] ?? 0); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2" style="border-left: 4px solid #36b9cc;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">En Proceso</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format(($stats['in_review'] ?? 0) + ($stats['escalated'] ?? 0)); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cog fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2" style="border-left: 4px solid #1cc88a;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Resueltas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format(($stats['resolved'] ?? 0) + ($stats['closed'] ?? 0)); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Critical Alerts -->
<?php if ($stats['critical_open'] > 0): ?>
<div class="alert alert-danger" role="alert">
    <h5 class="alert-heading">
        <i class="fas fa-exclamation-triangle"></i> Solicitudes Críticas
    </h5>
    <p>Hay <strong><?php echo $stats['critical_open']; ?></strong> solicitud(es) crítica(s) que requieren atención inmediata.</p>
    <hr>
    <a href="index.php?page=requests&priority=critica&status=recibido,en_revision,escalado" class="btn btn-danger btn-sm">
        <i class="fas fa-eye"></i> Ver Solicitudes Críticas
    </a>
</div>
<?php endif; ?>

<div class="row">
    <!-- Charts Section -->
    <div class="col-lg-8">
        <!-- Requests by Department Chart -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie"></i> Solicitudes por Departamento
                </h6>
            </div>
            <div class="card-body">
                <canvas id="departmentChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Request Types Chart -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar"></i> Tipos de Solicitudes
                </h6>
            </div>
            <div class="card-body">
                <canvas id="typeChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Requests and My Tasks -->
    <div class="col-lg-4">
        <!-- My Assigned Requests (for non-admin users) -->
        <?php if ($current_user['role'] !== 'admin' && !empty($my_requests)): ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tasks"></i> Mis Solicitudes Asignadas
                </h6>
            </div>
            <div class="card-body">
                <?php foreach ($my_requests as $request): ?>
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <strong><?php echo htmlspecialchars($request['folio']); ?></strong><br>
                        <small class="text-muted"><?php echo htmlspecialchars(substr($request['subject'], 0, 40)) . '...'; ?></small><br>
                        <span class="badge bg-<?php echo $request['status'] === 'recibido' ? 'warning' : ($request['status'] === 'resuelto' ? 'success' : 'info'); ?>"><?php echo REQUEST_STATUS[$request['status']]; ?></span>
                    </div>
                    <div>
                        <a href="index.php?page=requests&action=view&id=<?php echo $request['id']; ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
                <div class="text-center mt-3">
                    <a href="index.php?page=requests&assigned_to=<?php echo $current_user['id']; ?>" class="btn btn-sm btn-primary">
                        Ver todas mis solicitudes
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Requests -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clock"></i> Solicitudes Recientes
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_data)): ?>
                    <?php foreach (array_slice($recent_data, 0, 5) as $request): ?>
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>
                            <strong><?php echo htmlspecialchars($request['folio']); ?></strong><br>
                            <small class="text-muted"><?php echo htmlspecialchars(substr($request['subject'], 0, 30)) . '...'; ?></small><br>
                            <span class="badge bg-<?php echo $request['request_type'] === 'queja' ? 'danger' : ($request['request_type'] === 'sugerencia' ? 'info' : 'secondary'); ?>"><?php echo REQUEST_TYPES[$request['request_type']]; ?></span>
                            <small class="text-muted d-block"><?php echo time_ago($request['created_at']); ?></small>
                        </div>
                        <div>
                            <a href="index.php?page=requests&action=view&id=<?php echo $request['id']; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <div class="text-center mt-3">
                        <a href="index.php?page=requests" class="btn btn-sm btn-primary">
                            Ver todas las solicitudes
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No hay solicitudes recientes</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle"></i> Estadísticas Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h5 class="text-primary"><?php echo number_format($stats['avg_resolution_hours'] ?? 0, 1); ?></h5>
                        <small class="text-muted">Horas promedio de resolución</small>
                    </div>
                    <div class="col-6">
                        <h5 class="text-warning"><?php echo $stats['critical_open'] ?? 0; ?></h5>
                        <small class="text-muted">Críticas abiertas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Department Chart
<?php if (!empty($department_data)): ?>
const deptCtx = document.getElementById('departmentChart').getContext('2d');
const departmentChart = new Chart(deptCtx, {
    type: 'doughnut',
    data: {
        labels: [
            <?php foreach ($department_data as $dept): ?>
            '<?php echo DEPARTMENTS[$dept['department']]; ?>',
            <?php endforeach; ?>
        ],
        datasets: [{
            data: [
                <?php foreach ($department_data as $dept): ?>
                <?php echo $dept['total']; ?>,
                <?php endforeach; ?>
            ],
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});
<?php endif; ?>

// Type Chart
<?php if (!empty($type_data)): ?>
const typeCtx = document.getElementById('typeChart').getContext('2d');
const typeChart = new Chart(typeCtx, {
    type: 'bar',
    data: {
        labels: [
            <?php foreach ($type_data as $type): ?>
            '<?php echo REQUEST_TYPES[$type['request_type']]; ?>',
            <?php endforeach; ?>
        ],
        datasets: [{
            label: 'Total',
            data: [
                <?php foreach ($type_data as $type): ?>
                <?php echo $type['total']; ?>,
                <?php endforeach; ?>
            ],
            backgroundColor: '#4e73df'
        }, {
            label: 'Resueltas',
            data: [
                <?php foreach ($type_data as $type): ?>
                <?php echo $type['resolved']; ?>,
                <?php endforeach; ?>
            ],
            backgroundColor: '#1cc88a'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
<?php endif; ?>
</script>
