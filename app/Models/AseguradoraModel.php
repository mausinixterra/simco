<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use App\Helpers\TextHelper;
use PDO;
use stdClass;

class AseguradoraModel
{
    /**
     * Gets a list of active insurers.
     * @return stdClass[]
     */
    public static function all(): array
    {
        $sql = "SELECT DISTINCT
                    a.tipo_tercero_id AS tipo_id_tercero,
                    a.tercero_id,
                    b.nombre_tercero
                FROM
                    planes a,
                    terceros b
                WHERE
                    a.empresa_id = :empresa_id
                    AND a.tipo_tercero_id = :tipo_tercero_id
                    AND a.tipo_tercero_id = b.tipo_id_tercero
                    AND a.tercero_id = b.tercero_id
                    AND a.estado = :estado
                ORDER BY
                    b.nombre_tercero";

        $db = Database::getInstance();
        $stmt = $db->prepare($sql);

        // Parámetros nombrados para evitar inyección SQL
        $stmt->execute([
            'empresa_id'      => '01',
            'tipo_tercero_id' => 'NIT',
            'estado'          => '1',
        ]);
        
        $resultados = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        // Formatear nombres de aseguradoras: Primera letra en mayúscula, resto en minúscula
        foreach ($resultados as $resultado) {
            $resultado->nombre_tercero = TextHelper::formatearTextoTitulo($resultado->nombre_tercero);
        }
        
        return $resultados;
    }
}