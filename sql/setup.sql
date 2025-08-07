-- Database Schema for Sistema CRM Sindicatos
-- MySQL 5.7 Compatible

DROP DATABASE IF EXISTS sindicatos_crm;
CREATE DATABASE sindicatos_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sindicatos_crm;

-- Table: users
CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    whatsapp VARCHAR(20),
    role ENUM('admin', 'representante', 'asesor_legal', 'psicologo', 'observador') NOT NULL DEFAULT 'representante',
    department ENUM('rh', 'operaciones', 'mantenimiento', 'seguridad', 'legal', 'administrativo') NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_role (role),
    INDEX idx_department (department),
    INDEX idx_active (is_active)
);

-- Table: workers (trabajadores que pueden enviar solicitudes)
CREATE TABLE workers (
    id INT(11) NOT NULL AUTO_INCREMENT,
    worker_number VARCHAR(20) UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    whatsapp VARCHAR(20),
    department ENUM('rh', 'operaciones', 'mantenimiento', 'seguridad', 'legal', 'administrativo') NOT NULL,
    position VARCHAR(100),
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_worker_number (worker_number),
    INDEX idx_department (department),
    INDEX idx_whatsapp (whatsapp)
);

-- Table: requests (solicitudes)
CREATE TABLE requests (
    id INT(11) NOT NULL AUTO_INCREMENT,
    folio VARCHAR(20) NOT NULL UNIQUE,
    worker_id INT(11) NULL, -- NULL para solicitudes anónimas
    worker_name VARCHAR(100), -- Para casos anónimos o cuando no está en BD
    worker_whatsapp VARCHAR(20),
    department ENUM('rh', 'operaciones', 'mantenimiento', 'seguridad', 'legal', 'administrativo') NOT NULL,
    request_type ENUM('queja', 'sugerencia', 'peticion', 'reconocimiento', 'otro') NOT NULL,
    subject VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('baja', 'media', 'alta', 'critica') NOT NULL DEFAULT 'media',
    status ENUM('recibido', 'en_revision', 'escalado', 'resuelto', 'cerrado') NOT NULL DEFAULT 'recibido',
    assigned_to INT(11) NULL,
    is_anonymous TINYINT(1) NOT NULL DEFAULT 0,
    is_confidential TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_folio (folio),
    INDEX idx_department (department),
    INDEX idx_request_type (request_type),
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_created_at (created_at)
);

-- Table: request_attachments (archivos adjuntos)
CREATE TABLE request_attachments (
    id INT(11) NOT NULL AUTO_INCREMENT,
    request_id INT(11) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT(11) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE,
    INDEX idx_request_id (request_id)
);

-- Table: request_updates (seguimiento de casos)
CREATE TABLE request_updates (
    id INT(11) NOT NULL AUTO_INCREMENT,
    request_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    update_type ENUM('comment', 'status_change', 'assignment', 'resolution') NOT NULL,
    old_status ENUM('recibido', 'en_revision', 'escalado', 'resuelto', 'cerrado') NULL,
    new_status ENUM('recibido', 'en_revision', 'escalado', 'resuelto', 'cerrado') NULL,
    comments TEXT,
    is_internal TINYINT(1) NOT NULL DEFAULT 0, -- True para comentarios internos
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_request_id (request_id),
    INDEX idx_created_at (created_at)
);

