<?php

declare(strict_types=1);

namespace App\Helpers;

class TextHelper
{
    /**
     * Formatea un texto: Primera letra en mayúscula, resto en minúscula.
     * Preserva siglas conocidas en mayúsculas.
     * 
     * @param string $texto El texto a formatear
     * @return string El texto formateado
     */
    public static function formatearTextoTitulo(string $texto): string
    {
        // Lista de siglas que deben mantenerse en mayúsculas
        $siglas = [
            'NIT', 'UCI', 'NEO', 'EPS', 'IPS', 'SOAT', 'ARL', 'AFP', 'CCF', 
            'RUT', 'DNI', 'RH', 'VIH', 'SIDA', 'SOS', 'POS', 
            'S.A.S', 'S.A.S.', 'SAS', 'S.A.', 'S.A', 'SA', 'S A S', 'S A',
        ];
        
        // Convertir todo a minúsculas primero
        $textoMinuscula = mb_strtolower($texto, 'UTF-8');
        
        // Capitalizar solo la primera letra del texto completo
        $textoFormateado = mb_strtoupper(mb_substr($textoMinuscula, 0, 1, 'UTF-8'), 'UTF-8') 
                         . mb_substr($textoMinuscula, 1, null, 'UTF-8');
        
        // Reemplazar siglas en minúsculas por mayúsculas
        foreach ($siglas as $sigla) {
            $siglaMinuscula = mb_strtolower($sigla, 'UTF-8');
            // Reemplazar cuando la sigla está como palabra completa o separada
            $textoFormateado = preg_replace(
                '/\b' . preg_quote($siglaMinuscula, '/') . '\b/iu', 
                $sigla, 
                $textoFormateado
            );
        }
        
        return $textoFormateado;
    }

    /**
     * Formats a person or service name to proper case (multibyte-safe)
     * 
     * @param string $nombre El nombre a formatear
     * @return string El nombre formateado
     */
    public static function formatearNombre(string $nombre): string
    {
        return mb_convert_case($nombre, MB_CASE_TITLE, 'UTF-8');
    }
}
