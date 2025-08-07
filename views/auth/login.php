<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        .logo {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
        }
        .form-control {
            border-radius: 25px;
            padding: 12px 20px;
        }
        .public-access {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 2rem;
        }
        .public-access a {
            color: white;
            text-decoration: none;
        }
        .public-access a:hover {
            color: #f8f9fa;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="text-center mb-4">
                    <div class="logo">
                        <i class="fas fa-users"></i>
                    </div>
                    <h2 class="text-white mb-0"><?php echo APP_NAME; ?></h2>
                    <p class="text-white-50">Sistema de Atención de Solicitudes</p>
                </div>

                <div class="login-card">
                    <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php
                        switch ($error) {
                            case 'invalid_credentials':
                                echo '<i class="fas fa-exclamation-triangle"></i> Usuario o contraseña incorrectos.';
                                break;
                            case 'missing_fields':
                                echo '<i class="fas fa-exclamation-triangle"></i> Por favor complete todos los campos.';
                                break;
                            case 'session_expired':
                                echo '<i class="fas fa-clock"></i> Su sesión ha expirado. Por favor inicie sesión nuevamente.';
                                break;
                            case 'access_denied':
                                echo '<i class="fas fa-ban"></i> Acceso denegado. Su cuenta puede estar desactivada.';
                                break;
                            default:
                                echo '<i class="fas fa-exclamation-triangle"></i> Ha ocurrido un error.';
                        }
                        ?>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['message']) && $_GET['message'] === 'logged_out'): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle"></i> Sesión cerrada exitosamente.
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?page=login&action=authenticate">
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user"></i> Usuario o Email
                            </label>
                            <input type="text" class="form-control" id="username" name="username" required 
                                   placeholder="Ingrese su usuario o email" autofocus>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Contraseña
                            </label>
                            <input type="password" class="form-control" id="password" name="password" required 
                                   placeholder="Ingrese su contraseña">
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Recordar sesión
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-login text-white">
                                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Para solicitar acceso contacte al administrador del sistema
                        </small>
                    </div>
                </div>

                <!-- Public Access Section -->
                <div class="public-access text-center text-white">
                    <h6><i class="fas fa-external-link-alt"></i> Acceso Público</h6>
                    <div class="row">
                        <div class="col-6">
                            <a href="index.php?page=public_form" class="d-block p-2">
                                <i class="fas fa-edit fa-2x d-block mb-2"></i>
                                <small>Enviar Solicitud</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="index.php?page=faq" class="d-block p-2">
                                <i class="fas fa-question-circle fa-2x d-block mb-2"></i>
                                <small>Preguntas Frecuentes</small>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Demo Credentials -->
                <?php if ($_SERVER['SERVER_NAME'] === 'localhost'): ?>
                <div class="mt-3 p-3 bg-light rounded">
                    <h6 class="text-center text-muted mb-3">Credenciales de Prueba</h6>
                    <div class="row text-center">
                        <div class="col-6">
                            <small class="d-block"><strong>Administrador</strong></small>
                            <small class="text-muted">admin / admin123</small>
                        </div>
                        <div class="col-6">
                            <small class="d-block"><strong>Representante RH</strong></small>
                            <small class="text-muted">rep_rh / rh123</small>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>