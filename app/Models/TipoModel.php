<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use App\Helpers\TextHelper;
use PDO;
use stdClass;

class TipoModel
{
    /**
     * Función genérica para obtener listas (tipos) desde cualquier tabla.
     *
     * @param string $tabla El nombre de la tabla en la base de datos.
     * @param string $idCampo El nombre del campo que se usará como valor (ej. 'tipo_fuente_id').
     * @param string $descripcionCampo El nombre del campo que se mostrará (ej. 'descripcion').
     * @param string $orderBy El campo por el cual ordenar los resultados (ej. 'descripcion').
     * @return stdClass[] Un array de objetos con las propiedades id y descripcion.
     */
    public static function getTipos(string $tabla, string $idCampo, string $descripcionCampo, string $orderBy = 'descripcion'): array
    {
        
        // Medida de seguridad para evitar inyección SQL en nombres de tabla/columna.
        // Solo permite caracteres alfanuméricos y guiones bajos.
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $tabla) || !preg_match('/^[a-zA-Z0-9_]+$/', $idCampo) || !preg_match('/^[a-zA-Z0-9_]+$/', $descripcionCampo)) {
            throw new \InvalidArgumentException('Nombre de tabla o campo no válido.');
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $orderBy)) {
            throw new \InvalidArgumentException('Campo de ordenamiento no válido.');
        }

        $db = Database::getInstance();

        $query = "  SELECT 
                        {$idCampo} AS id, 
                        {$descripcionCampo} AS descripcion 
                    FROM {$tabla} 
                    ORDER BY {$orderBy} ASC";
        
        $stmt       = $db->query($query);
        $resultados = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        // Formatear descripciones: Primera letra en mayúscula, resto en minúscula
        foreach ($resultados as $resultado) {
            $resultado->descripcion = TextHelper::formatearTextoTitulo($resultado->descripcion);
        }
        
        return $resultados;
    }
}