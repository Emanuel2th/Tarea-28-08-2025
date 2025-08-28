<?php
// modelo/LlamadosModel.php
require_once 'Conexion.php';

class LlamadosModel {
    private $conn;
    public function __construct() {
        $this->conn = connection();
    }
    public function traerLlamados() {
        $sql = "SELECT * FROM vista_llamados_empresas ORDER BY id";
        $result = $this->conn->query($sql);
        $llamados = [];
        while ($row = $result->fetch_assoc()) {
            $llamados[] = $row;
        }
        return $llamados;
    }
    
    // Apply for a job
    public function postular($usuario_id, $llamado_id) {
        // Check if user already applied for this job
        $checkSql = "SELECT id FROM postulaciones WHERE usuario_id = ? AND llamado_id = ?";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->bind_param("ii", $usuario_id, $llamado_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            return ['success' => false, 'message' => 'Ya te has postulado a este llamado'];
        }
        
        // Insert the application
        $sql = "INSERT INTO postulaciones (usuario_id, llamado_id, fecha_postulacion) VALUES (?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $usuario_id, $llamado_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Postulación registrada exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al registrar la postulación'];
        }
    }
    
    // Get user applications with INNER JOIN
    public function getUserApplications($usuario_id) {
        $sql = "SELECT p.fecha_postulacion, l.titulo, l.descripcion, e.nombre as empresa_nombre 
                FROM postulaciones p 
                INNER JOIN llamados l ON p.llamado_id = l.id 
                INNER JOIN empresas e ON l.empresa_id = e.id 
                WHERE p.usuario_id = ? 
                ORDER BY p.fecha_postulacion DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $applications = [];
        while ($row = $result->fetch_assoc()) {
            $applications[] = $row;
        }
        
        return $applications;
    }
}
