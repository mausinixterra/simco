<?php use App\Core\Lang; ?>========================================
CLÍNICA DE OCCIDENTE
<?= Lang::get('lang_es') === 'Español' ? 'Sistema de PQRSF' : 'PQRSF System' ?>
========================================

<?= Lang::get('email_confirmation_greeting') ?>,
<?= htmlspecialchars($nombrePaciente) ?>


<?= Lang::get('email_confirmation_intro') ?>

========================================
<?= Lang::get('email_radicado_number') ?>
========================================

PQRSF <?= $pqrsfId ?>


<?= Lang::get('patient_full_name') ?>: <?= htmlspecialchars($nombrePaciente) ?>

<?= Lang::get('email_registration_date') ?>: <?= $fecha ?>

<?= Lang::get('email_applicant_type') ?>: <?= htmlspecialchars($tipoSolicitante) ?>


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

<?= Lang::get('lang_es') === 'Español' ? 'Los archivos adjuntos han sido incluidos en este correo.' : 'The attached files have been included in this email.' ?>
<?php endif; ?>

========================================

** <?= Lang::get('lang_es') === 'Español' ? 'SU SOLICITUD SERÁ RESPONDIDA DE ACUERDO A LO ESTABLECIDO EN LA LEY 1755 DE 2015' : 'YOUR REQUEST WILL BE ANSWERED IN ACCORDANCE WITH LAW 1755 OF 2015' ?> **

========================================
<?= Lang::get('lang_es') === 'Español' ? 'CANALES DE ATENCIÓN' : 'CONTACT CHANNELS' ?>
========================================

<?= Lang::get('lang_es') === 'Español' ? 'Atención telefónica:' : 'Phone Support:' ?> 6603000 Ext. 753
<?= Lang::get('lang_es') === 'Español' ? 'Lunes a sábado de 8:00am a 5:00pm' : 'Monday to Saturday from 8:00am to 5:00pm' ?>
<?= Lang::get('lang_es') === 'Español' ? 'Domingos y festivos de 7:00am a 4:00pm' : 'Sundays and holidays from 7:00am to 4:00pm' ?>

<?= Lang::get('lang_es') === 'Español' ? 'Línea WhatsApp:' : 'WhatsApp:' ?> 321 722 8389
<?= Lang::get('lang_es') === 'Español' ? 'Lunes a domingo' : 'Monday to Sunday' ?>

<?= Lang::get('lang_es') === 'Español' ? 'Web:' : 'Website:' ?> www.clinicadeoccidente.com

========================================

<?= Lang::get('email_automatic_message') ?>

<?= Lang::get('lang_es') === 'Español' ? 'Frente a cualquier solicitud, le invitamos hacer uso de los canales habilitados por la Clínica de Occidente.' : 'For any request, we invite you to use the channels enabled by Clínica de Occidente.' ?>

© <?= date('Y') ?> Clínica de Occidente - <?= Lang::get('email_all_rights_reserved') ?>
