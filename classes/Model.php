<?php
abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function all() {
        $sql = "SELECT * FROM {$this->table}";
        $result = $this->db->query($sql);
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        return $items;
    }

    public function create($data) {
        if (!is_array($data)) {
            return false;
        }

        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        
        // Convert array values to JSON strings
        $values = array_map(function($value) {
            return is_array($value) ? json_encode($value) : $value;
        }, $values);
        
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") 
                VALUES ($placeholders)";
        
        $stmt = $this->db->prepare($sql);
        $types = str_repeat('s', count($fields)); // Default to string type
        $stmt->bind_param($types, ...$values);
        
        if ($stmt->execute()) {
            return $this->db->getLastId();
        }
        return false;
    }

    public function update($id, $data) {
        if (!is_array($data)) {
            return false;
        }

        $fields = array_keys($data);
        $values = array_values($data);
        
        // Convert array values to JSON strings
        $values = array_map(function($value) {
            return is_array($value) ? json_encode($value) : $value;
        }, $values);
        
        $set = implode('=?,', $fields) . '=?';
        
        $sql = "UPDATE {$this->table} SET $set WHERE {$this->primaryKey} = ?";
        
        $stmt = $this->db->prepare($sql);
        $types = str_repeat('s', count($fields)) . 'i'; // Add 'i' for the ID
        $values[] = $id;
        $stmt->bind_param($types, ...$values);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function where($conditions, $params = []) {
        $sql = "SELECT * FROM {$this->table} WHERE $conditions";
        $stmt = $this->db->prepare($sql);
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        return $items;
    }
} 