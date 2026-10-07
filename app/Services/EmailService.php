<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Service responsible for sending emails
 * Handles SMTP configuration and email delivery
 * Uses EmailTemplateService for content generation
 */
class EmailService
{
    private PHPMailer $mailer;
    private EmailTemplateService $templateService;
    
    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->templateService = new EmailTemplateService();
        $this->configure();
    }
    
    /**
     * Configura PHPMailer con las variables de entorno
     */
    private function configure(): void
    {
        try {
            // Configuración del servidor SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $_ENV['MAIL_USERNAME'] ?? '';
            $this->mailer->Password = $_ENV['MAIL_PASSWORD'] ?? '';
            
            // Configurar encriptación según el tipo
            $encryption = $_ENV['MAIL_ENCRYPTION'] ?? 'tls';
            if ($encryption === 'ssl') {
                $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            
            $this->mailer->Port = (int)($_ENV['MAIL_PORT'] ?? 587);
            
            // Configuración del remitente
            $fromAddress = $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@simco.com';
            $fromName = $_ENV['MAIL_FROM_NAME'] ?? 'SIMCO - Sistema de PQRSF';
            
            $this->mailer->setFrom($fromAddress, $fromName);
            $this->mailer->addReplyTo($fromAddress, 'No responder');
            
            // Configuración de codificación
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->Encoding = 'base64';
            $this->mailer->isHTML(true);
            
        } catch (Exception $e) {
            error_log('Error al configurar EmailService: ' . $e->getMessage());
        }
    }
    
    /**
     * Sends PQRSF confirmation email to patient or advisor
     * 
     * @param string $to Recipient email address
     * @param string $nombrePaciente Patient/recipient name
     * @param int $pqrsfId PQRSF ID
     * @param string $tipoSolicitante Type of requester
     * @param string $observacion Observation/details
     * @param array $attachedFiles Array of attached files with path, name
     * @return bool True if sent successfully
     */
    public function sendPqrsfConfirmation(
        string $to,
        string $nombrePaciente,
        int $pqrsfId,
        string $tipoSolicitante,
        string $observacion,
        array $attachedFiles = [],
        string $lang = 'es'
    ): bool {
        try {
            // Prepare email
            $this->prepareEmail();
            
            // Set recipient
            $this->mailer->addAddress($to, $nombrePaciente);
            
            // Set subject based on language
            if ($lang === 'en') {
                $this->mailer->Subject = "Registration Confirmation - PQRSF #{$pqrsfId} - Clínica de Occidente";
            } else {
                $this->mailer->Subject = "Confirmación de Registro - PQRSF #{$pqrsfId} - Clínica de Occidente";
            }
            
            // Add embedded logos
            $this->addEmbeddedLogos();
            
            // Add user attachments
            $this->addFileAttachments($attachedFiles);
            
            // Generate and set email body using template service
            $this->mailer->isHTML(true);
            $this->mailer->Body = $this->templateService->getPqrsfConfirmationHtml(
                $nombrePaciente,
                $pqrsfId,
                $tipoSolicitante,
                $observacion,
                $attachedFiles,
                $lang
            );
            
            $this->mailer->AltBody = $this->templateService->getPqrsfConfirmationPlainText(
                $nombrePaciente,
                $pqrsfId,
                $tipoSolicitante,
                $observacion,
                $attachedFiles,
                $lang
            );
            
            // Send
            $this->mailer->send();
            return true;
            
        } catch (Exception $e) {
            error_log('Error sending PQRSF email: ' . $this->mailer->ErrorInfo);
            return false;
        }
    }
    
    /**
     * Prepares email by clearing previous recipients and attachments
     */
    private function prepareEmail(): void
    {
        $this->mailer->clearAddresses();
        $this->mailer->clearAttachments();
    }
    
    /**
     * Adds embedded logo images to email
     */
    private function addEmbeddedLogos(): void
    {
        // $logoPath = __DIR__ . '/../../public_html/assets/img/logo_cdo_blanco.png';
        // if (file_exists($logoPath)) {
        //     $this->mailer->addEmbeddedImage($logoPath, 'logo_cdo_blanco', 'logo_cdo_blanco.png');
        // }
        
        $logoHeaderPath = __DIR__ . '/../../public_html/assets/img/logo_header_light.png';
        if (file_exists($logoHeaderPath)) {
            $this->mailer->addEmbeddedImage($logoHeaderPath, 'logo_header', 'logo_header_light.png');
        }
    }
    
    /**
     * Adds file attachments to email
     * 
     * @param array $attachedFiles Array of files with 'path' and 'name' keys
     */
    private function addFileAttachments(array $attachedFiles): void
    {
        foreach ($attachedFiles as $file) {
            if (isset($file['path']) && file_exists($file['path'])) {
                $this->mailer->addAttachment(
                    $file['path'],
                    $file['name'] ?? basename($file['path'])
                );
            }
        }
    }
    
    /**
     * Sends satisfaction notification email to service team
     * 
     * @param int $pqrsfId PQRSF ID
     * @param string $conformidad 'si' or 'no'
     * @param string $motivo Reason if not satisfied
     * @return bool True if sent successfully
     */
    public function sendSatisfactionNotification(int $pqrsfId, string $conformidad, string $motivo, string $lang = 'es'): bool
    {
        try {
            // Prepare email
            $this->prepareEmail();
            
            // Set recipients - service team
            $this->mailer->addAddress('servicioalcliente@clinicadeoccidente.com.co', 'Servicio al Cliente');
            $this->mailer->addCC('coordinacion.servicioalcliente@clinicadeoccidente.com', 'Coordinación Servicio al Cliente');
            
            // Set subject based on language
            if ($lang === 'en') {
                $this->mailer->Subject = "Response Satisfaction - PQRSF #{$pqrsfId}";
            } else {
                $this->mailer->Subject = "Conformidad de Respuesta - PQRSF #{$pqrsfId}";
            }
            
            // Generate and set email body using template service
            $this->mailer->isHTML(true);
            $this->mailer->Body = $this->templateService->getSatisfactionNotificationHtml(
                $pqrsfId,
                $conformidad,
                $motivo,
                $lang
            );
            
            $this->mailer->AltBody = $this->templateService->getSatisfactionNotificationPlainText(
                $pqrsfId,
                $conformidad,
                $motivo,
                $lang
            );
            
            // Send
            $this->mailer->send();
            return true;
            
        } catch (Exception $e) {
            error_log('Error sending satisfaction notification: ' . $this->mailer->ErrorInfo);
            return false;
        }
    }
}
