<?php

declare(strict_types=1);

use App\Controllers\PqrsfController;
use App\Controllers\SatisfactionController;

/** @var App\Core\Router $router */

// This route handles displaying the form when the page loads.
$router->get('/', [PqrsfController::class, 'publicForm']);

// --- NEW ROUTE ---
// This route handles the form submission. It points to the same method
// because our controller is designed to handle both GET and POST.
$router->post('/', [PqrsfController::class, 'publicForm']);

// Success page after PQRSF registration
$router->get('pqrsf/success', [PqrsfController::class, 'showSuccess']);

// Insurance form routes
$router->get('aseguradoras', [PqrsfController::class, 'insuranceForm']);
$router->post('aseguradoras', [PqrsfController::class, 'insuranceForm']);

// Satisfaction response routes
$router->get('satisfaccion', [SatisfactionController::class, 'showForm']);
$router->post('satisfaccion', [SatisfactionController::class, 'showForm']);