<?php use App\Core\Lang; ?><?= Lang::get('email_satisfaction_title') ?> - PQRSF #<?= $pqrsfId ?>


<?= Lang::get('email_satisfaction_registered_for') ?> #<?= $pqrsfId ?>


<?= Lang::get('email_satisfaction_date_short') ?> <?= $fecha ?>

<?= Lang::get('email_satisfaction_conforme_short') ?> <?= $conformidadText ?>


<?php if ($conformidad === 'no' && !empty($motivo)): ?>
<?= Lang::get('email_satisfaction_reason_label') ?>
<?= $motivo ?>


<?php endif; ?>
========================================

<?= Lang::get('email_satisfaction_auto_short') ?>
