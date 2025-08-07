<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-plus"></i> Nueva Solicitud
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=requests" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit"></i> Formulario de Solicitud
                </h5>
            </div>
            <div class="card-body">
                <form id="request_form" method="POST" action="index.php?page=requests&action=store" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                    <!-- Worker Information -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="worker_search" class="form-label">
                                <i class="fas fa-search"></i> Buscar Trabajador
                            </label>
                            <input type="text" class="form-control" id="worker_search" 
                                   placeholder="Buscar por nombre, número o WhatsApp..."
                                   onkeyup="searchWorkers(this, 'search_results')">
                            <div id="search_results" class="list-group position-absolute" style="z-index: 1000; max-height: 200px; overflow-y: auto;"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="worker_whatsapp" class="form-label">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </label>
                            <input type="text" class="form-control" id="worker_whatsapp" name="worker_whatsapp" 
                                   placeholder="Número de WhatsApp">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="worker_name" class="form-label">
                                Nombre del Trabajador <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="worker_name" name="worker_name" required
                                   placeholder="Nombre completo del trabajador">
                            <input type="hidden" id="worker_id" name="worker_id">
                        </div>
                        <div class="col-md-4">
                            <label for="is_anonymous" class="form-label">&nbsp;</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous">
                                <label class="form-check-label" for="is_anonymous">
                                    <i class="fas fa-user-secret"></i> Solicitud Anónima
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Request Details -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="department" class="form-label">
                                Departamento <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="department" name="department" required>
                                <option value="">Seleccione un departamento</option>
                                <?php foreach (DEPARTMENTS as $key => $name): ?>
                                <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="request_type" class="form-label">
                                Tipo de Solicitud <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="request_type" name="request_type" required>
                                <option value="">Seleccione el tipo</option>
                                <?php foreach (REQUEST_TYPES as $key => $name): ?>
                                <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label">
                            Asunto <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="subject" name="subject" required
                               placeholder="Resumen breve del tema" maxlength="200">
                        <div class="form-text">Máximo 200 caracteres</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            Descripción Detallada <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="description" name="description" rows="5" required
                                  placeholder="Describa detalladamente la situación, incluyendo fechas, lugares y personas involucradas si es relevante"></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="priority" class="form-label">
                                Prioridad
                            </label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="baja">Baja</option>
                                <option value="media" selected>Media</option>
                                <option value="alta">Alta</option>
                                <option value="critica">Crítica</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">&nbsp;</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_confidential" name="is_confidential">
                                <label class="form-check-label" for="is_confidential">
                                    <i class="fas fa-lock"></i> Marcar como confidencial
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- File Attachments -->
                    <div class="mb-3">
                        <label for="attachments" class="form-label">
                            <i class="fas fa-paperclip"></i> Archivos Adjuntos
                        </label>
                        <input type="file" class="form-control" id="attachments" name="attachments[]" 
                               multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif"
                               onchange="previewFiles(this)">
                        <div class="form-text">
                            Formatos permitidos: PDF, DOC, DOCX, JPG, PNG, GIF. Tamaño máximo: 5MB por archivo.
                        </div>
                        <div id="file_preview" class="mt-2"></div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php?page=requests" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Crear Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Side Panel -->
    <div class="col-lg-4">
        <!-- Help Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle"></i> Ayuda
                </h6>
            </div>
            <div class="card-body">
                <h6>Tipos de Solicitudes:</h6>
                <ul class="list-unstyled">
                    <li><strong>Queja:</strong> Problemas o inconformidades</li>
                    <li><strong>Sugerencia:</strong> Ideas de mejora</li>
                    <li><strong>Petición:</strong> Solicitudes específicas</li>
                    <li><strong>Reconocimiento:</strong> Felicitaciones</li>
                </ul>

                <h6 class="mt-3">Prioridades:</h6>
                <ul class="list-unstyled">
                    <li><span class="badge bg-secondary">Baja</span> Asuntos no urgentes</li>
                    <li><span class="badge bg-info">Media</span> Asuntos normales</li>
                    <li><span class="badge bg-warning">Alta</span> Requiere atención pronta</li>
                    <li><span class="badge bg-danger">Crítica</span> Urgente, riesgo o seguridad</li>
                </ul>
            </div>
        </div>

        <!-- Quick Access -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-external-link-alt"></i> Acceso Rápido
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="index.php?page=faq" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-question-circle"></i> Preguntas Frecuentes
                    </a>
                    <a href="index.php?page=workers&action=create" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-user-plus"></i> Registrar Trabajador
                    </a>
                    <a href="index.php?page=public_form" target="_blank" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-external-link-alt"></i> Formulario Público
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-clear search results when anonymous is checked
document.getElementById('is_anonymous').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('worker_name').value = 'Anónimo';
        document.getElementById('worker_id').value = '';
        document.getElementById('worker_search').value = '';
        document.getElementById('search_results').innerHTML = '';
        document.getElementById('worker_name').readOnly = true;
    } else {
        document.getElementById('worker_name').value = '';
        document.getElementById('worker_name').readOnly = false;
    }
});

// Character count for subject
document.getElementById('subject').addEventListener('input', function() {
    const maxLength = 200;
    const currentLength = this.value.length;
    const remaining = maxLength - currentLength;
    
    let formText = this.nextElementSibling;
    formText.textContent = `${remaining} caracteres restantes`;
    
    if (remaining < 20) {
        formText.className = 'form-text text-warning';
    } else {
        formText.className = 'form-text';
    }
});

// Form validation
document.getElementById('request_form').addEventListener('submit', function(e) {
    const requiredFields = ['worker_name', 'department', 'request_type', 'subject', 'description'];
    let isValid = true;

    requiredFields.forEach(function(fieldName) {
        const field = document.getElementById(fieldName);
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    if (!isValid) {
        e.preventDefault();
        alert('Por favor complete todos los campos obligatorios.');
    }
});
</script>