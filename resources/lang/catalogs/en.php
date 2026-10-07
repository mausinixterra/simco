<?php

declare(strict_types=1);

/**
 * Diccionario de mapeo de catálogos de la base de datos al inglés.
 *
 * Estructura: [nombre_de_tabla => [id => traducción]].
 * Se indexa por ID (no por el texto en español) porque el texto lo administra otro
 * equipo y puede cambiar. Si un ID no está aquí, se muestra el texto de la base.
 * Al agregar un registro a un catálogo en la base, agrega aquí su traducción.
 *
 * Las aseguradoras (planes + terceros) no se traducen: son nombres propios.
 */
return [
    // tipos_id_pacientes (HIS) — tipo_id_paciente => descripción
    'tipos_id_pacientes' => [
        'AS'  => 'Unidentified adult',
        'CD'  => 'Diplomatic ID card',
        'CC'  => 'Citizenship ID card',
        'CE'  => 'Foreign resident ID card',
        'CN'  => 'Live birth certificate',
        'DE'  => 'Foreign document',
        'MS'  => 'Unidentified minor',
        'NV'  => 'Live birth record',
        'NIT' => 'Tax ID number (NIT)',
        'NU'  => 'Unique identification number',
        'PA'  => 'Passport',
        'PE'  => 'Special stay permit',
        'PTP' => 'Temporary stay permit (PTP)',
        'PT'  => 'Temporary stay permit (PT)',
        'RC'  => 'Civil birth registration',
        'RE'  => 'Special resident for peace',
        'SC'  => 'Safe-conduct stay permit',
        'TI'  => 'Identity card (minors)',
    ],

    // formulario_pqr_tipo_fuente — tipo_fuente_id => descripcion
    'formulario_pqr_tipo_fuente' => [
        '1'  => 'User (patient)',
        '2'  => 'Family member',
        '3'  => 'Insurance company',
        '4'  => 'Oversight body',
        '5'  => 'Insurers database',
        '6'  => 'Oversight body - Health secretariat',
        '7'  => 'Oversight body - Ombudsman\'s office',
        '8'  => 'SURA PIP - Prostate cancer',
        '9'  => 'Ethics hotline',
        '10' => 'SURA legal action (tutela) app',
        '11' => 'SURA app',
        '12' => 'Patients\' association',
        '13' => 'Companies',
    ],

    // formulario_pqr_presento_evento — id => nombre
    'formulario_pqr_presento_evento' => [
        '1'  => 'Surgery',
        '2'  => 'Outpatient clinic',
        '3'  => 'Comprehensive cancer center',
        '4'  => 'Rehabilitation',
        '5'  => 'Emergency department',
        '6'  => 'Hospitalization',
        '7'  => 'Call center',
        '8'  => 'Voluntary and private plans',
        '9'  => 'Medical billing audit',
        '10' => 'Marketing',
        '11' => 'Document management',
        '12' => 'Infrastructure',
        '13' => 'Maintenance',
        '14' => 'Operations management',
        '15' => 'Delivery room',
        '16' => 'Laboratory',
        '17' => 'In-house diagnostic imaging',
        '18' => 'External diagnostic imaging',
        '19' => 'Nuclear medicine',
        '20' => 'Palliative care',
        '21' => 'Cardioccidente',
        '22' => 'Angiography',
        '23' => 'ICU',
        '24' => 'NICU - Pediatrics',
        '25' => 'Customer service',
        '34' => 'Endoscopy',
        '35' => 'Delay in care by general practitioner/specialist',
        '36' => 'Request for a medical second opinion',
        '37' => 'Minor procedure appointment request',
        '38' => 'Transplant unit',
        '39' => 'International business',
        '40' => 'Catheter clinic',
    ],

    // formulario_pqr_tipo_enfoque_diferencial — enfoque_diferencial_id => descripcion
    'formulario_pqr_tipo_enfoque_diferencial' => [
        '1'  => 'Person with a disability',
        '2'  => 'Older adult (60 years or older)',
        '3'  => 'Child or adolescent (0 to 17 years)',
        '4'  => 'Pregnant person',
        '5'  => 'Victim of the armed conflict',
        '6'  => 'Migrant population',
        '7'  => 'Indigenous community',
        '8'  => 'Afro-Colombian, Black, Raizal or Palenquero community',
        '9'  => 'Rrom (Roma) people',
        '10' => 'Person deprived of liberty',
        '11' => 'Homeless person',
        '12' => 'Other',
        '13' => 'None of the above',
    ],
];
