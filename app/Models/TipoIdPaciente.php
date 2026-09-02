<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use App\Helpers\TextHelper;
use PDO;

class TipoIdPaciente
{
    public readonly string $tipo_id_paciente;
    public readonly string $descripcion;

    /**
     * Obtiene todos los tipos de ID de la base de datos.
     *
     * @return array
     */
    public static function all(): array
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->query('SELECT tipo_id_paciente, descripcion FROM tipos_id_pacientes ORDER BY descripcion ASC');
            
            // Obtener como objetos estándar para poder modificar
            $resultados = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            // Formatear descripciones: Primera letra en mayúscula, resto en minúscula
            foreach ($resultados as $resultado) {
                $resultado->descripcion = TextHelper::formatearTextoTitulo($resultado->descripcion);
            }
            
            return $resultados;
        } catch (\PDOException $e) {
            // En un caso real, loguearíamos el error.
            // Por ahora, retornamos un array vacío para no romper la vista.
            error_log($e->getMessage());
            return [];
        }
    }
}