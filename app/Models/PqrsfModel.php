<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

/**
 * Model responsible for PQRSF database operations
 * Follows Single Responsibility Principle - only handles data persistence
 */
class PqrsfModel
{
    /**
     * Creates a new PQRSF record in the database
     * 
     * @param array $data PQRSF data
     * @return int|false Returns the new PQRSF ID on success, false on failure
     */
    public function create(array $data)
    {       
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();

            // 1. Get the next ID from the sequence
            $stmt = $db->query("SELECT NEXTVAL('formulario_pqr_formulario_pqr_id_seq') as seq");
            $pqrId = (int) $stmt->fetchColumn();

            // 2. Insert the main form data
            $sql = "INSERT INTO formulario_pqr (
                        formulario_pqr_id, tipo_documento, documento, nombres, 
                        nombres_peticionario, correo_electronico, telefono_celular, 
                        servicio, tipo_id_aseguradora, aseguradora_id, tipo_fuente_id, 
                        observacion, acepta_terminos, tipo_documento_peticionario, 
                        documento_peticionario, correo_electronico_peticionario, 
                        telefono_celular_peticionario, direccion_peticionario,
                        enfoque_diferencial_id, enfoque_diferencial_otro
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $pqrId,
                $data['tipo_documento'], 
                $data['documento'], 
                $data['nombres'],
                $data['nombres_peticionario'], 
                $data['correo_electronico'], 
                $data['telefono_celular'],
                $data['servicio'], 
                $data['tipo_id_aseguradora'], 
                $data['aseguradora_id'],
                $data['tipo_fuente_id'], 
                $data['observacion'], 
                $data['acepta_terminos'],
                $data['tipo_documento_peticionario'], 
                $data['documento_peticionario'],
                $data['correo_electronico_peticionario'], 
                $data['telefono_celular_peticionario'],
                $data['direccion_peticionario'],
                implode(', ', $data['enfoques_diferenciales']) ?? null,
                $data['enfoque_diferencial_otro'] ?? null
            ]);

            $db->commit();
            return $pqrId;

        } catch (PDOException $e) {
            $db->rollBack();
            error_log('PQR Create Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Saves file attachments to the database
     * 
     * @param int $pqrId PQRSF ID
     * @param array $files Array of file information
     * @return bool True on success, false on failure
     */
    public function saveAttachments(int $pqrId, array $files): bool
    {
        if (empty($files)) {
            return true; // No files to save
        }
        
        $db = Database::getInstance();
        
        try {
            $sql = "INSERT INTO formulario_pqr_anexos (formulario_pqr_id, ruta, archivo) VALUES (?, ?, ?)";
            $stmt = $db->prepare($sql);
            
            foreach ($files as $file) {
                $stmt->execute([
                    $pqrId,
                    $file['directory'],
                    $file['filename']
                ]);
            }
            
            return true;
            
        } catch (PDOException $e) {
            error_log('PQR Attachments Save Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Gets the description of a tipo_fuente by its ID
     * 
     * @param int|string $tipoFuenteId Tipo fuente ID
     * @return string Description or default text
     */
    public function getTipoFuenteDescripcion($tipoFuenteId): string
    {
        $db = Database::getInstance();
        
        try {
            $stmt = $db->prepare("SELECT descripcion FROM formulario_pqr_tipo_fuente WHERE tipo_fuente_id = ?");
            $stmt->execute([$tipoFuenteId]);
            $result = $stmt->fetchColumn();
            
            return $result ?: 'No especificado';
            
        } catch (PDOException $e) {
            error_log('Error getting tipo_fuente description: ' . $e->getMessage());
            return 'No especificado';
        }
    }
    
    /**
     * Finds a PQRSF by ID
     * 
     * @param int $id PQRSF ID
     * @return array|false PQRSF data or false if not found
     */
    public function findById(int $id)
    {
        $db = Database::getInstance();
        
        try {
            $stmt = $db->prepare("SELECT * FROM formulario_pqr WHERE formulario_pqr_id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ?: false;
            
        } catch (PDOException $e) {
            error_log('Error finding PQRSF: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Gets attachments for a PQRSF
     * 
     * @param int $pqrId PQRSF ID
     * @return array Array of attachments
     */
    public function getAttachments(int $pqrId): array
    {
        $db = Database::getInstance();
        
        try {
            $stmt = $db->prepare("SELECT * FROM formulario_pqr_anexos WHERE formulario_pqr_id = ?");
            $stmt->execute([$pqrId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log('Error getting PQRSF attachments: ' . $e->getMessage());
            return [];
        }
    }
}