<?php
// modelo/UserModel.php
require_once 'Conexion.php';

class UserModel {
    private $conn;
    
    public function __construct() {
        $this->conn = connection();
    }
    
    // Register a new user
    public function registerUser($nombre, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $nombre, $email, $hashedPassword);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id; // Return the new user ID
        }
        return false;
    }
    
    // Login user
    public function loginUser($email, $password) {
        $sql = "SELECT id, nombre, email, password FROM usuarios WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                return $user; // Return user data without password
            }
        }
        return false;
    }
    
    // Check if email already exists
    public function emailExists($email) {
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }
    
    // Get user by ID
    public function getUserById($id) {
        $sql = "SELECT id, nombre, email FROM usuarios WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
}