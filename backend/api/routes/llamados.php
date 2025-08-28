<?php
// routes/llamados.php
require_once '../controller/LlamadosController.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    $controller = new LlamadosController();
    echo $controller->getLlamados();
} elseif ($method === 'POST') {
    // Handle job applications
    if (isset($input['action']) && $input['action'] === 'postular' && 
        isset($input['usuario_id']) && isset($input['llamado_id'])) {
        
        $controller = new LlamadosController();
        echo $controller->postular($input['usuario_id'], $input['llamado_id']);
    } elseif (isset($input['action']) && $input['action'] === 'get_user_applications' && 
               isset($input['usuario_id'])) {
        
        $controller = new LlamadosController();
        echo $controller->getUserApplications($input['usuario_id']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Datos incompletos para la acción']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
}