-- Table: satisfaction_surveys (encuestas de satisfacción)
CREATE TABLE satisfaction_surveys (
    id INT(11) NOT NULL AUTO_INCREMENT,
    request_id INT(11) NOT NULL,
    rating TINYINT(1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
    feedback TEXT,
    would_recommend TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE,
    INDEX idx_request_id (request_id),
    INDEX idx_rating (rating)
);

-- Table: faq_categories (categorías de preguntas frecuentes)
CREATE TABLE faq_categories (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    sort_order INT(11) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_sort_order (sort_order)
);

-- Table: faq_items (preguntas frecuentes)
CREATE TABLE faq_items (
    id INT(11) NOT NULL AUTO_INCREMENT,
    category_id INT(11) NOT NULL,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    sort_order INT(11) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    views_count INT(11) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (category_id) REFERENCES faq_categories(id) ON DELETE CASCADE,
    INDEX idx_category_id (category_id),
    INDEX idx_sort_order (sort_order)
);

-- Insert sample data

-- Default admin user
INSERT INTO users (username, email, password, full_name, role, is_active) VALUES
('admin', 'admin@sindicato.com', SHA2('admin123', 256), 'Administrador del Sistema', 'admin', 1),
('rep_rh', 'rh@sindicato.com', SHA2('rh123', 256), 'Representante de RH', 'representante', 1),
('rep_ops', 'operaciones@sindicato.com', SHA2('ops123', 256), 'Representante de Operaciones', 'representante', 1),
('asesor_legal', 'legal@sindicato.com', SHA2('legal123', 256), 'Asesor Legal Principal', 'asesor_legal', 1),
('psicologo1', 'psicologo@sindicato.com', SHA2('psi123', 256), 'Psicólogo Sindical', 'psicologo', 1);

UPDATE users SET department = 'rh' WHERE username = 'rep_rh';
UPDATE users SET department = 'operaciones' WHERE username = 'rep_ops';
UPDATE users SET department = 'legal' WHERE username = 'asesor_legal';

-- Sample workers
INSERT INTO workers (worker_number, full_name, email, phone, whatsapp, department, position) VALUES
('EMP001', 'Juan Pérez López', 'juan.perez@empresa.com', '5551234567', '5551234567', 'operaciones', 'Operador de Máquina'),
('EMP002', 'María González Ruiz', 'maria.gonzalez@empresa.com', '5552345678', '5552345678', 'rh', 'Asistente de RH'),
('EMP003', 'Carlos Ramírez Santos', 'carlos.ramirez@empresa.com', '5553456789', '5553456789', 'mantenimiento', 'Técnico Mecánico'),
('EMP004', 'Ana Torres Morales', 'ana.torres@empresa.com', '5554567890', '5554567890', 'seguridad', 'Guardia de Seguridad'),
('EMP005', 'Roberto Silva Cruz', 'roberto.silva@empresa.com', '5555678901', '5555678901', 'operaciones', 'Supervisor de Turno');

-- Sample requests
INSERT INTO requests (folio, worker_id, department, request_type, subject, description, priority, status) VALUES
('REQ001', 1, 'operaciones', 'queja', 'Condiciones inseguras en línea 3', 'Se han observado condiciones inseguras en la línea de producción 3, falta de equipo de protección personal y mantenimiento preventivo.', 'alta', 'en_revision'),
('REQ002', 2, 'rh', 'sugerencia', 'Mejora en horarios de capacitación', 'Propongo que las capacitaciones se realicen en horario escalonado para no afectar la producción.', 'media', 'recibido'),
('REQ003', 3, 'mantenimiento', 'peticion', 'Herramientas especializadas', 'Necesitamos herramientas especializadas para el mantenimiento preventivo de las nuevas máquinas.', 'media', 'recibido'),
('REQ004', NULL, 'rh', 'queja', 'Acoso laboral', 'Solicito de manera anónima investigación por posible acoso laboral en el departamento.', 'critica', 'escalado'),
('REQ005', 5, 'operaciones', 'reconocimiento', 'Excelente trabajo del equipo nocturno', 'Quiero reconocer el excelente trabajo que ha realizado el equipo del turno nocturno este mes.', 'baja', 'resuelto');

UPDATE requests SET is_anonymous = 1, worker_name = 'Anónimo' WHERE folio = 'REQ004';
UPDATE requests SET assigned_to = 2 WHERE folio = 'REQ001';
UPDATE requests SET assigned_to = 3 WHERE folio = 'REQ002';
UPDATE requests SET assigned_to = 4 WHERE folio = 'REQ004';

-- Sample FAQ categories
INSERT INTO faq_categories (name, description, sort_order) VALUES
('Derechos Laborales', 'Preguntas sobre derechos y obligaciones laborales', 1),
('Beneficios Sindicales', 'Información sobre beneficios para trabajadores sindicalizados', 2),
('Procesos Internos', 'Guías sobre procesos internos del sindicato', 3),
('Seguridad y Salud', 'Información sobre seguridad y salud ocupacional', 4);

-- Sample FAQ items
INSERT INTO faq_items (category_id, question, answer, sort_order) VALUES
(1, '¿Cuáles son mis derechos como trabajador sindicalizado?', 'Como trabajador sindicalizado tienes derecho a: representación legal, negociación colectiva, participación en decisiones que afecten tus condiciones laborales, y protección contra represalias.', 1),
(1, '¿Qué hacer en caso de despido injustificado?', 'En caso de despido injustificado, debes contactar inmediatamente al sindicato para recibir asesoría legal y representación en el proceso correspondiente.', 2),
(2, '¿Qué beneficios tengo como miembro del sindicato?', 'Los beneficios incluyen: asesoría legal gratuita, apoyo en negociaciones salariales, seguro de vida adicional, y acceso a programas de capacitación.', 1),
(3, '¿Cómo puedo presentar una queja formal?', 'Puedes presentar una queja a través del sistema CRM en línea, por WhatsApp, o acudiendo directamente a las oficinas del sindicato.', 1),
(4, '¿Qué hacer en caso de accidente laboral?', 'En caso de accidente: 1) Reporta inmediatamente a tu supervisor, 2) Solicita atención médica, 3) Notifica al sindicato dentro de 24 horas, 4) Documenta el incidente.', 1);

-- Create trigger to generate folio automatically
DELIMITER $$
CREATE TRIGGER generate_folio BEFORE INSERT ON requests
FOR EACH ROW
BEGIN
    DECLARE next_number INT;
    DECLARE folio_prefix VARCHAR(10);
    
    SET folio_prefix = CONCAT('REQ', YEAR(NOW()));
    
    SELECT COALESCE(MAX(CAST(SUBSTRING(folio, 8) AS UNSIGNED)), 0) + 1
    INTO next_number
    FROM requests 
    WHERE folio LIKE CONCAT(folio_prefix, '%');
    
    SET NEW.folio = CONCAT(folio_prefix, LPAD(next_number, 3, '0'));
END$$
DELIMITER ;

-- Create view for dashboard statistics
CREATE VIEW dashboard_stats AS
SELECT 
    COUNT(*) as total_requests,
    SUM(CASE WHEN status = 'recibido' THEN 1 ELSE 0 END) as new_requests,
    SUM(CASE WHEN status = 'en_revision' THEN 1 ELSE 0 END) as in_review,
    SUM(CASE WHEN status = 'escalado' THEN 1 ELSE 0 END) as escalated,
    SUM(CASE WHEN status = 'resuelto' THEN 1 ELSE 0 END) as resolved,
    SUM(CASE WHEN status = 'cerrado' THEN 1 ELSE 0 END) as closed,
    SUM(CASE WHEN priority = 'critica' AND status NOT IN ('resuelto', 'cerrado') THEN 1 ELSE 0 END) as critical_open,
    AVG(CASE WHEN resolved_at IS NOT NULL THEN TIMESTAMPDIFF(HOUR, created_at, resolved_at) END) as avg_resolution_hours
FROM requests;