<?php
// services/MailerService.php
// Servicio especializado en el despacho de correos transaccionales para la gestión de turnos y agenda clínica.

require_once __DIR__ . '/EmailService.php';

class MailerService {

    /**
     * Genera la URL para agregar un evento a Google Calendar con un solo clic.
     */
    public static function generarGoogleCalendarUrl($titulo, $fecha, $hora, $duracionMinutos = 45, $detalles = '', $ubicacion = '') {
        $timestampInicio = strtotime($fecha . ' ' . $hora);
        if (!$timestampInicio) {
            $timestampInicio = time();
        }
        $timestampFin = $timestampInicio + ($duracionMinutos * 60);

        // Formato UTC básico Ymd\THis\Z
        $dtStart = gmdate('Ymd\THis\Z', $timestampInicio);
        $dtEnd   = gmdate('Ymd\THis\Z', $timestampFin);

        $params = [
            'action'   => 'TEMPLATE',
            'text'     => $titulo,
            'dates'    => $dtStart . '/' . $dtEnd,
            'details'  => $detalles,
            'location' => $ubicacion,
            'sprop'    => 'website:nutrisaludintegra.com'
        ];

        return 'https://calendar.google.com/calendar/render?' . http_build_query($params);
    }

    /**
     * Limpia un número telefónico para enlace de WhatsApp internacional.
     */
    public static function generarWhatsAppUrl($telefono, $mensaje = '') {
        $telLimpio = preg_replace('/[^0-9]/', '', $telefono ?? '');
        if (empty($telLimpio)) return null;

        // Si empieza con 15 en Argentina (10 dígitos), o no tiene código de país
        if (strlen($telLimpio) === 10 && substr($telLimpio, 0, 2) !== '54') {
            $telLimpio = '549' . $telLimpio;
        } elseif (strlen($telLimpio) === 11 && substr($telLimpio, 0, 2) === '15') {
            $telLimpio = '549' . substr($telLimpio, 2);
        }

        $url = 'https://wa.me/' . $telLimpio;
        if (!empty($mensaje)) {
            $url .= '?text=' . urlencode($mensaje);
        }
        return $url;
    }

