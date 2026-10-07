<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SatisfactionModel;
use App\Core\Lang;

/**
 * Service responsible for satisfaction response business logic
 * Orchestrates satisfaction response operations by coordinating
 * database operations and email notifications
 */
class SatisfactionService
{
    private SatisfactionModel $satisfactionModel;
    private EmailService $emailService;
    
    public function __construct()
    {
        $this->satisfactionModel = new SatisfactionModel();
        $this->emailService = new EmailService();
    }
    
    /**
     * Gets PQRSF data for satisfaction form
     * 
     * @param string $encodedId Base64 encoded PQRSF ID
     * @return array|false PQRSF data or false if invalid/not found
     */
    public function getPqrsfData(string $encodedId)
    {
        // Decode the ID
        $decodedId = base64_decode($encodedId, true);
        
        // Validate that it's a valid number
        if ($decodedId === false || !is_numeric($decodedId)) {
            return false;
        }
        
        $id = (int) $decodedId;
        
        // Get PQRSF data
        return $this->satisfactionModel->getPqrsfDataForSatisfaction($id);
    }
    
    /**
     * Processes satisfaction response
     * 
     * @param array $data Form data including encoded id, conformidad, and motivo
     * @return array Result with success status and message
     */
    public function processSatisfactionResponse(array $data): array
    {
        try {
            // Decode the ID
            $decodedId = base64_decode($data['id'], true);
            
            if ($decodedId === false || !is_numeric($decodedId)) {
                return [
                    'success' => false,
                    'message' => Lang::get('error_satisfaction_invalid_id'),
                ];
            }
            
            $id = (int) $decodedId;
            $conformidad = $data['conformidad'] ?? '';
            $motivo = $data['motivo'] ?? '';
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            
            // Validate conformidad
            if (!in_array($conformidad, ['si', 'no'])) {
                return [
                    'success' => false,
                    'message' => Lang::get('error_satisfaction_invalid_response'),
                ];
            }
            
            // If not satisfied, motivo is required
            if ($conformidad === 'no' && strlen(trim($motivo)) < 10) {
                return [
                    'success' => false,
                    'message' => Lang::get('error_satisfaction_reason_required'),
                ];
            }
            
            // Insert satisfaction response
            $inserted = $this->satisfactionModel->insertSatisfactionResponse($id, $conformidad, $motivo, $ip);
            
            if (!$inserted) {
                return [
                    'success' => false,
                    'message' => Lang::get('error_satisfaction_save_failed'),
                ];
            }
            
            // Send notification email
            $emailSent = $this->sendSatisfactionNotification($id, $conformidad, $motivo);
            
            return [
                'success' => true,
                'message' => Lang::get('success_satisfaction_registered'),
                'emailSent' => $emailSent,
            ];
            
        } catch (\Exception $e) {
            error_log('Error processing satisfaction response: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => Lang::get('error_satisfaction_save_failed'),
            ];
        }
    }
    
    /**
     * Sends satisfaction notification email to service team
     * 
     * @param int $pqrsfId PQRSF ID
     * @param string $conformidad 'si' or 'no'
     * @param string $motivo Reason if not satisfied
     * @return bool True if email was sent successfully
     */
    private function sendSatisfactionNotification(int $pqrsfId, string $conformidad, string $motivo): bool
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
            
            // Send the email
            $emailSent = $this->emailService->sendSatisfactionNotification(
                $pqrsfId,
                $conformidad,
                $motivo,
                $lang
            );
            
            if ($emailSent) {
                error_log("Satisfaction notification email sent successfully for PQRSF #{$pqrsfId}");
            } else {
                error_log("Failed to send satisfaction notification email for PQRSF #{$pqrsfId}");
            }
            
            return $emailSent;
            
        } catch (\Exception $e) {
            error_log('Error sending satisfaction notification email: ' . $e->getMessage());
            return false;
        }
    }
}
