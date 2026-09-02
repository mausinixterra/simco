<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PqrsfModel;
use App\Core\Lang;

/**
 * Service responsible for PQRSF business logic
 * Orchestrates the creation of PQRSF entries by coordinating
 * file uploads, database operations, and email notifications
 */
class PqrsfService
{
    private PqrsfModel $pqrsfModel;
    private FileUploadService $fileUploadService;
    private EmailService $emailService;
    
    public function __construct()
    {
        $this->pqrsfModel = new PqrsfModel();
        $this->fileUploadService = new FileUploadService();
        $this->emailService = new EmailService();
    }
    
    /**
     * Creates a new PQRSF entry with all associated data
     * 
     * @param array $data Validated and sanitized form data
     * @param array $files Uploaded files
     * @param bool $isInsuranceForm Whether this is from the insurance form (sends email to advisor)
     * @return array Result with success status, message, and additional data
     */
    public function createPqrsf(array $data, array $files, bool $isInsuranceForm = false): array
    {
        try {
            // 1. Save the PQRSF to database and get the ID
            $pqrsfId = $this->pqrsfModel->create($data);
            
            if ($pqrsfId === false) {
                return [
                    'success' => false,
                    'message' => Lang::get('error_pqrsf_registration_failed')
                ];
            }
            
            // 2. Upload files if present
            $uploadedFiles = $this->fileUploadService->uploadPqrsfFiles($pqrsfId, $files);
            
            // 3. Save file records to database
            if (!empty($uploadedFiles)) {
                $this->pqrsfModel->saveAttachments($pqrsfId, $uploadedFiles);
            }
            
            // 4. Send confirmation email (to advisor if insurance form, to patient otherwise)
            $emailSent = $this->sendConfirmationEmail($pqrsfId, $data, $uploadedFiles, $isInsuranceForm);
            
            return [
                'success' => true,
                'message' => Lang::get('success_pqrsf_registered'),
                'pqrsfId' => $pqrsfId,
                'emailSent' => $emailSent
            ];
            
        } catch (\Exception $e) {
            error_log('Error creating PQRSF: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => Lang::get('error_pqrsf_registration_failed')
            ];
        }
    }
    
    /**
     * Sends a confirmation email to the patient or advisor
     * 
     * @param int $pqrsfId ID of the registered PQRSF
     * @param array $data Patient and form data
     * @param array $uploadedFiles List of uploaded files
     * @param bool $isInsuranceForm Whether to send to advisor instead of patient
     * @return bool True if email was sent successfully
     */
    private function sendConfirmationEmail(int $pqrsfId, array $data, array $uploadedFiles, bool $isInsuranceForm = false): bool
    {
        try {
            // Check if email is enabled in configuration
            $config = require __DIR__ . '/../../config/app.php';
            
            if (!isset($config['mail']['enabled']) || !$config['mail']['enabled']) {
                error_log('Email sending is disabled in configuration');
                return false;
            }
            
            // Get the current language from session, default to 'es'
            $lang = $_SESSION['lang'] ?? $config['default_lang'] ?? 'es';
            
            // Get the tipo_fuente description
            $tipoSolicitante = $this->pqrsfModel->getTipoFuenteDescripcion($data['tipo_fuente_id']);
            
            // Determine recipient email and name based on form type
            if ($isInsuranceForm) {
                // For insurance form, send to advisor (stored in petitioner fields)
                $recipientEmail = $data['correo_electronico_peticionario'];
                $recipientName = $data['nombres_peticionario'];
            } else {
                // For public form, send to patient
                $recipientEmail = $data['correo_electronico'];
                $recipientName = $data['nombres'];
            }
            
            // Send the email
            $emailSent = $this->emailService->sendPqrsfConfirmation(
                $recipientEmail,
                $recipientName,
                $pqrsfId,
                $tipoSolicitante,
                $data['observacion'],
                $uploadedFiles,
                $lang
            );
            
            if ($emailSent) {
                error_log("Confirmation email sent successfully to: {$recipientEmail} for PQRSF #{$pqrsfId}");
            } else {
                error_log("Failed to send confirmation email to: {$recipientEmail} for PQRSF #{$pqrsfId}");
            }
            
            return $emailSent;
            
        } catch (\Exception $e) {
            error_log('Error sending confirmation email: ' . $e->getMessage());
            return false;
        }
    }
}
