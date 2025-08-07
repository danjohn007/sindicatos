<?php
/**
 * Worker Model
 * Sistema CRM Sindicatos
 */

class Worker {
    private $conn;
    private $table_name = "workers";
    
    public $id;
    public $worker_number;
    public $full_name;
    public $email;
    public $phone;
    public $whatsapp;
    public $department;
    public $position;
    public $is_active;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET worker_number = :worker_number,
                      full_name = :full_name,
                      email = :email,
                      phone = :phone,
                      whatsapp = :whatsapp,
                      department = :department,
                      position = :position,
                      is_active = :is_active";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':worker_number', $this->worker_number);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':whatsapp', $this->whatsapp);
        $stmt->bindParam(':department', $this->department);
        $stmt->bindParam(':position', $this->position);
        $stmt->bindParam(':is_active', $this->is_active);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE is_active = 1 ORDER BY full_name";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->worker_number = $row['worker_number'];
            $this->full_name = $row['full_name'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->whatsapp = $row['whatsapp'];
            $this->department = $row['department'];
            $this->position = $row['position'];
            $this->is_active = $row['is_active'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    public function findByWhatsapp($whatsapp) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE whatsapp = :whatsapp AND is_active = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':whatsapp', $whatsapp);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
        return false;
    }

    public function searchWorkers($term) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE (full_name LIKE :term OR worker_number LIKE :term OR whatsapp LIKE :term) 
                  AND is_active = 1 
                  ORDER BY full_name 
                  LIMIT 10";

        $stmt = $this->conn->prepare($query);
        $term = '%' . $term . '%';
        $stmt->bindParam(':term', $term);
        $stmt->execute();

        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET worker_number = :worker_number,
                      full_name = :full_name,
                      email = :email,
                      phone = :phone,
                      whatsapp = :whatsapp,
                      department = :department,
                      position = :position,
                      is_active = :is_active
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':worker_number', $this->worker_number);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':whatsapp', $this->whatsapp);
        $stmt->bindParam(':department', $this->department);
        $stmt->bindParam(':position', $this->position);
        $stmt->bindParam(':is_active', $this->is_active);

        return $stmt->execute();
    }

    public function delete() {
        $query = "UPDATE " . $this->table_name . " SET is_active = 0 WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function getByDepartment($department) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE department = :department AND is_active = 1 
                  ORDER BY full_name";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department', $department);
        $stmt->execute();

        return $stmt;
    }

    public function workerNumberExists($worker_number, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE worker_number = :worker_number";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':worker_number', $worker_number);
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
?>