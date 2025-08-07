<?php
/**
 * User Model
 * Sistema CRM Sindicatos
 */

class User {
    private $conn;
    private $table_name = "users";
    
    public $id;
    public $username;
    public $email;
    public $password;
    public $full_name;
    public $phone;
    public $whatsapp;
    public $role;
    public $department;
    public $is_active;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function authenticate($username, $password) {
        $query = "SELECT id, username, email, full_name, role, department, is_active 
                  FROM " . $this->table_name . " 
                  WHERE (username = :username OR email = :username) 
                  AND password = SHA2(:password, 256) 
                  AND is_active = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET username = :username,
                      email = :email,
                      password = SHA2(:password, 256),
                      full_name = :full_name,
                      phone = :phone,
                      whatsapp = :whatsapp,
                      role = :role,
                      department = :department,
                      is_active = :is_active";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':whatsapp', $this->whatsapp);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':department', $this->department);
        $stmt->bindParam(':is_active', $this->is_active);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT id, username, email, full_name, phone, whatsapp, role, department, is_active, created_at
                  FROM " . $this->table_name . "
                  ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function readOne() {
        $query = "SELECT id, username, email, full_name, phone, whatsapp, role, department, is_active, created_at
                  FROM " . $this->table_name . "
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->full_name = $row['full_name'];
            $this->phone = $row['phone'];
            $this->whatsapp = $row['whatsapp'];
            $this->role = $row['role'];
            $this->department = $row['department'];
            $this->is_active = $row['is_active'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET username = :username,
                      email = :email,
                      full_name = :full_name,
                      phone = :phone,
                      whatsapp = :whatsapp,
                      role = :role,
                      department = :department,
                      is_active = :is_active
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':whatsapp', $this->whatsapp);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':department', $this->department);
        $stmt->bindParam(':is_active', $this->is_active);

        return $stmt->execute();
    }

    public function updatePassword($new_password) {
        $query = "UPDATE " . $this->table_name . "
                  SET password = SHA2(:password, 256)
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':password', $new_password);

        return $stmt->execute();
    }

    public function delete() {
        $query = "UPDATE " . $this->table_name . "
                  SET is_active = 0
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function getUsersByRole($role) {
        $query = "SELECT id, username, email, full_name, department
                  FROM " . $this->table_name . "
                  WHERE role = :role AND is_active = 1
                  ORDER BY full_name";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->execute();

        return $stmt;
    }

    public function getUsersByDepartment($department) {
        $query = "SELECT id, username, email, full_name, role
                  FROM " . $this->table_name . "
                  WHERE department = :department AND is_active = 1
                  ORDER BY full_name";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department', $department);
        $stmt->execute();

        return $stmt;
    }

    public function emailExists($email, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function usernameExists($username, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
?>