<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-clipboard-list"></i> Gestión de Solicitudes
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="index.php?page=requests&action=create" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Nueva Solicitud
            </a>
            <a href="index.php?page=reports" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-chart-bar"></i> Reportes
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-filter"></i> Filtros
            <button class="btn btn-sm btn-outline-secondary float-end" type="button" data-bs-toggle="collapse" data-bs-target="#filters" aria-expanded="false">
                <i class="fas fa-chevron-down"></i>
            </button>
        </h5>
    </div>
    <div class="collapse" id="filters">
        <div class="card-body">
            <form method="GET" action="index.php">
                <input type="hidden" name="page" value="requests">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Folio, asunto, trabajador..." 
                               value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="department" class="form-label">Departamento</label>
                        <select class="form-select" id="department" name="department">
                            <option value="">Todos</option>
                            <?php foreach (DEPARTMENTS as $key => $name): ?>
                            <option value="<?php echo $key; ?>" <?php echo ($_GET['department'] ?? '') === $key ? 'selected' : ''; ?>>
                                <?php echo $name; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="status" class="form-label">Estado</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Todos</option>
                            <?php foreach (REQUEST_STATUS as $key => $name): ?>
                            <option value="<?php echo $key; ?>" <?php echo ($_GET['status'] ?? '') === $key ? 'selected' : ''; ?>>
                                <?php echo $name; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="request_type" class="form-label">Tipo</label>
                        <select class="form-select" id="request_type" name="request_type">
                            <option value="">Todos</option>
                            <?php foreach (REQUEST_TYPES as $key => $name): ?>
                            <option value="<?php echo $key; ?>" <?php echo ($_GET['request_type'] ?? '') === $key ? 'selected' : ''; ?>>
                                <?php echo $name; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="priority" class="form-label">Prioridad</label>
                        <select class="form-select" id="priority" name="priority">
                            <option value="">Todas</option>
                            <option value="baja" <?php echo ($_GET['priority'] ?? '') === 'baja' ? 'selected' : ''; ?>>Baja</option>
                            <option value="media" <?php echo ($_GET['priority'] ?? '') === 'media' ? 'selected' : ''; ?>>Media</option>
                            <option value="alta" <?php echo ($_GET['priority'] ?? '') === 'alta' ? 'selected' : ''; ?>>Alta</option>
                            <option value="critica" <?php echo ($_GET['priority'] ?? '') === 'critica' ? 'selected' : ''; ?>>Crítica</option>
                        </select>
                    </div>
                    <div class="col-md-1 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="date_from" class="form-label">Desde</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date_to" class="form-label">Hasta</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="index.php?page=requests" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Limpiar Filtros
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Results Summary -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <span class="text-muted">
            Mostrando <?php echo count($requests); ?> de <?php echo number_format($pagination['total_records']); ?> solicitudes
        </span>
    </div>
    <div>
        <div class="btn-group btn-group-sm" role="group">
            <input type="radio" class="btn-check" name="view" id="view-table" autocomplete="off" checked>
            <label class="btn btn-outline-secondary" for="view-table">
                <i class="fas fa-table"></i> Tabla
            </label>
            <input type="radio" class="btn-check" name="view" id="view-cards" autocomplete="off">
            <label class="btn btn-outline-secondary" for="view-cards">
                <i class="fas fa-th-large"></i> Tarjetas
            </label>
        </div>
    </div>
</div>

