<?php
/**
 * Request Model
 * Sistema CRM Sindicatos
 */

class Request {
    private $conn;
    private $table_name = "requests";
    
    public $id;
    public $folio;
    public $worker_id;
    public $worker_name;
    public $worker_whatsapp;
    public $department;
    public $request_type;
    public $subject;
    public $description;
    public $priority;
    public $status;
    public $assigned_to;
    public $is_anonymous;
    public $is_confidential;
    public $created_at;
    public $updated_at;
    public $resolved_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET worker_id = :worker_id,
                      worker_name = :worker_name,
                      worker_whatsapp = :worker_whatsapp,
                      department = :department,
                      request_type = :request_type,
                      subject = :subject,
                      description = :description,
                      priority = :priority,
                      is_anonymous = :is_anonymous,
                      is_confidential = :is_confidential";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':worker_id', $this->worker_id);
        $stmt->bindParam(':worker_name', $this->worker_name);
        $stmt->bindParam(':worker_whatsapp', $this->worker_whatsapp);
        $stmt->bindParam(':department', $this->department);
        $stmt->bindParam(':request_type', $this->request_type);
        $stmt->bindParam(':subject', $this->subject);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':priority', $this->priority);
        $stmt->bindParam(':is_anonymous', $this->is_anonymous);
        $stmt->bindParam(':is_confidential', $this->is_confidential);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            
            // Get the generated folio
            $stmt = $this->conn->prepare("SELECT folio FROM " . $this->table_name . " WHERE id = :id");
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->folio = $row['folio'];
            
            return true;
        }
        return false;
    }

    public function read($filters = [], $limit = null, $offset = 0) {
        $query = "SELECT r.*, w.full_name as worker_full_name, w.worker_number,
                         u.full_name as assigned_to_name
                  FROM " . $this->table_name . " r
                  LEFT JOIN workers w ON r.worker_id = w.id
                  LEFT JOIN users u ON r.assigned_to = u.id
                  WHERE 1=1";

        $params = [];

        if (!empty($filters['department'])) {
            $query .= " AND r.department = :department";
            $params[':department'] = $filters['department'];
        }

        if (!empty($filters['status'])) {
            $query .= " AND r.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['request_type'])) {
            $query .= " AND r.request_type = :request_type";
            $params[':request_type'] = $filters['request_type'];
        }

        if (!empty($filters['priority'])) {
            $query .= " AND r.priority = :priority";
            $params[':priority'] = $filters['priority'];
        }

        if (!empty($filters['assigned_to'])) {
            $query .= " AND r.assigned_to = :assigned_to";
            $params[':assigned_to'] = $filters['assigned_to'];
        }

        if (!empty($filters['search'])) {
            $query .= " AND (r.folio LIKE :search OR r.subject LIKE :search OR r.worker_name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['date_from'])) {
            $query .= " AND DATE(r.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $query .= " AND DATE(r.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        $query .= " ORDER BY r.created_at DESC";

        if ($limit) {
            $query .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        if ($limit) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT r.*, w.full_name as worker_full_name, w.worker_number, w.email as worker_email,
                         u.full_name as assigned_to_name
                  FROM " . $this->table_name . " r
                  LEFT JOIN workers w ON r.worker_id = w.id
                  LEFT JOIN users u ON r.assigned_to = u.id
                  WHERE r.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            foreach ($row as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
            return $row;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET department = :department,
                      request_type = :request_type,
                      subject = :subject,
                      description = :description,
                      priority = :priority,
                      status = :status,
                      assigned_to = :assigned_to,
                      is_confidential = :is_confidential";

        if ($this->status === 'resuelto' || $this->status === 'cerrado') {
            $query .= ", resolved_at = NOW()";
        }

        $query .= " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':department', $this->department);
        $stmt->bindParam(':request_type', $this->request_type);
        $stmt->bindParam(':subject', $this->subject);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':priority', $this->priority);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':assigned_to', $this->assigned_to);
        $stmt->bindParam(':is_confidential', $this->is_confidential);

        return $stmt->execute();
    }

    public function updateStatus($new_status, $user_id) {
        $old_status = $this->status;
        $this->status = $new_status;

        if ($this->update()) {
            // Log the status change
            $this->logUpdate($user_id, 'status_change', null, $old_status, $new_status);
            return true;
        }
        return false;
    }

    public function assign($user_id, $assigned_by) {
        $query = "UPDATE " . $this->table_name . "
                  SET assigned_to = :assigned_to
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':assigned_to', $user_id);

        if ($stmt->execute()) {
            $this->assigned_to = $user_id;
            $this->logUpdate($assigned_by, 'assignment', "Caso asignado al usuario ID: $user_id");
            return true;
        }
        return false;
    }

    public function logUpdate($user_id, $update_type, $comments = null, $old_status = null, $new_status = null, $is_internal = 0) {
        $query = "INSERT INTO request_updates
                  SET request_id = :request_id,
                      user_id = :user_id,
                      update_type = :update_type,
                      old_status = :old_status,
                      new_status = :new_status,
                      comments = :comments,
                      is_internal = :is_internal";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':request_id', $this->id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':update_type', $update_type);
        $stmt->bindParam(':old_status', $old_status);
        $stmt->bindParam(':new_status', $new_status);
        $stmt->bindParam(':comments', $comments);
        $stmt->bindParam(':is_internal', $is_internal);

        return $stmt->execute();
    }

    public function getUpdates() {
        $query = "SELECT ru.*, u.full_name as user_name
                  FROM request_updates ru
                  JOIN users u ON ru.user_id = u.id
                  WHERE ru.request_id = :request_id
                  ORDER BY ru.created_at ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':request_id', $this->id);
        $stmt->execute();

        return $stmt;
    }

    public function getAttachments() {
        $query = "SELECT * FROM request_attachments WHERE request_id = :request_id ORDER BY uploaded_at ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':request_id', $this->id);
        $stmt->execute();

        return $stmt;
    }

    public function addAttachment($file_data) {
        $query = "INSERT INTO request_attachments
                  SET request_id = :request_id,
                      file_name = :file_name,
                      file_path = :file_path,
                      file_size = :file_size,
                      file_type = :file_type";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':request_id', $this->id);
        $stmt->bindParam(':file_name', $file_data['file_name']);
        $stmt->bindParam(':file_path', $file_data['file_path']);
        $stmt->bindParam(':file_size', $file_data['file_size']);
        $stmt->bindParam(':file_type', $file_data['file_type']);

        return $stmt->execute();
    }

    public function getCount($filters = []) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE 1=1";
        $params = [];

        if (!empty($filters['department'])) {
            $query .= " AND department = :department";
            $params[':department'] = $filters['department'];
        }

        if (!empty($filters['status'])) {
            $query .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['request_type'])) {
            $query .= " AND request_type = :request_type";
            $params[':request_type'] = $filters['request_type'];
        }

        if (!empty($filters['search'])) {
            $query .= " AND (folio LIKE :search OR subject LIKE :search OR worker_name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getDashboardStats() {
        $query = "SELECT * FROM dashboard_stats";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStatsByDepartment() {
        $query = "SELECT department, 
                         COUNT(*) as total,
                         SUM(CASE WHEN status IN ('recibido', 'en_revision', 'escalado') THEN 1 ELSE 0 END) as active,
                         SUM(CASE WHEN status IN ('resuelto', 'cerrado') THEN 1 ELSE 0 END) as resolved
                  FROM " . $this->table_name . "
                  GROUP BY department
                  ORDER BY total DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function getStatsByType() {
        $query = "SELECT request_type, 
                         COUNT(*) as total,
                         SUM(CASE WHEN status IN ('recibido', 'en_revision', 'escalado') THEN 1 ELSE 0 END) as active,
                         SUM(CASE WHEN status IN ('resuelto', 'cerrado') THEN 1 ELSE 0 END) as resolved
                  FROM " . $this->table_name . "
                  GROUP BY request_type
                  ORDER BY total DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function getRecentRequests($limit = 10) {
        $query = "SELECT r.*, w.full_name as worker_full_name
                  FROM " . $this->table_name . " r
                  LEFT JOIN workers w ON r.worker_id = w.id
                  ORDER BY r.created_at DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }
}
?>