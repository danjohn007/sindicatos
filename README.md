# Sistema CRM Sindicatos

Sistema de gestión de relaciones con trabajadores (CRM) desarrollado en PHP puro sin framework, especializado en la atención de solicitudes, quejas y sugerencias de trabajadores sindicalizados.

## 🎯 Objetivo

Centralizar, organizar y dar seguimiento a las solicitudes, quejas y sugerencias de los trabajadores sindicalizados, permitiendo al sindicato analizar patrones, responder con eficiencia y generar reportes por departamento, tipo de solicitud y estatus.

## ✨ Características Principales

### 📋 Gestión de Solicitudes
- Formulario digital para captura de solicitudes (quejas, sugerencias, peticiones, reconocimientos)
- Búsqueda automática por WhatsApp para cargar información del trabajador
- Soporte para solicitudes anónimas
- Clasificación por departamento y tipo de solicitud
- Adjuntar archivos (PDF, DOC, DOCX, JPG, PNG, GIF)
- Sistema de folios únicos automáticos

### 🔄 Clasificación y Ruteo Automático
- Clasificación automática por departamento y tipo
- Asignación automática a responsables sindicales
- Sistema de prioridades (Baja, Media, Alta, Crítica)
- Estados de seguimiento (Recibido, En revisión, Escalado, Resuelto, Cerrado)

### 👥 Gestión de Usuarios
- **Administrador General**: Acceso completo al sistema
- **Representantes de Departamento**: Gestión de su área específica
- **Asesores Legales**: Acceso a casos legales
- **Psicólogos Sindicales**: Atención especializada
- **Observadores Externos**: Acceso limitado de solo lectura

### 📊 Panel de Control (Dashboard)
- Métricas en tiempo real de solicitudes
- Gráficos por departamento y tipo de solicitud
- Alertas de casos críticos
- Tiempo promedio de resolución
- Casos sin asignar o sin respuesta

### 📈 Sistema de Reportes
- Reportes por departamento, tipo y estado
- Exportación a PDF y Excel
- Estadísticas de tiempo de atención
- Análisis de patrones y tendencias

### 🌐 Acceso Público
- Formulario público para envío de solicitudes
- Sin necesidad de registro previo
- Protección contra spam con rate limiting
- Confirmación automática con número de folio

### 📚 Base de Conocimiento
- Preguntas frecuentes organizadas por categorías
- Guías legales y procedimientos internos
- Sistema de autoayuda para trabajadores

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP 7.4+ (puro, sin framework)
- **Base de Datos**: MySQL 5.7+
- **Frontend**: Bootstrap 5.1.3
- **Iconos**: Font Awesome 6.0
- **Gráficos**: Chart.js
- **Arquitectura**: MVC (Modelo-Vista-Controlador)

## 📋 Requisitos del Sistema

- **Servidor Web**: Apache 2.4+ con mod_rewrite
- **PHP**: Versión 7.4 o superior
- **MySQL**: Versión 5.7 o superior
- **Extensiones PHP requeridas**:
  - PDO
  - PDO_MySQL
  - GD (para manejo de imágenes)
  - fileinfo (para validación de archivos)
  - mbstring

## 🚀 Instalación

### 1. Clonar el Repositorio
```bash
git clone https://github.com/danjohn007/sindicatos.git
cd sindicatos
```

### 2. Configurar el Servidor Web

#### Apache Virtual Host
```apache
<VirtualHost *:80>
    ServerName sindicatos.local
    DocumentRoot /path/to/sindicatos
    
    <Directory /path/to/sindicatos>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/sindicatos_error.log
    CustomLog ${APACHE_LOG_DIR}/sindicatos_access.log combined
</VirtualHost>
```

#### Agregar al archivo hosts (opcional)
```
127.0.0.1 sindicatos.local
```

### 3. Configurar la Base de Datos

#### Crear la base de datos
```sql
mysql -u root -p < sql/setup.sql
```

