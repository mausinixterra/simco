<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Lang;

/**
 * Service responsible for validating and sanitizing input data
 */
class InputValidator
{
    /**
     * Sanitizes and validates PQRSF form data
     * 
     * @param array $post POST data from form submission
     * @param array $files FILES data from form submission
     * @return array Returns ['valid' => bool, 'data' => array, 'errors' => array]
     */
    public function validatePqrsfForm(array $post, array $files): array
    {
        $data       = $this->sanitizePqrsfData($post);
        $errors     = $this->validatePqrsfData($data);
        $fileErrors = $this->validateFiles($files);
        $errors     = array_merge($errors, $fileErrors);
        
        return [
            'valid'  => empty($errors),
            'data'   => $data,
            'errors' => $errors
        ];
    }
    
    /**
     * Sanitizes PQRSF form data
     * 
     * @param array $post Raw POST data
     * @return array Sanitized data
     */
    private function sanitizePqrsfData(array $post): array
    {
        $aseguradoraParts = explode('*_*', $post['aseguradora'] ?? '');

        $enfoquesDiferenciales = [];
        if (isset($post['enfoques_diferenciales']) && is_array($post['enfoques_diferenciales'])) {
            $enfoquesDiferenciales = array_map(function($value) {
                return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            }, $post['enfoques_diferenciales']);
        }

        return [
            // Datos del Paciente
            'tipo_fuente_id'           => filter_var($post['tipo_solicitante'] ?? '', FILTER_SANITIZE_NUMBER_INT),
            'servicio'                 => filter_var($post['servicio'] ?? '', FILTER_SANITIZE_NUMBER_INT),
            'nombres'                  => trim(filter_var($post['nombres'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS)),
            'tipo_documento'           => trim(filter_var($post['tipo_documento'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS)),
            'documento'                => trim(filter_var($post['documento'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS)),
            'correo_electronico'       => trim(filter_var($post['correo_electronico'] ?? '', FILTER_SANITIZE_EMAIL)),
            'telefono_celular'         => preg_replace('/[^0-9]/', '', $post['telefono_celular'] ?? ''),
            'tipo_id_aseguradora'      => $aseguradoraParts[0] ?? null,
            'aseguradora_id'           => $aseguradoraParts[1] ?? null,
            'enfoques_diferenciales'   => $enfoquesDiferenciales,
            'enfoque_diferencial_otro' => trim(filter_var($post['enfoque_diferencial_otro'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS)),
            
            // Datos del Peticionario
            'nombres_peticionario'            => trim(filter_var($post['nombres_peticionario'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS)),
            'tipo_documento_peticionario'     => trim(filter_var($post['tipo_documento_peticionario'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS)),
            'documento_peticionario'          => trim(filter_var($post['documento_peticionario'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS)),
            'direccion_peticionario'          => trim(filter_var($post['direccion_peticionario'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS)),
            'correo_electronico_peticionario' => trim(filter_var($post['correo_electronico_peticionario'] ?? '', FILTER_SANITIZE_EMAIL)),
            'telefono_celular_peticionario'   => preg_replace('/[^0-9]/', '', $post['telefono_celular_peticionario'] ?? ''),
            
            // Datos del Evento y Autorización
            'observacion'     => trim(filter_var($post['observacion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS)),
            'acepta_terminos' => isset($post['authorization']) && $post['authorization'] === '1' ? '1' : '0',
        ];
    }
    
    /**
     * Validates PQRSF data
     * 
     * @param array $data Sanitized data to validate
     * @return array Array of error messages
     */
    private function validatePqrsfData(array $data): array
    {
        $errors = [];
        
        // Required fields validation
        if (empty($data['tipo_fuente_id'])) {
            $errors[] = Lang::get('error_applicant_type_required');
        }
        
        if (empty($data['servicio'])) {
            $errors[] = Lang::get('error_service_required');
        }
        
        if (empty($data['nombres'])) {
            $errors[] = Lang::get('error_patient_name_required');
        }
        
        if (empty($data['tipo_documento'])) {
            $errors[] = Lang::get('error_patient_id_type_required');
        }
        
        if (empty($data['documento'])) {
            $errors[] = Lang::get('error_patient_id_number_required');
        }
        
        if (empty($data['aseguradora_id'])) {
            $errors[] = Lang::get('error_insurer_required');
        }

        if (empty($data['enfoques_diferenciales']) || !is_array($data['enfoques_diferenciales']) || count($data['enfoques_diferenciales']) === 0) {
            $errors[] = Lang::get('error_select_at_least_one_option');
        }

        if (!empty($data['enfoques_diferenciales']) && is_array($data['enfoques_diferenciales']) && in_array('12', $data['enfoques_diferenciales']) && empty($data['enfoque_diferencial_otro'])) {
            if (empty($data['enfoque_diferencial_otro'])) {
                $errors[] = Lang::get('error_specify_other_option');
            }
        }
        
        // Email validation
        if (!filter_var($data['correo_electronico'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = Lang::get('error_patient_email_invalid');
        }
        
        // Phone validation
        if (empty($data['telefono_celular']) || strlen($data['telefono_celular']) < 7) {
            $errors[] = Lang::get('error_patient_phone_invalid');
        }
        
        // Observation validation
        if (empty($data['observacion'])) {
            $errors[] = Lang::get('error_event_details_required');
        }
        
        // Terms validation
        if ($data['acepta_terminos'] === '0') {
            $errors[] = Lang::get('error_authorization_required');
        }
        
        return $errors;
    }
    
    /**
     * Validates uploaded files
     * 
     * @param array $files FILES array
     * @return array Array of error messages
     */
    private function validateFiles(array $files): array
    {
        $errors = [];
        
        if (!isset($files['attachments']) || empty($files['attachments']['name'][0])) {
            return $errors; // No files uploaded, which is OK
        }
        
        // Validate number of files
        if (count($files['attachments']['name']) > 5) {
            $errors[] = Lang::get('error_max_files_upload');
        }
        
        // Validate total size and individual file errors
        $totalSize = 0;
        foreach ($files['attachments']['error'] as $key => $error) {
            if ($error === UPLOAD_ERR_OK) {
                $totalSize += $files['attachments']['size'][$key];
            } elseif ($error !== UPLOAD_ERR_NO_FILE) {
                $errors[] = Lang::get('error_file_upload_failed');
                break; // Stop checking if there's an upload error
            }
        }
        
        // Validate total file size (25MB max)
        if ($totalSize > 25 * 1024 * 1024) {
            $errors[] = Lang::get('error_total_file_size');
        }
        
        return $errors;
    }
}