    /**
     * Envía confirmación de turno al paciente por correo.
     */
    public static function enviarConfirmacionTurnoPaciente($datosTurno, $paciente, $nutri) {
        $emailPaciente = $paciente['Email'] ?? null;
        if (empty($emailPaciente) || !filter_var($emailPaciente, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $nombrePaciente = trim(($paciente['Nombre'] ?? '') . ' ' . ($paciente['Apellido'] ?? ''));
        $nombreNutri    = trim(($nutri['Nombre'] ?? 'Lic.') . ' ' . ($nutri['Apellido'] ?? 'Nutricionista'));
        $especialidad   = $nutri['Especialidad'] ?? 'Nutricionista Clínico';
        $matricula      = $nutri['Matricula'] ?? '';
        $colorTema      = $nutri['Color_Tema'] ?? '#2ecc71';

        $fecha      = $datosTurno['Fecha'] ?? date('Y-m-d');
        $hora       = substr($datosTurno['Hora'] ?? '09:00', 0, 5);
        $modalidad  = $datosTurno['Modalidad'] ?? 'Presencial';
        $motivo     = $datosTurno['Motivo_Consulta'] ?? 'Consulta Nutricional';
        $link       = $datosTurno['Link_Reunion'] ?? '';
        $direccion  = $datosTurno['Direccion'] ?? ($nutri['Direccion'] ?? 'Consultorio');
        $notas      = $datosTurno['Notas'] ?? '';

        $fechaFormateada = date('d/m/Y', strtotime($fecha));

        $tituloEvento = "Consulta Nutricional con $nombreNutri";
        $ubicacionEvento = ($modalidad === 'Online') ? ($link ?: 'Videollamada Online') : $direccion;
        $detallesEvento = "Turno con $nombreNutri.\nModalidad: $modalidad.\nMotivo: $motivo.\n" . ($modalidad === 'Online' && $link ? "Enlace de reunión: $link\n" : "");

        $gcalUrl = self::generarGoogleCalendarUrl($tituloEvento, $fecha, $hora, 45, $detallesEvento, $ubicacionEvento);
        $wppUrl = !empty($nutri['Whatsapp'] ?? $nutri['Telefono']) ? self::generarWhatsAppUrl($nutri['Whatsapp'] ?? $nutri['Telefono'], "Hola $nombreNutri, te escribo por mi turno del $fechaFormateada a las $hora hs.") : null;

        $asunto = "Confirmación de Turno Nutricional - $fechaFormateada a las $hora hs";

        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>' . htmlspecialchars($asunto) . '</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7f6; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; color: #2c3e50;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: #f4f7f6; padding: 30px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.06); max-width: 600px; width: 100%;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color: ' . htmlspecialchars($colorTema) . '; padding: 35px 30px; text-align: center; color: #ffffff;">
                            <h1 style="margin: 0 0 5px 0; font-size: 26px; font-weight: bold; letter-spacing: 0.5px;">NutriSalud</h1>
                            <p style="margin: 0; font-size: 14px; opacity: 0.95;">Confirmación de Turno Nutricional</p>
                        </td>
                    </tr>

                    <!-- Contenido -->
                    <tr>
                        <td style="padding: 35px 35px 25px 35px;">
                            <h2 style="margin: 0 0 15px 0; color: #1a252f; font-size: 20px;">¡Hola, ' . htmlspecialchars($nombrePaciente) . '! 👋</h2>
                            <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 1.6; color: #555555;">
                                Tu turno ha sido agendado exitosamente. A continuación encontrarás todos los detalles de tu próxima consulta:
                            </p>

                            <!-- Ficha de Turno -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 2px solid #e2e8f0; border-radius: 14px; margin: 20px 0; padding: 22px;">
                                <tr>
                                    <td>
                                        <div style="font-size: 12px; text-transform: uppercase; font-weight: bold; color: ' . htmlspecialchars($colorTema) . '; margin-bottom: 15px; letter-spacing: 0.5px;">
                                            📅 Datos de la Consulta
                                        </div>
                                        <table border="0" cellpadding="8" cellspacing="0" width="100%" style="font-size: 14px;">
                                            <tr>
                                                <td width="35%" style="color: #64748b; font-weight: bold;">Fecha:</td>
                                                <td style="color: #0f172a; font-weight: bold; font-size: 15px;">' . htmlspecialchars($fechaFormateada) . '</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #64748b; font-weight: bold;">Hora:</td>
                                                <td style="color: #0f172a; font-weight: bold; font-size: 15px;">' . htmlspecialchars($hora) . ' hs</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #64748b; font-weight: bold;">Modalidad:</td>
                                                <td style="color: #0f172a;">
                                                    <span style="display: inline-block; background-color: ' . ($modalidad === 'Online' ? '#e0f2fe' : '#ecfdf5') . '; color: ' . ($modalidad === 'Online' ? '#0369a1' : '#047857') . '; padding: 3px 10px; border-radius: 20px; font-weight: bold; font-size: 12px;">' . htmlspecialchars($modalidad) . '</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #64748b; font-weight: bold;">Motivo:</td>
                                                <td style="color: #334155;">' . htmlspecialchars($motivo) . '</td>
                                            </tr>';

        if ($modalidad === 'Online' && !empty($link)) {
            $html .= '                      <tr>
                                                <td style="color: #64748b; font-weight: bold;">Enlace de Reunión:</td>
                                                <td><a href="' . htmlspecialchars($link) . '" target="_blank" style="color: ' . htmlspecialchars($colorTema) . '; font-weight: bold; word-break: break-all;">' . htmlspecialchars($link) . '</a></td>
                                            </tr>';
        } elseif ($modalidad === 'Presencial' && !empty($direccion)) {
            $html .= '                      <tr>
                                                <td style="color: #64748b; font-weight: bold;">Dirección:</td>
                                                <td style="color: #334155;">' . htmlspecialchars($direccion) . '</td>
                                            </tr>';
        }

        if (!empty($notas)) {
            $html .= '                      <tr>
                                                <td style="color: #64748b; font-weight: bold;">Indicaciones:</td>
                                                <td style="color: #64748b; font-style: italic;">' . nl2br(htmlspecialchars($notas)) . '</td>
                                            </tr>';
        }

        $html .= '                      </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Botones de Acción -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0 15px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="' . htmlspecialchars($gcalUrl) . '" target="_blank" style="background-color: #4285F4; color: #ffffff; text-decoration: none; padding: 13px 28px; border-radius: 50px; font-weight: bold; font-size: 14px; display: inline-block; margin-bottom: 10px; box-shadow: 0 4px 12px rgba(66, 133, 244, 0.3);">
                                            📅 Añadir a Google Calendar
                                        </a>';

        if ($wppUrl) {
            $html .= '                  <br>
                                        <a href="' . htmlspecialchars($wppUrl) . '" target="_blank" style="background-color: #25D366; color: #ffffff; text-decoration: none; padding: 11px 24px; border-radius: 50px; font-weight: bold; font-size: 13px; display: inline-block; box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);">
                                            💬 Contactar al Nutricionista por WhatsApp
                                        </a>';
        }

        $html .= '                  </td>
                                </tr>
                            </table>

                            <!-- Ficha Profesional -->
                            <div style="background-color: #f1f5f9; border-radius: 10px; padding: 15px 20px; margin-top: 25px;">
                                <div style="font-size: 11px; text-transform: uppercase; font-weight: bold; color: #64748b;">Tu Profesional:</div>
                                <div style="font-size: 15px; font-weight: bold; color: #1e293b; margin-top: 2px;">' . htmlspecialchars($nombreNutri) . '</div>
                                <div style="font-size: 13px; color: #475569;">' . htmlspecialchars($especialidad) . ($matricula ? ' • M.P. ' . htmlspecialchars($matricula) : '') . '</div>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 30px; text-align: center; font-size: 12px; color: #94a3b8;">
                            <p style="margin: 0 0 5px 0;">Recordatorio automático enviado desde el Consultorio Nutricional NutriSalud.</p>
                            <p style="margin: 0;">Si necesitas reprogramar o cancelar tu turno, por favor infórmalo con al menos 24 hs de anticipación.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

        return EmailService::enviar($emailPaciente, $asunto, $html, $nutri['Email'] ?? null);
    }

    /**
     * Envía una notificación por correo al nutricionista cuando se agenda o solicita un turno.
     */
    public static function enviarNotificacionTurnoNutricionista($datosTurno, $paciente, $nutri) {
        $emailNutri = $nutri['Email'] ?? null;
        if (empty($emailNutri) || !filter_var($emailNutri, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $nombrePaciente = trim(($paciente['Nombre'] ?? '') . ' ' . ($paciente['Apellido'] ?? ''));
        $nombreNutri    = trim(($nutri['Nombre'] ?? 'Lic.') . ' ' . ($nutri['Apellido'] ?? ''));
        $colorTema      = $nutri['Color_Tema'] ?? '#2ecc71';

        $fecha      = $datosTurno['Fecha'] ?? date('Y-m-d');
        $hora       = substr($datosTurno['Hora'] ?? '09:00', 0, 5);
        $modalidad  = $datosTurno['Modalidad'] ?? 'Presencial';
        $motivo     = $datosTurno['Motivo_Consulta'] ?? 'Consulta Nutricional';
        $estado     = $datosTurno['Estado_Turno'] ?? 'Pendiente';

        $fechaFormateada = date('d/m/Y', strtotime($fecha));
        $asunto = "Nuevo Turno Agendado: $nombrePaciente - $fechaFormateada $hora hs";

        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>' . htmlspecialchars($asunto) . '</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7f6; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; color: #2c3e50;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: #f4f7f6; padding: 30px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.06); max-width: 600px; width: 100%;">
                    
                    <tr>
                        <td style="background-color: #1e293b; padding: 30px; text-align: center; color: #ffffff;">
                            <h1 style="margin: 0 0 5px 0; font-size: 24px; font-weight: bold;">NutriSalud Pro</h1>
                            <p style="margin: 0; font-size: 13px; opacity: 0.85;">Notificación de Agenda Clínica</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 35px 35px 25px 35px;">
                            <h2 style="margin: 0 0 15px 0; color: #0f172a; font-size: 19px;">Hola ' . htmlspecialchars($nombreNutri) . ',</h2>
                            <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 1.6; color: #475569;">
                                Se ha registrado un nuevo turno en tu agenda para el paciente <strong>' . htmlspecialchars($nombrePaciente) . '</strong>.
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; margin: 15px 0; padding: 18px;">
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="6" cellspacing="0" width="100%" style="font-size: 14px;">
                                            <tr>
                                                <td width="35%" style="color: #64748b; font-weight: bold;">Paciente:</td>
                                                <td style="color: #0f172a; font-weight: bold;">' . htmlspecialchars($nombrePaciente) . '</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #64748b; font-weight: bold;">DNI:</td>
                                                <td style="color: #0f172a;">' . htmlspecialchars($paciente['DNI'] ?? 'S/D') . '</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #64748b; font-weight: bold;">Teléfono:</td>
                                                <td style="color: #0f172a;">' . htmlspecialchars($paciente['Telefono'] ?? 'S/D') . '</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #64748b; font-weight: bold;">Fecha y Hora:</td>
                                                <td style="color: #0f172a; font-weight: bold;">' . htmlspecialchars($fechaFormateada) . ' - ' . htmlspecialchars($hora) . ' hs</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #64748b; font-weight: bold;">Modalidad:</td>
                                                <td style="color: #0f172a;">' . htmlspecialchars($modalidad) . '</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #64748b; font-weight: bold;">Motivo:</td>
                                                <td style="color: #0f172a;">' . htmlspecialchars($motivo) . '</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #64748b; font-weight: bold;">Estado:</td>
                                                <td style="color: #0f172a; font-weight: bold;">' . htmlspecialchars($estado) . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 25px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="' . (defined('BASE_URL') ? BASE_URL : '') . 'index.php?action=listar_turnos" target="_blank" style="background-color: ' . htmlspecialchars($colorTema) . '; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 50px; font-weight: bold; font-size: 14px; display: inline-block;">
                                            Ver Agenda en NutriSalud &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 18px 30px; text-align: center; font-size: 12px; color: #94a3b8;">
                            &copy; ' . date('Y') . ' NutriSalud SaaS - Gestión Clínica Profesional
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

        return EmailService::enviar($emailNutri, $asunto, $html);
    }

    /**
     * Notifica la reprogramación de un turno al paciente.
     */
    public static function enviarReprogramacionTurno($datosTurno, $paciente, $nutri, $fechaAnterior = '', $horaAnterior = '') {
        $emailPaciente = $paciente['Email'] ?? null;
        if (empty($emailPaciente) || !filter_var($emailPaciente, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $nombrePaciente = trim(($paciente['Nombre'] ?? '') . ' ' . ($paciente['Apellido'] ?? ''));
        $nombreNutri    = trim(($nutri['Nombre'] ?? 'Lic.') . ' ' . ($nutri['Apellido'] ?? 'Nutricionista'));
        $colorTema      = $nutri['Color_Tema'] ?? '#2ecc71';

        $fechaNueva = $datosTurno['Fecha'] ?? date('Y-m-d');
        $horaNueva  = substr($datosTurno['Hora'] ?? '09:00', 0, 5);
        $modalidad  = $datosTurno['Modalidad'] ?? 'Presencial';
        $motivo     = $datosTurno['Motivo_Consulta'] ?? 'Consulta Nutricional';
        $link       = $datosTurno['Link_Reunion'] ?? '';
        $direccion  = $datosTurno['Direccion'] ?? ($nutri['Direccion'] ?? 'Consultorio');

        $fechaNuevaFmt = date('d/m/Y', strtotime($fechaNueva));
        $fechaAntFmt   = !empty($fechaAnterior) ? date('d/m/Y', strtotime($fechaAnterior)) : '';
        $horaAntFmt    = !empty($horaAnterior) ? substr($horaAnterior, 0, 5) : '';

        $asunto = "Reprogramación de Turno Nutricional - Nueva Fecha: $fechaNuevaFmt $horaNueva hs";

        $tituloEvento = "Consulta Nutricional con $nombreNutri (Reprogramada)";
        $ubicacionEvento = ($modalidad === 'Online') ? ($link ?: 'Videollamada Online') : $direccion;
        $detallesEvento = "Turno reprogramado con $nombreNutri.\nModalidad: $modalidad.\nMotivo: $motivo.";
        $gcalUrl = self::generarGoogleCalendarUrl($tituloEvento, $fechaNueva, $horaNueva, 45, $detallesEvento, $ubicacionEvento);

        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>' . htmlspecialchars($asunto) . '</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7f6; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; color: #2c3e50;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: #f4f7f6; padding: 30px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.06); max-width: 600px; width: 100%;">
                    
                    <tr>
                        <td style="background-color: #f59e0b; padding: 30px; text-align: center; color: #ffffff;">
                            <h1 style="margin: 0 0 5px 0; font-size: 24px; font-weight: bold;">Turno Reprogramado</h1>
                            <p style="margin: 0; font-size: 14px; opacity: 0.95;">NutriSalud - ' . htmlspecialchars($nombreNutri) . '</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 35px 35px 25px 35px;">
                            <h2 style="margin: 0 0 15px 0; color: #1a252f; font-size: 20px;">Estimado/a ' . htmlspecialchars($nombrePaciente) . ',</h2>
                            <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 1.6; color: #555555;">
                                Te informamos que tu turno ha sido reprogramado. A continuación te presentamos el nuevo horario de atención:
                            </p>';

        if (!empty($fechaAntFmt)) {
            $html .= '      <div style="background-color: #fee2e2; border-left: 4px solid #ef4444; padding: 12px 16px; margin-bottom: 20px; border-radius: 6px; font-size: 14px; color: #991b1b;">
                                <strike>Horario anterior: ' . htmlspecialchars($fechaAntFmt) . ' a las ' . htmlspecialchars($horaAntFmt) . ' hs</strike>
                            </div>';
        }

        $html .= '          <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ecfdf5; border: 2px solid #a7f3d0; border-radius: 12px; margin: 15px 0; padding: 20px;">
                                <tr>
                                    <td>
                                        <div style="font-size: 12px; text-transform: uppercase; font-weight: bold; color: #047857; margin-bottom: 12px;">
                                            ✅ NUEVO HORARIO CONFIRMADO
                                        </div>
                                        <table border="0" cellpadding="6" cellspacing="0" width="100%" style="font-size: 14px;">
                                            <tr>
                                                <td width="35%" style="color: #065f46; font-weight: bold;">Nueva Fecha:</td>
                                                <td style="color: #064e3b; font-weight: bold; font-size: 16px;">' . htmlspecialchars($fechaNuevaFmt) . '</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #065f46; font-weight: bold;">Nueva Hora:</td>
                                                <td style="color: #064e3b; font-weight: bold; font-size: 16px;">' . htmlspecialchars($horaNueva) . ' hs</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #065f46; font-weight: bold;">Modalidad:</td>
                                                <td style="color: #064e3b; font-weight: bold;">' . htmlspecialchars($modalidad) . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 25px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="' . htmlspecialchars($gcalUrl) . '" target="_blank" style="background-color: #4285F4; color: #ffffff; text-decoration: none; padding: 13px 28px; border-radius: 50px; font-weight: bold; font-size: 14px; display: inline-block;">
                                            📅 Actualizar en Google Calendar
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 18px 30px; text-align: center; font-size: 12px; color: #94a3b8;">
                            Si este nuevo horario no se ajusta a tus posibilidades, contáctate directamente con tu profesional.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

        return EmailService::enviar($emailPaciente, $asunto, $html, $nutri['Email'] ?? null);
    }

    /**
     * Notifica la cancelación de un turno al paciente.
     */
    public static function enviarCancelacionTurno($datosTurno, $paciente, $nutri, $motivoCancelacion = '') {
        $emailPaciente = $paciente['Email'] ?? null;
        if (empty($emailPaciente) || !filter_var($emailPaciente, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $nombrePaciente = trim(($paciente['Nombre'] ?? '') . ' ' . ($paciente['Apellido'] ?? ''));
        $nombreNutri    = trim(($nutri['Nombre'] ?? 'Lic.') . ' ' . ($nutri['Apellido'] ?? 'Nutricionista'));

        $fecha = date('d/m/Y', strtotime($datosTurno['Fecha'] ?? date('Y-m-d')));
        $hora  = substr($datosTurno['Hora'] ?? '09:00', 0, 5);

        $asunto = "Cancelación de Turno Nutricional - $fecha $hora hs";

        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>' . htmlspecialchars($asunto) . '</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7f6; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; color: #2c3e50;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: #f4f7f6; padding: 30px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.06); max-width: 600px; width: 100%;">
                    
                    <tr>
                        <td style="background-color: #ef4444; padding: 30px; text-align: center; color: #ffffff;">
                            <h1 style="margin: 0 0 5px 0; font-size: 24px; font-weight: bold;">Turno Cancelado</h1>
                            <p style="margin: 0; font-size: 13px; opacity: 0.95;">NutriSalud - ' . htmlspecialchars($nombreNutri) . '</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 35px 35px 25px 35px;">
                            <h2 style="margin: 0 0 15px 0; color: #1a252f; font-size: 20px;">Estimado/a ' . htmlspecialchars($nombrePaciente) . ',</h2>
                            <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 1.6; color: #555555;">
                                Te comunicamos que la consulta programada para el día <strong>' . htmlspecialchars($fecha) . ' a las ' . htmlspecialchars($hora) . ' hs</strong> ha sido cancelada.
                            </p>';

        if (!empty($motivoCancelacion)) {
            $html .= '      <div style="background-color: #f8fafc; border-left: 4px solid #64748b; padding: 12px 16px; margin: 15px 0; font-size: 14px; color: #475569;">
                                <strong>Motivo:</strong> ' . htmlspecialchars($motivoCancelacion) . '
                            </div>';
        }

        $html .= '          <p style="margin: 20px 0; font-size: 14px; color: #555555;">
                                Puedes solicitar un nuevo turno ingresando a tu portal de paciente o comunicándote directamente con el profesional.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 18px 30px; text-align: center; font-size: 12px; color: #94a3b8;">
                            &copy; ' . date('Y') . ' NutriSalud SaaS
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

        return EmailService::enviar($emailPaciente, $asunto, $html, $nutri['Email'] ?? null);
    }
}