#### O manualmente:
1. Crear base de datos: `sindicatos_crm`
2. Importar el archivo `sql/setup.sql`
3. Verificar que se crearon todas las tablas y datos de ejemplo

### 4. Configurar la Aplicación

Editar el archivo `config/config.php` con sus datos:

```php
// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'sindicatos_crm');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');

// URL base del sitio
define('BASE_URL', 'http://sindicatos.local/');
```

### 5. Configurar Permisos

```bash
# Dar permisos de escritura a la carpeta de uploads
chmod 755 assets/uploads/
chown -R www-data:www-data assets/uploads/

# Permisos para logs (si se usan)
chmod 755 logs/
chown -R www-data:www-data logs/
```

### 6. Acceder al Sistema

- **URL Principal**: `http://sindicatos.local/`
- **Formulario Público**: `http://sindicatos.local/index.php?page=public_form`

## 🔐 Credenciales de Acceso por Defecto

| Usuario | Contraseña | Rol | Descripción |
|---------|------------|-----|-------------|
| admin | admin123 | Administrador | Acceso completo al sistema |
| rep_rh | rh123 | Representante | Departamento de RH |
| rep_ops | ops123 | Representante | Departamento de Operaciones |
| asesor_legal | legal123 | Asesor Legal | Casos legales |
| psicologo1 | psi123 | Psicólogo | Atención psicológica |

**⚠️ IMPORTANTE**: Cambiar estas contraseñas antes de usar en producción.

## 📁 Estructura del Proyecto

```
sindicatos/
├── config/
│   ├── database.php          # Configuración de BD
│   └── config.php           # Configuración general
├── controllers/
│   ├── AuthController.php    # Autenticación
│   ├── DashboardController.php # Panel principal
│   ├── RequestController.php  # Gestión de solicitudes
│   ├── PublicController.php   # Formulario público
│   └── ApiController.php      # API para AJAX
├── models/
│   ├── User.php             # Modelo de usuarios
│   ├── Request.php          # Modelo de solicitudes
│   └── Worker.php           # Modelo de trabajadores
├── views/
│   ├── layout/              # Plantillas base
│   ├── auth/               # Vistas de autenticación
│   ├── dashboard/          # Panel de control
│   ├── requests/           # Gestión de solicitudes
│   └── public/             # Formulario público
├── assets/
│   ├── css/                # Estilos personalizados
│   ├── js/                 # JavaScript personalizado
│   └── uploads/            # Archivos subidos
├── includes/
│   └── functions.php       # Funciones auxiliares
├── sql/
│   └── setup.sql          # Script de base de datos
├── index.php              # Punto de entrada
└── README.md             # Este archivo
```

## 🔧 Configuración Avanzada

### Configuración de Email (Opcional)
```php
// En config/config.php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu-email@gmail.com');
define('SMTP_PASS', 'tu-contraseña-app');
```

### Configuración de Uploads
```php
// Tamaño máximo de archivo (5MB por defecto)
define('MAX_FILE_SIZE', 5 * 1024 * 1024);

// Extensiones permitidas
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif']);
```

### Configuración de Seguridad
```php
// Tiempo de vida de sesión (1 hora por defecto)
define('SESSION_LIFETIME', 3600);

// Algoritmo de hash para contraseñas
define('HASH_ALGORITHM', 'sha256');
```

## 🔒 Seguridad

### Características de Seguridad Implementadas
- **Protección CSRF**: Tokens en todos los formularios
- **Validación de entrada**: Sanitización de todos los datos
- **Control de acceso**: Roles y permisos por funcionalidad
- **Rate limiting**: Protección contra spam en formulario público
- **Validación de archivos**: Tipo y tamaño de archivos subidos
- **Sesiones seguras**: Tiempo de vida limitado

### Recomendaciones Adicionales para Producción
1. **Cambiar contraseñas por defecto**
2. **Configurar HTTPS**
3. **Configurar firewall**
4. **Backups regulares de la base de datos**
5. **Logs de auditoría**
6. **Actualizar dependencias regularmente**

