<?php use App\Core\Lang; ?><!DOCTYPE html>
<html lang="<?php echo Lang::get('lang_es') === 'Español' ? 'es' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .email-container {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #1e3a8a;
            color: white;
            padding: 12px 20px 16px;
            text-align: center;
        }

        .header-logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            width: 100%;
            margin: 0 auto 10px;
        }

        .header-logos img {
            max-height: 55px;
            width: auto;
            display: block;
        }

        .header h1 {
            margin: 3px 0;
            font-size: 23px;
            font-weight: 600;
        }

        .header p {
            margin: 2px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #f0f4ff;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #1e3a8a;
            border-radius: 4px;
        }
        .info-box p {
            margin: 10px 0;
        }
        .info-box strong {
            color: #1e3a8a;
        }
        .radicado {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
            display: inline-block;
            padding: 8px 15px;
            background-color: #fef3c7;
            border-radius: 4px;
        }
        .important-notice {
            background-color: #fef3c7;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #f59e0b;
            border-radius: 4px;
            text-align: center;
        }
        .important-notice strong {
            color: #92400e;
            font-size: 14px;
        }
        .contact-info {
            background-color: #f9fafb;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
            text-align: center;
        }
        .contact-info h3 {
            color: #1e3a8a;
            margin-top: 0;
            font-size: 16px;
        }
        .contact-info p {
            margin: 8px 0;
            font-size: 13px;
            line-height: 1.8;
        }
        .contact-info strong {
            color: #1e3a8a;
        }
        .footer {
            background-color: #1e3a8a;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 12px;
        }
        .footer p {
            margin: 8px 0;
            opacity: 0.9;
        }
        @media only screen and (max-width: 600px) {
            .header-logos {
                flex-direction: column;
                gap: 10px;
            }
            .header-logos img {
                max-height: 50px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="header-logos">
                <!-- <img src="cid:logo_cdo_blanco" alt="Clínica de Occidente" style="max-height: 60px;"> -->
                <img src="cid:logo_header" alt="SIMCO" style="max-height: 60px;">
            </div>
        </div>
        
        <div class="content">
            <p class="greeting"><?= Lang::get('email_confirmation_greeting') ?>,<br><strong><?= htmlspecialchars($nombrePaciente) ?></strong></p>
            
            <p><strong><?= Lang::get('email_confirmation_intro') ?></strong></p>
            
            <div class="info-box" style="text-align: center;">
                <h3 style="color: #1e3a8a; margin-top: 0;"><?= Lang::get('email_radicado_number') ?></h3>
                <span class="radicado"><?= $pqrsfId ?></span>
            </div>
            
            <div class="info-box">
                <!-- <p style="text-align: center; margin-bottom: 15px;">
                    <h3 style="color: #1e3a8a; margin-top: 0;"><?= Lang::get('email_radicado_number') ?></h3>
                    <span class="radicado"><?= $pqrsfId ?></span>
                </p> -->
                <p><strong><?= Lang::get('patient_full_name') ?>:</strong> <?= htmlspecialchars($nombrePaciente) ?></p>
                <p><strong><?= Lang::get('email_registration_date') ?>:</strong> <?= $fecha ?></p>
                <p><strong><?= Lang::get('email_applicant_type') ?>:</strong> <?= htmlspecialchars($tipoSolicitante) ?></p>
            </div>
            
            <div class="info-box">
                <p><strong><?= Lang::get('email_manifestation_details') ?>:</strong></p>
                <p style="margin-top: 10px; line-height: 1.8;"><?= nl2br(htmlspecialchars($observacion)) ?></p>
            </div>
            
            <?php if (!empty($attachedFiles)): ?>
            <div class="info-box">
                <p><strong><?= Lang::get('file_list_title') ?></strong></p>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <?php foreach ($attachedFiles as $file): ?>
                    <li><?= htmlspecialchars($file['name'] ?? basename($file['path'])) ?></li>
                    <?php endforeach; ?>
                </ul>
                <p style="font-size: 12px; color: #6b7280;">
                    <?php 
                    if (Lang::get('lang_es') === 'Español') {
                        echo 'Los archivos adjuntos han sido incluidos en este correo para su referencia.';
                    } else {
                        echo 'The attached files have been included in this email for your reference.';
                    }
                    ?>
                </p>
            </div>
            <?php endif; ?>
            
            <div class="important-notice">
                <strong><?php 
                if (Lang::get('lang_es') === 'Español') {
                    echo 'SU SOLICITUD SERÁ RESPONDIDA DE ACUERDO A LO ESTABLECIDO EN LA LEY 1755 DE 2015';
                } else {
                    echo 'YOUR REQUEST WILL BE ANSWERED IN ACCORDANCE WITH LAW 1755 OF 2015';
                }
                ?></strong>
            </div>
            
            <div class="contact-info">
                <h3><?php 
                if (Lang::get('lang_es') === 'Español') {
                    echo 'Canales de Atención';
                } else {
                    echo 'Contact Channels';
                }
                ?></h3>
                <p><strong><?php 
                if (Lang::get('lang_es') === 'Español') {
                    echo 'Atención telefónica:';
                } else {
                    echo 'Phone Support:';
                }
                ?></strong> 6603000 Ext. 753<br>
                <strong><?php 
                if (Lang::get('lang_es') === 'Español') {
                    echo 'Horario:';
                } else {
                    echo 'Hours:';
                }
                ?></strong> <?php 
                if (Lang::get('lang_es') === 'Español') {
                    echo 'Lunes a sábado de 8:00am a 5:00pm<br>Domingos y festivos de 7:00am a 4:00pm';
                } else {
                    echo 'Monday to Saturday from 8:00am to 5:00pm<br>Sundays and holidays from 7:00am to 4:00pm';
                }
                ?></p>
                <p><strong><?php 
                if (Lang::get('lang_es') === 'Español') {
                    echo 'Línea WhatsApp:';
                } else {
                    echo 'WhatsApp:';
                }
                ?></strong><a href="https://wa.me/573217228389" target="_blank" rel="noopener noreferrer" class="cursor-pointer text-blue-600 no-underline hover:underline"> 321 722 8389 </a><br><?php 
                if (Lang::get('lang_es') === 'Español') {
                    echo 'Lunes a domingo';
                } else {
                    echo 'Monday to Sunday';
                }
                ?></p>
                <p><strong><?php 
                if (Lang::get('lang_es') === 'Español') {
                    echo 'Web:';
                } else {
                    echo 'Website:';
                }
                ?></strong> <a href="https://www.clinicadeoccidente.com" target="_blank" rel="noopener noreferrer" class="cursor-pointer text-blue-600 no-underline hover:underline">www.clinicadeoccidente.com</a></p>
            </div>
        </div>
        
        <div class="footer">
            <strong><?php 
            if (Lang::get('lang_es') === 'Español') {
                echo 'Este mensaje es generado automáticamente, por favor no lo responda.';
            } else {
                echo 'This message is automatically generated, please do not reply.';
            }
            ?></strong><br>
            <?php 
            if (Lang::get('lang_es') === 'Español') {
                echo 'Frente a cualquier solicitud, le invitamos hacer uso de los canales habilitados por la Clínica de Occidente.';
            } else {
                echo 'For any request, we invite you to use the channels enabled by Clínica de Occidente.';
            }
            ?><br>
            <span style="margin-top: 15px;">&copy; <?= date('Y') ?> Clínica de Occidente - <?php 
            if (Lang::get('lang_es') === 'Español') {
                echo 'Todos los derechos reservados';
            } else {
                echo 'All rights reserved';
            }
            ?></span>
        </div>
    </div>
</body>
</html>
