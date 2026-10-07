<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\SatisfactionService;
use App\Services\FlashMessageService;
use App\Models\SatisfactionModel;

/**
 * Controller responsible for handling HTTP requests/responses for Satisfaction Response
 * Delegates business logic to services
 */
class SatisfactionController
{
    private SatisfactionService $satisfactionService;
    private FlashMessageService $flashMessage;

    public function __construct()
    {
        $this->satisfactionService = new SatisfactionService();
        $this->flashMessage = new FlashMessageService();
    }

    /**
     * Displays the satisfaction form or response message
     */
    public function showForm(): void
    {
        // Handle POST request (form submission)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleFormSubmission();
            return;
        }

        // Get the encoded ID from query string
        $encodedId = $_GET['id'] ?? null;
        $mensaje = $_GET['mensaje'] ?? null;
        
        // Ignore 'lang' parameter - it's handled by the Lang class
        // This prevents the form from showing errors when changing language

        // Check if we're showing a result message
        if ($mensaje !== null) {
            $this->showResultMessage($mensaje);
            return;
        }

        // If no ID, show connection error (but only if not just changing language)
        if ($encodedId === null) {
            $this->renderView('satisfaction', [
                'showError' => true,
                'errorType' => 'no_connection',
                'pqrsfData' => null,
            ]);
            return;
        }

        // Get PQRSF data
        $pqrsfData = $this->satisfactionService->getPqrsfData($encodedId);

        // If data not found, show error
        if ($pqrsfData === false) {
            $this->renderView('satisfaction', [
                'showError' => true,
                'errorType' => 'not_found',
                'pqrsfData' => null,
            ]);
            return;
        }

        // Show the form
        $this->renderView('satisfaction', [
            'showError' => false,
            'errorType' => null,
            'pqrsfData' => $pqrsfData,
            'encodedId' => $encodedId,
        ]);
    }

    /**
     * Handles form submission using PRG (Post-Redirect-Get) pattern
     */
    private function handleFormSubmission(): void
    {
        // Process satisfaction response
        $result = $this->satisfactionService->processSatisfactionResponse($_POST);
        
        // Redirect based on result
        if ($result['success']) {
            $this->redirect('/satisfaccion?mensaje=exito');
        } else {
            $this->redirect('/satisfaccion?mensaje=error');
        }
    }

    /**
     * Shows result message (success or error)
     * 
     * @param string $mensaje 'exito' or 'error'
     */
    private function showResultMessage(string $mensaje): void
    {
        $isSuccess = $mensaje === 'exito';
        
        $this->renderView('satisfaction', [
            'showError' => false,
            'errorType' => null,
            'pqrsfData' => null,
            'showResult' => true,
            'resultSuccess' => $isSuccess,
        ]);
    }

    /**
     * Renders a view without layout (standalone page)
     * 
     * @param string $viewName Name of the view file
     * @param array $data Data to pass to the view
     */
    private function renderView(string $viewName, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . '/../Views/satisfaction/' . $viewName . '.php';
    }

    /**
     * Redirects to a given URL
     * 
     * @param string $url URL to redirect to
     */
    private function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}
