<?php
// controller/UserController.php
require_once '../modelo/UserModel.php';

class UserController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    // Handle user registration
    public function registerUser($nombre, $email, $password) {
        // Validate input
        if (empty($nombre) || empty($email) || empty($password)) {
            return json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
        }
        
        // Check if email already exists
        if ($this->userModel->emailExists($email)) {
            return json_encode(['success' => false, 'message' => 'El email ya está registrado']);
        }
        
        // Register the user
        $userId = $this->userModel->registerUser($nombre, $email, $password);
        
        if ($userId) {
            // Return success with user data (without password)
            $user = $this->userModel->getUserById($userId);
            return json_encode(['success' => true, 'message' => 'Usuario registrado exitosamente', 'user' => $user]);
        } else {
            return json_encode(['success' => false, 'message' => 'Error al registrar el usuario']);
        }
    }
    
    // Handle user login
    public function loginUser($email, $password) {
        // Validate input
        if (empty($email) || empty($password)) {
            return json_encode(['success' => false, 'message' => 'Email y contraseña son requeridos']);
        }
        
        // Attempt to login
        $user = $this->userModel->loginUser($email, $password);
        
        if ($user) {
            // Return success with user data (without password)
            return json_encode(['success' => true, 'message' => 'Login exitoso', 'user' => $user]);
        } else {
            return json_encode(['success' => false, 'message' => 'Credenciales inválidas']);
        }
    }
}