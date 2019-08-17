<?php
session_start();

class Database {

    private $conn;

    public function __construct() {
        $global = require "global.php";
        try {
            // Connect mysql using pdo
            $this->conn = new PDO("mysql:dbname=" . $global["DB_NAME"] . ";host=" . $global["DB_HOST"],
                $global["DB_USER"], $global["DB_PASS"]);
        } catch (PDOException $pdoe) {
            exit("Database Connection Error: $pdoe->getMessage()");
        }
    }

    public static function conn() {
        return new self();
    }

    // SELECT a single record
    public function search($query, $options = array()) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($options);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // SELECT batch records
    public function searchAll($query, $options = array()) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($options);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function execute($query, $options = array()) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($options);
        return $stmt->rowCount();
    }
}