## 📊 Base de Datos

### Tablas Principales
- **users**: Usuarios del sistema
- **workers**: Trabajadores sindicalizados
- **requests**: Solicitudes/quejas/sugerencias
- **request_updates**: Seguimiento de casos
- **request_attachments**: Archivos adjuntos
- **faq_categories**: Categorías de FAQ
- **faq_items**: Preguntas frecuentes
- **satisfaction_surveys**: Encuestas de satisfacción

### Vistas y Triggers
- **dashboard_stats**: Vista con estadísticas del dashboard
- **generate_folio**: Trigger para generar folios automáticos

## 🚀 Uso del Sistema

### Para Trabajadores (Formulario Público)
1. Acceder al formulario público
2. Llenar datos personales (opcional anónimo)
3. Seleccionar departamento y tipo de solicitud
4. Describir el problema o sugerencia
5. Adjuntar archivos si es necesario
6. Enviar y recibir número de folio

### Para Representantes Sindicales
1. Iniciar sesión con credenciales
2. Ver dashboard con métricas
3. Revisar solicitudes asignadas
4. Actualizar estado de casos
5. Agregar comentarios y seguimiento
6. Generar reportes

### Para Administradores
1. Gestión completa de usuarios
2. Configuración del sistema
3. Reportes avanzados
4. Auditoría de actividades
5. Gestión de FAQ

## 🐛 Solución de Problemas

### Problemas Comunes

#### Error de conexión a base de datos
```
Solution: Verificar credenciales en config/database.php
```

#### Archivos no se suben
```
Solution: Verificar permisos en assets/uploads/
chmod 755 assets/uploads/
```

#### Sesión no funciona
```
Solution: Verificar configuración de PHP session
session.save_path en php.ini
```

#### Gráficos no aparecen
```
Solution: Verificar conexión a CDN de Chart.js
O descargar Chart.js localmente
```

## 🔄 Mantenimiento

### Tareas Regulares
- **Backup de base de datos**: Diario
- **Limpieza de archivos temporales**: Semanal
- **Revisión de logs**: Diario
- **Actualización de FAQ**: Mensual
- **Revisión de usuarios inactivos**: Mensual

### Scripts de Mantenimiento (Ejemplo)
```bash
#!/bin/bash
# Backup diario
mysqldump -u usuario -p sindicatos_crm > backup_$(date +%Y%m%d).sql

# Limpiar archivos temporales
find /tmp -name "rate_limit_*" -mtime +1 -delete
```

## 📞 Soporte y Contacto

- **Desarrollador**: [GitHub - danjohn007](https://github.com/danjohn007)
- **Versión**: 1.0.0
- **Licencia**: MIT
- **Documentación**: Este README.md

## 🚧 Roadmap Futuro

### Características Planeadas
- [ ] Integración con WhatsApp Business API
- [ ] Notificaciones por email automáticas
- [ ] App móvil complementaria
- [ ] Integración con sistemas de nómina
- [ ] Dashboard ejecutivo avanzado
- [ ] Sistema de encuestas automáticas
- [ ] Integración con calendario
- [ ] API REST completa

### Mejoras Técnicas
- [ ] Cache de consultas frecuentes
- [ ] Optimización de base de datos
- [ ] Tests unitarios
- [ ] Documentación de API
- [ ] Docker containers
- [ ] CI/CD pipeline

---

## 📄 Licencia

Este proyecto está licenciado bajo la Licencia MIT. Consulte el archivo `LICENSE` para más detalles.

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crear rama para nueva característica (`git checkout -b feature/nueva-caracteristica`)
3. Commit los cambios (`git commit -am 'Agregar nueva característica'`)
4. Push a la rama (`git push origin feature/nueva-caracteristica`)
5. Crear Pull Request

---

**Desarrollado con ❤️ para la comunidad sindical**
