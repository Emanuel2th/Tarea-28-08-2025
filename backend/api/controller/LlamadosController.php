<?php
// controller/LlamadosController.php
require_once '../modelo/LlamadosModel.php';

class LlamadosController {
    private $model;
    
    public function __construct() {
        $this->model = new LlamadosModel();
    }
    
    public function getLlamados() {
        $llamados = $this->model->traerLlamados();
        return json_encode(['llamados' => $llamados]);
    }
    
    // Handle job application
    public function postular($usuario_id, $llamado_id) {
        $result = $this->model->postular($usuario_id, $llamado_id);
        return json_encode($result);
    }
    
    // Get user applications
    public function getUserApplications($usuario_id) {
        $applications = $this->model->getUserApplications($usuario_id);
        return json_encode(['applications' => $applications]);
    }
}
