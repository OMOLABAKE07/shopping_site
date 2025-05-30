<?php
class Database {
    private $conn;
    private static $instance = null;

    private function __construct() {
        require_once __DIR__ . '/../includes/config.php';
        $this->conn = $conn;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function query($sql) {
        return mysqli_query($this->conn, $sql);
    }

    public function prepare($sql) {
        return mysqli_prepare($this->conn, $sql);
    }

    public function escape($value) {
        return mysqli_real_escape_string($this->conn, $value);
    }

    public function getLastId() {
        return mysqli_insert_id($this->conn);
    }

    public function getAffectedRows() {
        return mysqli_affected_rows($this->conn);
    }

    public function beginTransaction() {
        mysqli_begin_transaction($this->conn);
    }

    public function commit() {
        mysqli_commit($this->conn);
    }

    public function rollback() {
        mysqli_rollback($this->conn);
    }
} 