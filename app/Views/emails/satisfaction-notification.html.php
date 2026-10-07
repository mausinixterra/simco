<?php use App\Core\Lang; ?><!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9fafb;
        }
        .header {
            background-color: #1e3a8a;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: white;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .info-box {
            background-color: #f0f4ff;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #1e3a8a;
            border-radius: 4px;
        }
        .conformidad-si {
            color: #16a34a;
            font-weight: bold;
        }
        .conformidad-no {
            color: #dc2626;
            font-weight: bold;
        }
        .motivo-box {
            background-color: #fef2f2;
            padding: 15px;
            border-left: 4px solid #dc2626;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><?= Lang::get('email_satisfaction_title') ?></h2>
            <p>PQRSF #<?= $pqrsfId ?></p>
        </div>
        <div class="content">
            <p><?= Lang::get('email_satisfaction_registered_for') ?> <strong>#<?= $pqrsfId ?></strong></p>
            
            <div class="info-box">
                <p><strong><?= Lang::get('email_satisfaction_response_date') ?></strong> <?= $fecha ?></p>
                <p><strong><?= Lang::get('email_satisfaction_conforme_label') ?></strong> <span class="conformidad-<?= $conformidad ?>"><?= $conformidadText ?></span></p>
                
                <?php if ($conformidad === 'no' && !empty($motivo)): ?>
                <p><strong><?= Lang::get('email_satisfaction_reason_label') ?></strong></p>
                <div class="motivo-box">
                    <?= nl2br(htmlspecialchars($motivo, ENT_QUOTES, 'UTF-8')) ?>
                </div>
                <?php endif; ?>
            </div>
            
            <p style="margin-top: 30px; font-size: 12px; color: #6b7280;">
                <?= Lang::get('email_satisfaction_auto_review') ?>
            </p>
        </div>
    </div>
</body>
</html>
