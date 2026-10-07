<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use App\Core\Lang;
use PDO;
use PDOException;

/**
 * Model responsible for satisfaction response database operations
 * Follows Single Responsibility Principle - only handles data persistence
 */
class SatisfactionModel
{
    /**
     * Gets PQRSF data for satisfaction response
     * 
     * @param int $id PQRSF ID
     * @return array|false PQRSF data or false if not found or already answered
     */
    public function getPqrsfDataForSatisfaction(int $id)
    {
        $db = Database::getInstance();
        
        try {
            $query = "SELECT 
                        fp.formulario_pqr_id AS id,
                        fp.tipo_documento || ' ' || fp.documento AS documentopaciente,
                        fp.nombres AS nombrepaciente,
                        fp.nombres_peticionario AS nombrepeticionario,
                        fppe.id AS servicio_id,
                        fppe.nombre AS servicio
                    FROM
                        formulario_pqr fp
                    INNER JOIN formulario_pqr_seguimiento fps ON fp.formulario_pqr_id = fps.formulario_pqr_id
                    INNER JOIN formulario_pqr_presento_evento fppe ON fppe.id = fp.servicio 
                    WHERE
                        fp.formulario_pqr_id = :id
                        AND fps.fecha_respuesta_conformidad IS NULL";
            
            $stmt = $db->prepare($query);
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return false;
            }

            $result['servicio'] = Lang::catalog('formulario_pqr_presento_evento', (string) $result['servicio_id'], $result['servicio']);

            return $result;

        } catch (PDOException $e) {
            error_log('Error getting PQRSF data for satisfaction: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Inserts satisfaction response conformity data
     * 
     * @param int $id PQRSF ID
     * @param string $conformidad 'si' or 'no'
     * @param string $motivo Reason if not satisfied (can be empty)
     * @param string $ip IP address of the respondent
     * @return bool True on success, false on failure
     */
    public function insertSatisfactionResponse(int $id, string $conformidad, string $motivo, string $ip): bool
    {
        $db = Database::getInstance();
        
        try {
            $query = "UPDATE formulario_pqr_seguimiento
                        SET motivo = :motivo,
                            conforme = :conformidad,
                            fecha_respuesta_conformidad = NOW(),
                            ip_respuesta = :ip
                        WHERE formulario_pqr_id = :id";
            
            $stmt = $db->prepare($query);
            $result = $stmt->execute([
                'motivo' => $motivo === '' ? null : $motivo,
                'conformidad' => $conformidad,
                'ip' => $ip,
                'id' => $id,
            ]);
            
            return $result;
            
        } catch (PDOException $e) {
            error_log('Error inserting satisfaction response: ' . $e->getMessage());
            return false;
        }
    }
}
