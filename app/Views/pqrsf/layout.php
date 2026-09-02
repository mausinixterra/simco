<?php 
// Configuración de variables para el header
$pageTitle = 'Registrar solicitud de PQRSF';
$useChoicesJs = true;

// Include header compartido
require_once __DIR__ . '/../shared/header.php';
?>

<?php 
// Include navbar compartido
require_once __DIR__ . '/../shared/navbar.php';
?>
    <div class="min-h-[calc(100vh-200px)]">
        <main class="container mx-auto px-6 py-6">
            <?php require_once __DIR__ . "/{$viewName}.php"; ?>
        </main>
    </div>

    <?php require_once __DIR__ . '/../shared/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
</body>
</html>