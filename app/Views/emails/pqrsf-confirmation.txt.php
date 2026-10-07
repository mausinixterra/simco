<?php use App\Core\Lang; ?>========================================
CLÍNICA DE OCCIDENTE
<?= Lang::get('email_header_subtitle') ?>
========================================

<?= Lang::get('email_confirmation_greeting') ?>,
<?= htmlspecialchars($nombrePaciente, ENT_QUOTES, 'UTF-8') ?>


<?= Lang::get('email_confirmation_intro') ?>

========================================
<?= Lang::get('email_radicado_number') ?>
========================================

PQRSF <?= $pqrsfId ?>


<?= Lang::get('patient_full_name') ?>: <?= htmlspecialchars($nombrePaciente, ENT_QUOTES, 'UTF-8') ?>

<?= Lang::get('email_registration_date') ?>: <?= $fecha ?>

<?= Lang::get('email_applicant_type') ?>: <?= htmlspecialchars($tipoSolicitante, ENT_QUOTES, 'UTF-8') ?>


----------------------------------------
<?= Lang::get('email_manifestation_details') ?>
----------------------------------------
<?= $observacion ?>


<?php if (!empty($attachedFiles)): ?>
----------------------------------------
<?= Lang::get('file_list_title') ?>
----------------------------------------
<?php foreach ($attachedFiles as $file): ?>
- <?= $file['name'] ?? basename($file['path']) ?>

<?php endforeach; ?>

<?= Lang::get('email_attachments_included_plain') ?>
<?php endif; ?>

========================================

** <?= Lang::get('email_law_1755_notice') ?> **

========================================
<?= mb_strtoupper(Lang::get('email_contact_channels_title'), 'UTF-8') ?>
========================================

<?= Lang::get('email_contact_phone_label') ?> 6603000 Ext. 753
<?= Lang::get('email_contact_hours_weekday') ?>
<?= Lang::get('email_contact_hours_weekend') ?>

<?= Lang::get('email_contact_whatsapp_label') ?> 321 722 8389
<?= Lang::get('email_contact_whatsapp_hours') ?>

<?= Lang::get('email_contact_web_label') ?> www.clinicadeoccidente.com

========================================

<?= Lang::get('email_automatic_message') ?>

<?= Lang::get('email_use_official_channels') ?>

© <?= date('Y') ?> Clínica de Occidente - <?= Lang::get('email_all_rights_reserved') ?>
