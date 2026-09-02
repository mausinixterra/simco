<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Lang;

/**
 * Service responsible for generating email templates
 * Renders email templates from view files with data binding
 */
class EmailTemplateService
{
    private string $templatePath;
    
    public function __construct()
    {
        $this->templatePath = __DIR__ . '/../Views/emails/';
    }
    
    /**
     * Generates PQRSF confirmation email template (HTML version)
     */
    public function getPqrsfConfirmationHtml(
        string $nombrePaciente,
        int $pqrsfId,
        string $tipoSolicitante,
        string $observacion,
        array $attachedFiles = [],
        string $lang = 'es'
    ): string {
        return $this->render('pqrsf-confirmation.html.php', [
            'nombrePaciente' => $nombrePaciente,
            'pqrsfId' => $pqrsfId,
            'tipoSolicitante' => $tipoSolicitante,
            'observacion' => $observacion,
            'attachedFiles' => $attachedFiles,
            'fecha' => date('d/m/Y H:i:s')
        ], $lang);
    }
    
    /**
     * Generates PQRSF confirmation email template (plain text version)
     */
    public function getPqrsfConfirmationPlainText(
        string $nombrePaciente,
        int $pqrsfId,
        string $tipoSolicitante,
        string $observacion,
        array $attachedFiles = [],
        string $lang = 'es'
    ): string {
        return $this->render('pqrsf-confirmation.txt.php', [
            'nombrePaciente' => $nombrePaciente,
            'pqrsfId' => $pqrsfId,
            'tipoSolicitante' => $tipoSolicitante,
            'observacion' => $observacion,
            'attachedFiles' => $attachedFiles,
            'fecha' => date('d/m/Y H:i:s')
        ], $lang);
    }
    
    /**
     * Generates satisfaction notification email template (HTML version)
     */
    public function getSatisfactionNotificationHtml(
        int $pqrsfId,
        string $conformidad,
        string $motivo,
        string $lang = 'es'
    ): string {
        return $this->render('satisfaction-notification.html.php', [
            'pqrsfId' => $pqrsfId,
            'conformidad' => $conformidad,
            'conformidadText' => ucfirst($conformidad),
            'motivo' => $motivo,
            'fecha' => date('d/m/Y H:i:s')
        ], $lang);
    }
    
    /**
     * Generates satisfaction notification email template (plain text version)
     */
    public function getSatisfactionNotificationPlainText(
        int $pqrsfId,
        string $conformidad,
        string $motivo,
        string $lang = 'es'
    ): string {
        return $this->render('satisfaction-notification.txt.php', [
            'pqrsfId' => $pqrsfId,
            'conformidad' => $conformidad,
            'conformidadText' => ucfirst($conformidad),
            'motivo' => $motivo,
            'fecha' => date('d/m/Y H:i:s')
        ], $lang);
    }
    
    /**
     * Renders a template file with given variables
     * 
     * @param string $template Template filename (relative to template path)
     * @param array $data Variables to extract into template scope
     * @param string $lang Language code to load
     * @return string Rendered template content
     */
    private function render(string $template, array $data, string $lang = 'es'): string
    {
        // Load the language for this template rendering
        Lang::load($lang);
        
        $templateFile = $this->templatePath . $template;
        
        if (!file_exists($templateFile)) {
            error_log("Email template not found: {$templateFile}");
            return '';
        }
        
        // Extract variables into local scope
        extract($data, EXTR_SKIP);
        
        // Start output buffering
        ob_start();
        
        // Include template file
        include $templateFile;
        
        // Get buffered content and clean buffer
        $content = ob_get_clean();
        
        return $content !== false ? $content : '';
    }
}
