<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .form-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 2rem;
        }
        .form-section {
            padding: 2rem;
        }
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }
        .info-box {
            background: rgba(102, 126, 234, 0.1);
            border-left: 4px solid #667eea;
            border-radius: 5px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="form-card">
                    <!-- Header -->
                    <div class="header-section text-center">
                        <h1 class="mb-0">
                            <i class="fas fa-comment-dots fa-2x mb-3"></i><br>
                            Enviar Solicitud
                        </h1>
                        <p class="mb-0">Sistema de Atención de Trabajadores Sindicalizados</p>
                    </div>

                    <div class="form-section">
                        <!-- Error Message -->
                        <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
                        </div>
                        <?php endif; ?>

                        <!-- Information Box -->
                        <div class="info-box">
                            <h6><i class="fas fa-info-circle"></i> Información Importante</h6>
                            <ul class="mb-0 small">
                                <li>Sus datos personales serán tratados de forma confidencial</li>
                                <li>Recibirá un número de folio para dar seguimiento a su solicitud</li>
                                <li>Puede enviar solicitudes de forma anónima si lo prefiere</li>
                                <li>El tiempo de respuesta varía según el tipo y prioridad de la solicitud</li>
                            </ul>
                        </div>

                        <form id="public_request_form" method="POST" action="index.php?page=public_form&action=submit" enctype="multipart/form-data">
                            <!-- Worker Information -->
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-user"></i> Información del Trabajador
                            </h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="worker_name" class="form-label">
                                        Nombre Completo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="worker_name" name="worker_name" 
                                           placeholder="Su nombre completo" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous">
                                        <label class="form-check-label" for="is_anonymous">
                                            <i class="fas fa-user-secret"></i> Anónimo
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="worker_whatsapp" class="form-label">
                                        <i class="fab fa-whatsapp text-success"></i> WhatsApp
                                    </label>
                                    <input type="text" class="form-control" id="worker_whatsapp" name="worker_whatsapp" 
                                           placeholder="Número de WhatsApp (opcional)">
                                    <div class="form-text">Para verificar su identidad automáticamente</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope"></i> Email
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           placeholder="email@ejemplo.com (opcional)">
                                </div>
                            </div>

                            <!-- Request Information -->
                            <h5 class="text-primary mb-3 mt-4">
                                <i class="fas fa-clipboard"></i> Información de la Solicitud
                            </h5>

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
                                          placeholder="Describa detalladamente la situación. Incluya fechas, lugares y personas involucradas si es relevante."></textarea>
                                <div class="form-text">Sea lo más específico posible para una mejor atención</div>
                            </div>

                            <!-- File Attachments -->
                            <div class="mb-4">
                                <label for="attachments" class="form-label">
                                    <i class="fas fa-paperclip"></i> Archivos Adjuntos (Opcional)
                                </label>
                                <input type="file" class="form-control" id="attachments" name="attachments[]" 
                                       multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif"
                                       onchange="previewFiles(this)">
                                <div class="form-text">
                                    Formatos: PDF, DOC, DOCX, JPG, PNG, GIF. Máximo 5MB por archivo.
                                </div>
                                <div id="file_preview" class="mt-2"></div>
                            </div>

                            <!-- Privacy Notice -->
                            <div class="alert alert-info" role="alert">
                                <h6 class="alert-heading">
                                    <i class="fas fa-shield-alt"></i> Aviso de Privacidad
                                </h6>
                                <p class="mb-2">Al enviar esta solicitud, usted acepta que:</p>
                                <ul class="mb-0 small">
                                    <li>Sus datos serán utilizados únicamente para atender su solicitud</li>
                                    <li>La información será tratada de manera confidencial</li>
                                    <li>Solo personal autorizado tendrá acceso a sus datos</li>
                                    <li>Puede solicitar la eliminación de sus datos en cualquier momento</li>
                                </ul>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php?page=login" class="btn btn-outline-secondary me-md-2">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                                <button type="submit" class="btn btn-primary btn-submit text-white">
                                    <i class="fas fa-paper-plane"></i> Enviar Solicitud
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Additional Links -->
                <div class="text-center mt-4">
                    <div class="row">
                        <div class="col-6">
                            <a href="index.php?page=faq" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-question-circle"></i><br>
                                <small>Preguntas Frecuentes</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="index.php?page=login" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-sign-in-alt"></i><br>
                                <small>Acceso Sistema</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Anonymous checkbox handler
        document.getElementById('is_anonymous').addEventListener('change', function() {
            const workerName = document.getElementById('worker_name');
            const workerWhatsapp = document.getElementById('worker_whatsapp');
            const email = document.getElementById('email');
            
            if (this.checked) {
                workerName.value = 'Anónimo';
                workerName.readOnly = true;
                workerWhatsapp.value = '';
                workerWhatsapp.readOnly = true;
                email.value = '';
                email.readOnly = true;
            } else {
                workerName.value = '';
                workerName.readOnly = false;
                workerWhatsapp.readOnly = false;
                email.readOnly = false;
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

        // File preview function
        function previewFiles(input) {
            const preview = document.getElementById('file_preview');
            preview.innerHTML = '';
            
            if (input.files) {
                for (let file of input.files) {
                    const div = document.createElement('div');
                    div.className = 'border p-2 mb-2 rounded bg-light';
                    div.innerHTML = `
                        <i class="fas fa-file text-primary"></i> ${file.name} 
                        <small class="text-muted">(${(file.size / 1024).toFixed(1)} KB)</small>
                    `;
                    preview.appendChild(div);
                }
            }
        }

        // Form validation
        document.getElementById('public_request_form').addEventListener('submit', function(e) {
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
                return false;
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
            submitBtn.disabled = true;
        });

        // Auto-save form data
        const form = document.getElementById('public_request_form');
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                localStorage.setItem('public_form_' + this.name, this.value);
            });

            // Restore saved values
            const savedValue = localStorage.getItem('public_form_' + input.name);
            if (savedValue && !input.value) {
                input.value = savedValue;
            }
        });
    </script>
</body>
</html>