<!-- Requests Table -->
<div id="table-view">
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Folio</th>
                    <th>Trabajador</th>
                    <th>Departamento</th>
                    <th>Tipo</th>
                    <th>Asunto</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Asignado a</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($requests)): ?>
                    <?php foreach ($requests as $request): ?>
                    <tr class="<?php echo $request['priority'] === 'critica' ? 'table-danger' : ''; ?>">
                        <td>
                            <strong><?php echo htmlspecialchars($request['folio']); ?></strong>
                            <?php if ($request['is_confidential']): ?>
                            <i class="fas fa-lock text-warning ms-1" title="Confidencial"></i>
                            <?php endif; ?>
                            <?php if ($request['is_anonymous']): ?>
                            <i class="fas fa-user-secret text-info ms-1" title="Anónimo"></i>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($request['is_anonymous']): ?>
                                <em>Anónimo</em>
                            <?php else: ?>
                                <?php echo htmlspecialchars($request['worker_full_name'] ?: $request['worker_name']); ?>
                                <?php if ($request['worker_number']): ?>
                                <br><small class="text-muted"><?php echo htmlspecialchars($request['worker_number']); ?></small>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                <?php echo DEPARTMENTS[$request['department']]; ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $request['request_type'] === 'queja' ? 'danger' : ($request['request_type'] === 'sugerencia' ? 'info' : 'secondary'); ?>">
                                <?php echo REQUEST_TYPES[$request['request_type']]; ?>
                            </span>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($request['subject']); ?>">
                                <?php echo htmlspecialchars($request['subject']); ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $request['priority'] === 'critica' ? 'danger' : 
                                    ($request['priority'] === 'alta' ? 'warning' : 
                                    ($request['priority'] === 'media' ? 'info' : 'secondary')); 
                            ?>">
                                <?php echo ucfirst($request['priority']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $request['status'] === 'recibido' ? 'warning' : 
                                    ($request['status'] === 'en_revision' ? 'info' : 
                                    ($request['status'] === 'escalado' ? 'danger' : 
                                    ($request['status'] === 'resuelto' ? 'success' : 'dark'))); 
                            ?>">
                                <?php echo REQUEST_STATUS[$request['status']]; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($request['assigned_to_name']): ?>
                                <small><?php echo htmlspecialchars($request['assigned_to_name']); ?></small>
                            <?php else: ?>
                                <em class="text-muted">Sin asignar</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small>
                                <?php echo format_date($request['created_at'], 'd/m/Y'); ?><br>
                                <span class="text-muted"><?php echo time_ago($request['created_at']); ?></span>
                            </small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="index.php?page=requests&action=view&id=<?php echo $request['id']; ?>" 
                                   class="btn btn-outline-primary" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if (in_array($_SESSION['user_role'], ['admin', 'representante', 'asesor_legal'])): ?>
                                <a href="index.php?page=requests&action=edit&id=<?php echo $request['id']; ?>" 
                                   class="btn btn-outline-secondary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i><br>
                            No se encontraron solicitudes con los filtros aplicados.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Cards View (hidden by default) -->
<div id="cards-view" style="display: none;">
    <div class="row">
        <?php if (!empty($requests)): ?>
            <?php foreach ($requests as $request): ?>
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 <?php echo $request['priority'] === 'critica' ? 'border-danger' : ''; ?>">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong><?php echo htmlspecialchars($request['folio']); ?></strong>
                        <div>
                            <?php if ($request['is_confidential']): ?>
                            <i class="fas fa-lock text-warning" title="Confidencial"></i>
                            <?php endif; ?>
                            <?php if ($request['is_anonymous']): ?>
                            <i class="fas fa-user-secret text-info" title="Anónimo"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title"><?php echo htmlspecialchars($request['subject']); ?></h6>
                        <p class="card-text">
                            <small class="text-muted">
                                <?php echo htmlspecialchars(substr($request['description'], 0, 100)); ?>...
                            </small>
                        </p>
                        <div class="mb-2">
                            <span class="badge bg-secondary me-1"><?php echo DEPARTMENTS[$request['department']]; ?></span>
                            <span class="badge bg-<?php echo $request['request_type'] === 'queja' ? 'danger' : 'info'; ?> me-1">
                                <?php echo REQUEST_TYPES[$request['request_type']]; ?>
                            </span>
                            <span class="badge bg-<?php echo $request['priority'] === 'critica' ? 'danger' : 'warning'; ?>">
                                <?php echo ucfirst($request['priority']); ?>
                            </span>
                        </div>
                        <div class="mb-2">
                            Estado: <span class="badge bg-<?php echo $request['status'] === 'resuelto' ? 'success' : 'warning'; ?>">
                                <?php echo REQUEST_STATUS[$request['status']]; ?>
                            </span>
                        </div>
                        <p class="card-text">
                            <small class="text-muted">
                                <?php echo format_date($request['created_at']); ?>
                            </small>
                        </p>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group w-100" role="group">
                            <a href="index.php?page=requests&action=view&id=<?php echo $request['id']; ?>" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <?php if (in_array($_SESSION['user_role'], ['admin', 'representante', 'asesor_legal'])): ?>
                            <a href="index.php?page=requests&action=edit&id=<?php echo $request['id']; ?>" 
                               class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-3x mb-3"></i><br>
                    No se encontraron solicitudes con los filtros aplicados.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pagination -->
<?php if ($pagination['total_pages'] > 1): ?>
<nav aria-label="Paginación de solicitudes">
    <ul class="pagination justify-content-center">
        <?php if ($pagination['has_previous']): ?>
        <li class="page-item">
            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['p' => $pagination['previous_page']])); ?>">
                <i class="fas fa-chevron-left"></i> Anterior
            </a>
        </li>
        <?php endif; ?>

        <?php
        $start_page = max(1, $pagination['current_page'] - 2);
        $end_page = min($pagination['total_pages'], $pagination['current_page'] + 2);
        ?>

        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
        <li class="page-item <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['p' => $i])); ?>">
                <?php echo $i; ?>
            </a>
        </li>
        <?php endfor; ?>

        <?php if ($pagination['has_next']): ?>
        <li class="page-item">
            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['p' => $pagination['next_page']])); ?>">
                Siguiente <i class="fas fa-chevron-right"></i>
            </a>
        </li>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?>

<script>
// Toggle between table and cards view
document.getElementById('view-table').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('table-view').style.display = 'block';
        document.getElementById('cards-view').style.display = 'none';
    }
});

document.getElementById('view-cards').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('table-view').style.display = 'none';
        document.getElementById('cards-view').style.display = 'block';
    }
});
</script>