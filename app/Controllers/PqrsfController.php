<?php

    declare(strict_types=1);

    namespace App\Controllers;

    use App\Models\AseguradoraModel;
    use App\Models\TipoIdPaciente;
    use App\Models\TipoModel;
    use App\Services\FlashMessageService;
    use App\Services\InputValidator;
    use App\Services\PqrsfService;

    /**
     * Controller responsible for handling HTTP requests/responses for PQRSF
     * Delegates business logic to services
     */
    class PqrsfController
    {
        private PqrsfService $pqrsfService;
        private InputValidator $validator;
        private FlashMessageService $flashMessage;

        public function __construct()
        {
            $this->pqrsfService = new PqrsfService();
            $this->validator = new InputValidator();
            $this->flashMessage = new FlashMessageService();
        }

        /**
         * Displays the public form and handles form submission
         */
        public function publicForm(): void
        {
            // Handle POST request (form submission)
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handleFormSubmission();
                return;
            }

            // Display the form with status from flash message (if any)
            $this->displayForm();
        }

        /**
         * Handles form submission using PRG (Post-Redirect-Get) pattern
         */
        private function handleFormSubmission(): void
        {
            $validation = $this->validator->validatePqrsfForm($_POST, $_FILES);
            
            if (!$validation['valid']) {
                $this->flashMessage->error([
                    'success' => false,
                    'message' => implode(' ', $validation['errors'])
                ]);
                $this->redirect($_SERVER['REQUEST_URI']);
                return;
            }
            
            $result = $this->pqrsfService->createPqrsf($validation['data'], $_FILES);
            
            if ($result['success']) {
                $result['formOrigin'] = 'public';
                $this->flashMessage->success($result);
                $this->redirect('/pqrsf/success');
            } else {
                $this->flashMessage->error($result);
                $this->redirect($_SERVER['REQUEST_URI']);
            }
        }

        /**
         * Displays the form with data for select fields
         */
        private function displayForm(): void
        {
            $status = $this->flashMessage->getError();

            $tiposId               = TipoIdPaciente::all();
            $tiposSolicitante      = TipoModel::getTipos('formulario_pqr_tipo_fuente', 'tipo_fuente_id', 'descripcion');
            $serviciosEvento       = TipoModel::getTipos('formulario_pqr_presento_evento', 'id', 'nombre');
            $enfoquesDiferenciales = TipoModel::getTipos('formulario_pqr_tipo_enfoque_diferencial', 'enfoque_diferencial_id', 'descripcion', 'enfoque_diferencial_id');
            $aseguradoras          = AseguradoraModel::all();
            
            $this->renderView('public', [
                'tiposId'               => $tiposId,
                'tiposSolicitante'      => $tiposSolicitante,
                'serviciosEvento'       => $serviciosEvento,
                'enfoquesDiferenciales' => $enfoquesDiferenciales,
                'aseguradoras'          => $aseguradoras,
                'status'                => $status,
            ]);
        }

        /**
         * Displays the success page after PQRSF registration
         */
        public function showSuccess(): void
        {
            $result = $this->flashMessage->getSuccess();
            
            if ($result === null) {
                if (isset($_GET['test'])) {
                    $this->renderSuccessView([
                        'success'      => true,
                        'pqrsfId'      => 12345,
                        'emailSent'    => true,
                        'errorMessage' => null,
                        'formOrigin'   => 'public',
                    ]);
                    return;
                }
                
                $this->redirect('/');
                return;
            }

            $this->renderSuccessView([
                'success'      => $result['success'],
                'pqrsfId'      => $result['pqrsfId']    ?? null,
                'emailSent'    => $result['emailSent']  ?? false,
                'errorMessage' => $result['message']    ?? null,
                'formOrigin'   => $result['formOrigin'] ?? 'public',
            ]);
        }

        /**
         * Displays the insurance form and handles form submission
         */
        public function insuranceForm(): void
        {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handleInsuranceFormSubmission();
                return;
            }

            $this->displayInsuranceForm();
        }

        /**
         * Handles insurance form submission using PRG (Post-Redirect-Get) pattern
         */
        private function handleInsuranceFormSubmission(): void
        {
            $_POST['nombres_peticionario']            = $_POST['nombre_asesor'] ?? '';
            $_POST['telefono_celular_peticionario']   = $_POST['telefono_asesor'] ?? '';
            $_POST['correo_electronico_peticionario'] = $_POST['correo_asesor'] ?? '';
            $_POST['tipo_documento_peticionario']     = '';
            $_POST['documento_peticionario']          = '';
            $_POST['direccion_peticionario']          = '';

            $validation = $this->validator->validatePqrsfForm($_POST, $_FILES);
            
            if (!$validation['valid']) {
                $this->flashMessage->error([
                    'success' => false,
                    'message' => implode(' ', $validation['errors'])
                ]);
                $this->redirect($_SERVER['REQUEST_URI']);
                return;
            }
            
            $result = $this->pqrsfService->createPqrsf($validation['data'], $_FILES, true);
            
            if ($result['success']) {
                $result['formOrigin'] = 'insurance';
                $this->flashMessage->success($result);
                $this->redirect('/pqrsf/success');
            } else {
                $this->flashMessage->error($result);
                $this->redirect($_SERVER['REQUEST_URI']);
            }
        }

        /**
         * Displays the insurance form with data for select fields
         */
        private function displayInsuranceForm(): void
        {
            $status = $this->flashMessage->getError();

            $tiposId               = TipoIdPaciente::all();
            $tiposSolicitante      = TipoModel::getTipos('formulario_pqr_tipo_fuente', 'tipo_fuente_id', 'descripcion');
            $serviciosEvento       = TipoModel::getTipos('formulario_pqr_presento_evento', 'id', 'nombre');
            $enfoquesDiferenciales = TipoModel::getTipos('formulario_pqr_tipo_enfoque_diferencial', 'enfoque_diferencial_id', 'descripcion', 'enfoque_diferencial_id');
            $aseguradoras          = AseguradoraModel::all();
            
            $this->renderView('insurance', [
                'tiposId'               => $tiposId,
                'tiposSolicitante'      => $tiposSolicitante,
                'serviciosEvento'       => $serviciosEvento,
                'enfoquesDiferenciales' => $enfoquesDiferenciales,
                'aseguradoras'          => $aseguradoras,
                'status'                => $status,
            ]);
        }
        
        /**
         * Renders a view with layout
         * 
         * @param string $viewName Name of the view file
         * @param array $data Data to pass to the view
         */
        private function renderView(string $viewName, array $data = []): void
        {
            extract($data);
            require_once __DIR__ . '/../Views/pqrsf/layout.php';
        }

        /**
         * Renders the success view without layout
         * 
         * @param array $data Data to pass to the view
         */
        private function renderSuccessView(array $data = []): void
        {
            extract($data);
            require_once __DIR__ . '/../Views/pqrsf/success.php';
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