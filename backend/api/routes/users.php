<?php
// routes/users.php
require_once '../controller/UserController.php';

header('Content-Type: application/json');

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Get the request data
$input = json_decode(file_get_contents('php://input'), true);

// Create user controller instance
$controller = new UserController();

switch ($method) {
    case 'POST':
        // Check the action from the request
        if (isset($input['action'])) {
            switch ($input['action']) {
                case 'register':
                    // Register user
                    if (isset($input['nombre']) && isset($input['email']) && isset($input['password'])) {
                        echo $controller->registerUser($input['nombre'], $input['email'], $input['password']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Faltan datos para el registro']);
                    }
                    break;
                    
                case 'login':
                    // Login user
                    if (isset($input['email']) && isset($input['password'])) {
                        echo $controller->loginUser($input['email'], $input['password']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Faltan datos para el login']);
                    }
                    break;
                    
                default:
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
                    break;
            }
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción no especificada']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        break;
}