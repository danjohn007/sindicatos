<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud Enviada - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .success-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            text-align: center;
        }
        .success-icon {
            color: #28a745;
            font-size: 4rem;
            margin-bottom: 2rem;
        }
        .folio-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 2rem 0;
        }
        .btn-action {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }
        .info-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="success-card">
                    <!-- Success Icon and Message -->
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    
                    <h1 class="h2 text-success mb-3">¡Solicitud Enviada Exitosamente!</h1>
                    
                    <p class="lead text-muted mb-4">
                        Su solicitud ha sido recibida y procesada correctamente. 
                        Un representante sindical la revisará y se pondrá en contacto con usted.
                    </p>

                    <!-- Folio Information -->
                    <div class="folio-box">
                        <h3 class="mb-2">
                            <i class="fas fa-ticket-alt"></i> Número de Folio
                        </h3>
                        <h2 class="mb-2"><?php echo htmlspecialchars($success_folio); ?></h2>
                        <p class="mb-0">
                            <small>Guarde este número para dar seguimiento a su solicitud</small>
                        </p>
                    </div>

                    <!-- Important Information -->
                    <div class="alert alert-info" role="alert">
                        <h5 class="alert-heading">
                            <i class="fas fa-info-circle"></i> ¿Qué sigue ahora?
                        </h5>
                        <hr>
                        <ol class="text-start mb-0">
                            <li>Su solicitud será clasificada y asignada al departamento correspondiente</li>
                            <li>Un representante sindical la revisará dentro de las próximas 24-48 horas</li>
                            <li>Se le contactará para brindar seguimiento y solicitar información adicional si es necesario</li>
                            <li>Recibirá una respuesta o resolución según el tipo y complejidad de su solicitud</li>
                        </ol>
                    </div>

                    <!-- Information Grid -->
                    <div class="info-grid">
                        <div class="info-item">
                            <h6 class="text-primary">
                                <i class="fas fa-clock"></i> Tiempo de Respuesta
                            </h6>
                            <p class="mb-0 small">
                                <strong>Quejas críticas:</strong> 24 horas<br>
                                <strong>Solicitudes normales:</strong> 3-5 días<br>
                                <strong>Sugerencias:</strong> 5-10 días
                            </p>
                        </div>
                        
                        <div class="info-item">
                            <h6 class="text-primary">
                                <i class="fas fa-phone"></i> Contacto Directo
                            </h6>
                            <p class="mb-0 small">
                                Si su caso es urgente, puede contactar directamente a las oficinas del sindicato durante horario laboral.
                            </p>
                        </div>
                        
                        <div class="info-item">
                            <h6 class="text-primary">
                                <i class="fas fa-shield-alt"></i> Confidencialidad
                            </h6>
                            <p class="mb-0 small">
                                Su información será tratada con total confidencialidad y solo será compartida con personal autorizado.
                            </p>
                        </div>
                        
                        <div class="info-item">
                            <h6 class="text-primary">
                                <i class="fas fa-search"></i> Seguimiento
                            </h6>
                            <p class="mb-0 small">
                                Puede consultar el estado de su solicitud proporcionando su número de folio a cualquier representante.
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4">
                        <div class="d-grid gap-2 d-md-block">
                            <button class="btn btn-success btn-action text-white me-2" onclick="printFolio()">
                                <i class="fas fa-print"></i> Imprimir Folio
                            </button>
                            <a href="index.php?page=public_form" class="btn btn-outline-primary">
                                <i class="fas fa-plus"></i> Nueva Solicitud
                            </a>
                        </div>
                        
                        <div class="mt-3">
                            <a href="index.php?page=faq" class="btn btn-outline-secondary btn-sm me-2">
                                <i class="fas fa-question-circle"></i> Preguntas Frecuentes
                            </a>
                            <a href="index.php?page=login" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-sign-in-alt"></i> Acceso al Sistema
                            </a>
                        </div>
                    </div>

                    <!-- Social Share (Optional) -->
                    <div class="mt-4 pt-3 border-top">
                        <p class="text-muted small mb-2">Ayúdanos a mejorar nuestros servicios:</p>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary" onclick="shareOnWhatsApp()">
                                <i class="fab fa-whatsapp"></i> Compartir
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="copyToClipboard()">
                                <i class="fas fa-copy"></i> Copiar Folio
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Additional Resources -->
                <div class="text-center mt-4">
                    <p class="text-white">
                        <small>
                            <i class="fas fa-info-circle"></i> 
                            Para más información sobre sus derechos laborales, visite nuestra sección de 
                            <a href="index.php?page=faq" class="text-white">Preguntas Frecuentes</a>
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Clear saved form data
        const formFields = ['worker_name', 'worker_whatsapp', 'email', 'department', 'request_type', 'subject', 'description'];
        formFields.forEach(field => {
            localStorage.removeItem('public_form_' + field);
        });

        // Print folio function
        function printFolio() {
            const folio = '<?php echo htmlspecialchars($success_folio); ?>';
            const printContent = `
                <div style="text-align: center; padding: 2rem; font-family: Arial, sans-serif;">
                    <h1>Comprobante de Solicitud</h1>
                    <h2>Sistema CRM Sindicatos</h2>
                    <hr>
                    <h3>Número de Folio:</h3>
                    <h1 style="color: #667eea; border: 2px solid #667eea; padding: 1rem; display: inline-block;">${folio}</h1>
                    <hr>
                    <p>Fecha: ${new Date().toLocaleDateString('es-MX')}</p>
                    <p>Hora: ${new Date().toLocaleTimeString('es-MX')}</p>
                    <hr>
                    <p style="font-size: 0.9em; color: #666;">
                        Guarde este comprobante para dar seguimiento a su solicitud.<br>
                        Para más información contacte a las oficinas del sindicato.
                    </p>
                </div>
            `;
            
            const printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.print();
        }

        // Share on WhatsApp
        function shareOnWhatsApp() {
            const folio = '<?php echo htmlspecialchars($success_folio); ?>';
            const message = `¡He enviado una solicitud al sindicato! Mi número de folio es: ${folio}`;
            const url = `https://wa.me/?text=${encodeURIComponent(message)}`;
            window.open(url, '_blank');
        }

        // Copy folio to clipboard
        function copyToClipboard() {
            const folio = '<?php echo htmlspecialchars($success_folio); ?>';
            navigator.clipboard.writeText(folio).then(function() {
                alert('Número de folio copiado al portapapeles: ' + folio);
            }, function(err) {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = folio;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('Número de folio copiado: ' + folio);
            });
        }

        // Auto-redirect after 5 minutes (optional)
        setTimeout(function() {
            if (confirm('¿Desea ser redirigido a la página principal?')) {
                window.location.href = 'index.php?page=login';
            }
        }, 300000); // 5 minutes
    </script>
</body>
</html>