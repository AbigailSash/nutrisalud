<?php
// services/EmailService.php
// Servicio institucional para el envío de correos electrónicos transaccionales vía SMTP Socket nativo / PHP mail.

class EmailService {

    /**
     * Envía un correo de bienvenida con las credenciales de acceso al paciente recién registrado.
     */
    public static function enviarCredencialesAltaPaciente($email, $nombre, $apellido, $usuario, $password, $nutri = []) {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $nombreCompleto = trim($nombre . ' ' . $apellido);
        $nombreNutri = trim(($nutri['Nombre'] ?? 'Lic.') . ' ' . ($nutri['Apellido'] ?? 'en Nutrición'));
        $especialidadNutri = $nutri['Especialidad'] ?? 'Nutricionista Clínico';
        $matriculaNutri = $nutri['Matricula'] ?? '';
        $colorTema = $nutri['Color_Tema'] ?? '#2ecc71';
        $loginUrl = defined('BASE_URL') ? BASE_URL . 'index.php?action=login_paciente' : 'index.php?action=login_paciente';

        $asunto = "¡Bienvenido/a a NutriSalud! - Tus credenciales de acceso";

        $cuerpoHTML = self::generarPlantillaAltaPaciente(
            $nombreCompleto,
            $usuario,
            $password,
            $loginUrl,
            $nombreNutri,
            $especialidadNutri,
            $matriculaNutri,
            $colorTema,
            $nutri
        );

        return self::enviar($email, $asunto, $cuerpoHTML, $nutri['Email'] ?? null);
    }

    /**
     * Envía un correo con el enlace criptográfico seguro para restablecer la contraseña.
     */
    public static function enviarRecuperacionPassword($email, $nombre, $resetUrl, $tipoUsuario = 'nutricionista', $minutos = 30, $colorTema = '#2ecc71') {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $asunto = "Recuperación de Contraseña - NutriSalud";
        $cuerpoHTML = self::generarPlantillaRecuperacionPassword($nombre, $resetUrl, $tipoUsuario, $minutos, $colorTema);

        return self::enviar($email, $asunto, $cuerpoHTML);
    }

    /**
     * Motor central de envío: Intenta primero vía SMTP si está configurado, o fallback a mail() nativo.
     */
    public static function enviar($destinatario, $asunto, $cuerpoHTML, $replyTo = null) {
        $configFile = __DIR__ . '/../config/email.php';
        $config = file_exists($configFile) ? require($configFile) : [];

        $smtpEnabled = !empty($config['smtp_enabled']) && !empty($config['smtp_username']) && !empty($config['smtp_password']);

        $enviado = false;
        $metodoUsado = 'LOCAL_LOG';
        $mensajeError = '';

        if ($smtpEnabled) {
            $resultadoSMTP = self::enviarViaSMTP($destinatario, $asunto, $cuerpoHTML, $config, $replyTo);
            $enviado = $resultadoSMTP['exito'];
            $metodoUsado = 'SMTP (' . ($config['smtp_host'] ?? '') . ')';
            $mensajeError = $resultadoSMTP['error'] ?? '';
        } else {
            // Intentar PHP mail() nativo (útil en producción cPanel/Hosting)
            $headers = [];
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "Content-type: text/html; charset=UTF-8";
            $headers[] = "From: " . ($config['from_name'] ?? 'NutriSalud') . " <" . ($config['from_email'] ?? 'notificaciones@nutrisalud.com') . ">";
            if (!empty($replyTo) && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
                $headers[] = "Reply-To: " . $replyTo;
            }
            $headers[] = "X-Mailer: PHP/" . phpversion();

            $cabecerasStr = implode("\r\n", $headers);

            try {
                $enviado = @mail($destinatario, '=?UTF-8?B?' . base64_encode($asunto) . '?=', $cuerpoHTML, $cabecerasStr);
                $metodoUsado = 'PHP_MAIL_NATIVO';
            } catch (Throwable $e) {
                $enviado = false;
                $mensajeError = $e->getMessage();
            }
        }

        // Registrar siempre en log para auditoría
        self::registrarLogEnvio($destinatario, $asunto, $enviado, $metodoUsado, $mensajeError);

        // Si está en modo log local (sin SMTP activo), retornamos true registrando en log
        return $enviado || !$smtpEnabled;
    }

    /**
     * Cliente SMTP Socket Puro en PHP (Sin dependencias externas, compatible con SSL/TLS/STARTTLS).
     */
    public static function enviarViaSMTP($destinatario, $asunto, $cuerpoHTML, $config, $replyTo = null) {
        $host = $config['smtp_host'] ?? 'localhost';
        $port = (int)($config['smtp_port'] ?? 587);
        $user = $config['smtp_username'] ?? '';
        $pass = $config['smtp_password'] ?? '';
        $fromEmail = !empty($config['from_email']) ? $config['from_email'] : $user;
        $fromName = $config['from_name'] ?? 'NutriSalud';
        $secure = strtolower($config['smtp_secure'] ?? 'tls');

        $timeout = 15;
        $errno = 0;
        $errstr = '';

        $prefix = ($secure === 'ssl' || $port === 465) ? 'ssl://' : '';
        $socket = @stream_socket_client($prefix . $host . ':' . $port, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT);

        if (!$socket) {
            return ['exito' => false, 'error' => "No se pudo conectar a $host:$port ($errstr)"];
        }

        stream_set_timeout($socket, $timeout);

        $leerRespuesta = function() use ($socket) {
            $respuesta = '';
            while ($linea = fgets($socket, 515)) {
                $respuesta .= $linea;
                if (substr($linea, 3, 1) === ' ') break;
            }
            return $respuesta;
        };

        $enviarComando = function($comando) use ($socket, $leerRespuesta) {
            fputs($socket, $comando . "\r\n");
            return $leerRespuesta();
        };

        $resp = $leerRespuesta();
        if (substr($resp, 0, 3) !== '220') {
            fclose($socket);
            return ['exito' => false, 'error' => "Banner SMTP inválido: $resp"];
        }

        $enviarComando("EHLO " . (gethostname() ?: 'localhost'));

        // STARTTLS para puerto 587 o modo TLS
        if (($secure === 'tls' || $port === 587) && $prefix === '') {
            $resp = $enviarComando("STARTTLS");
            if (substr($resp, 0, 3) === '220') {
                $crypto = @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                if (!$crypto) {
                    fclose($socket);
                    return ['exito' => false, 'error' => "Fallo en la negociación TLS/STARTTLS"];
                }
                $enviarComando("EHLO " . (gethostname() ?: 'localhost'));
            }
        }

        // Autenticación AUTH LOGIN
        $resp = $enviarComando("AUTH LOGIN");
        if (substr($resp, 0, 3) !== '334') {
            fclose($socket);
            return ['exito' => false, 'error' => "AUTH LOGIN no soportado: $resp"];
        }

        $resp = $enviarComando(base64_encode($user));
        if (substr($resp, 0, 3) !== '334') {
            fclose($socket);
            return ['exito' => false, 'error' => "Usuario SMTP rechazado: $resp"];
        }

        $resp = $enviarComando(base64_encode($pass));
        if (substr($resp, 0, 3) !== '235') {
            fclose($socket);
            return ['exito' => false, 'error' => "Contraseña SMTP incorrecta / Autenticación fallida: $resp"];
        }

        // MAIL FROM & RCPT TO
        $resp = $enviarComando("MAIL FROM: <$fromEmail>");
        if (substr($resp, 0, 3) !== '250') {
            fclose($socket);
            return ['exito' => false, 'error' => "MAIL FROM rechazado: $resp"];
        }

        $resp = $enviarComando("RCPT TO: <$destinatario>");
        if (substr($resp, 0, 3) !== '250' && substr($resp, 0, 3) !== '251') {
            fclose($socket);
            return ['exito' => false, 'error' => "Destinatario rechazado: $resp"];
        }

        // DATA
        $resp = $enviarComando("DATA");
        if (substr($resp, 0, 3) !== '354') {
            fclose($socket);
            return ['exito' => false, 'error' => "DATA rechazado: $resp"];
        }

        // Contenido del mensaje con cabeceras MIME
        $cabeceras = [];
        $cabeceras[] = "MIME-Version: 1.0";
        $cabeceras[] = "Content-Type: text/html; charset=UTF-8";
        $cabeceras[] = "Content-Transfer-Encoding: base64";
        $cabeceras[] = "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <$fromEmail>";
        $cabeceras[] = "To: <$destinatario>";
        if (!empty($replyTo)) {
            $cabeceras[] = "Reply-To: <$replyTo>";
        }
        $cabeceras[] = "Subject: =?UTF-8?B?" . base64_encode($asunto) . "?=";
        $cabeceras[] = "Date: " . date('r');
        $cabeceras[] = "X-Mailer: NutriSalud-Mailer-v2.0";

        $cuerpoBase64 = chunk_split(base64_encode($cuerpoHTML));
        $mensajeCompleto = implode("\r\n", $cabeceras) . "\r\n\r\n" . $cuerpoBase64 . "\r\n.";

        fputs($socket, $mensajeCompleto . "\r\n");
        $resp = $leerRespuesta();

        $enviarComando("QUIT");
        fclose($socket);

        if (substr($resp, 0, 3) === '250') {
            return ['exito' => true, 'error' => ''];
        }

        return ['exito' => false, 'error' => "Error al finalizar DATA: $resp"];
    }

    /**
     * Genera la plantilla HTML institucional responsive con el color de marca del profesional.
     */
    private static function generarPlantillaAltaPaciente($nombrePaciente, $usuario, $password, $loginUrl, $nombreNutri, $especialidadNutri, $matriculaNutri, $colorTema, $nutri) {
        return '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenida a NutriSalud</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7f6; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; color: #2c3e50;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: #f4f7f6; padding: 30px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.06); max-width: 600px; width: 100%;">
                    
                    <!-- Header con color de marca -->
                    <tr>
                        <td style="background-color: ' . htmlspecialchars($colorTema) . '; padding: 35px 30px; text-align: center; color: #ffffff;">
                            <h1 style="margin: 0 0 5px 0; font-size: 26px; font-weight: bold; letter-spacing: 0.5px;">NutriSalud</h1>
                            <p style="margin: 0; font-size: 14px; opacity: 0.9;">Tu Portal de Nutrición y Salud Integral</p>
                        </td>
                    </tr>

                    <!-- Contenido Principal -->
                    <tr>
                        <td style="padding: 35px 35px 25px 35px;">
                            <h2 style="margin: 0 0 15px 0; color: #1a252f; font-size: 20px;">¡Hola, ' . htmlspecialchars($nombrePaciente) . '! 👋</h2>
                            <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 1.6; color: #555555;">
                                Tu profesional de nutrición te ha dado de alta en la plataforma. A partir de ahora podrás consultar tus planes de alimentación, seguir tus hábitos diarios y revisar tus próximos turnos desde tu portal personal.
                            </p>

                            <!-- Tarjeta de Credenciales -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; margin: 20px 0; padding: 20px;">
                                <tr>
                                    <td>
                                        <div style="font-size: 13px; text-transform: uppercase; font-weight: bold; color: ' . htmlspecialchars($colorTema) . '; margin-bottom: 12px; letter-spacing: 0.5px;">
                                            🔐 Tus Credenciales de Acceso
                                        </div>
                                        <table border="0" cellpadding="6" cellspacing="0" width="100%" style="font-size: 14px;">
                                            <tr>
                                                <td width="35%" style="color: #64748b; font-weight: bold;">Usuario (DNI):</td>
                                                <td style="color: #0f172a; font-weight: bold; font-family: monospace; font-size: 15px;">' . htmlspecialchars($usuario) . '</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #64748b; font-weight: bold;">Contraseña Inicial:</td>
                                                <td style="color: #0f172a; font-weight: bold; font-family: monospace; font-size: 15px;">' . htmlspecialchars($password) . '</td>
                                            </tr>
                                        </table>
                                        <div style="margin-top: 12px; font-size: 12px; color: #94a3b8; font-style: italic;">
                                            * Te recomendamos cambiar tu contraseña una vez que ingreses por primera vez desde "Mi Perfil".
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Botón de Ingreso -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 25px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="' . htmlspecialchars($loginUrl) . '" target="_blank" style="background-color: ' . htmlspecialchars($colorTema) . '; color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 50px; font-weight: bold; font-size: 15px; display: inline-block; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
                                            Ingresar a Mi Portal &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Tarjeta del Profesional -->
                            <div style="background-color: #f1f5f9; border-radius: 10px; padding: 15px 20px; margin-top: 25px;">
                                <div style="font-size: 11px; text-transform: uppercase; font-weight: bold; color: #64748b;">Tu Profesional a Cargo:</div>
                                <div style="font-size: 15px; font-weight: bold; color: #1e293b; margin-top: 2px;">' . htmlspecialchars($nombreNutri) . '</div>
                                <div style="font-size: 13px; color: #475569;">' . htmlspecialchars($especialidadNutri) . ($matriculaNutri ? ' • M.P. ' . htmlspecialchars($matriculaNutri) : '') . '</div>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 30px; text-align: center; font-size: 12px; color: #94a3b8;">
                            <p style="margin: 0 0 5px 0;">Este es un mensaje automático generado por NutriSalud.</p>
                            <p style="margin: 0;">Si no solicitaste esta cuenta, por favor ponte en contacto con tu nutricionista.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    /**
     * Genera la plantilla HTML para el correo de restablecimiento de contraseña.
     */
    private static function generarPlantillaRecuperacionPassword($nombre, $resetUrl, $tipoUsuario, $minutos, $colorTema) {
        $rolLabel = ($tipoUsuario === 'paciente') ? 'Portal de Pacientes' : 'Consultorio Profesional';
        
        return '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña - NutriSalud</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7f6; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; color: #2c3e50;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: #f4f7f6; padding: 30px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.06); max-width: 600px; width: 100%;">
                    
                    <!-- Header con color de marca -->
                    <tr>
                        <td style="background-color: ' . htmlspecialchars($colorTema) . '; padding: 30px; text-align: center; color: #ffffff;">
                            <h1 style="margin: 0 0 5px 0; font-size: 24px; font-weight: bold;">NutriSalud</h1>
                            <p style="margin: 0; font-size: 13px; opacity: 0.9;">' . htmlspecialchars($rolLabel) . '</p>
                        </td>
                    </tr>

                    <!-- Contenido Principal -->
                    <tr>
                        <td style="padding: 35px 35px 25px 35px;">
                            <h2 style="margin: 0 0 15px 0; color: #1a252f; font-size: 20px;">Solicitud de Recuperación de Contraseña</h2>
                            <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 1.6; color: #555555;">
                                Hola <strong>' . htmlspecialchars($nombre) . '</strong>, hemos recibido una solicitud para restablecer la contraseña de tu cuenta en NutriSalud.
                            </p>
                            <p style="margin: 0 0 25px 0; font-size: 14px; line-height: 1.6; color: #555555;">
                                Haz clic en el botón a continuación para crear tu nueva contraseña segura:
                            </p>

                            <!-- Botón de Restablecimiento -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 25px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="' . htmlspecialchars($resetUrl) . '" target="_blank" style="background-color: ' . htmlspecialchars($colorTema) . '; color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 50px; font-weight: bold; font-size: 15px; display: inline-block; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
                                            Restablecer mi Contraseña &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Alerta de Expiración y Seguridad -->
                            <div style="background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 10px; padding: 15px; margin: 25px 0; font-size: 13px; color: #92400e;">
                                <strong>⚠️ Importante:</strong> Este enlace es de un solo uso y expirará automáticamente en <strong>' . (int)$minutos . ' minutos</strong> por motivos de seguridad.
                            </div>

                            <p style="margin: 0 0 10px 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                                Si el botón no funciona, copia y pega el siguiente enlace en tu navegador web:<br>
                                <a href="' . htmlspecialchars($resetUrl) . '" style="color: ' . htmlspecialchars($colorTema) . '; word-break: break-all;">' . htmlspecialchars($resetUrl) . '</a>
                            </p>
                            
                            <p style="margin: 15px 0 0 0; font-size: 12px; color: #94a3b8;">
                                Si no solicitaste este cambio, puedes ignorar este correo; tu contraseña actual continuará siendo segura.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 30px; text-align: center; font-size: 12px; color: #94a3b8;">
                            <p style="margin: 0 0 5px 0;">Mensaje automático de seguridad de NutriSalud.</p>
                            <p style="margin: 0;">&copy; ' . date('Y') . ' NutriSalud SaaS. Todos los derechos reservados.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    /**
     * Registra en archivo de log local cada intento de envío con información detallada.
     */
    private static function registrarLogEnvio($destinatario, $asunto, $enviado, $metodo = 'DESCONOCIDO', $error = '') {
        $logsDir = __DIR__ . '/../logs/';
        if (!is_dir($logsDir)) {
            @mkdir($logsDir, 0777, true);
        }
        $logFile = $logsDir . 'emails_enviados.log';
        $timestamp = date('Y-m-d H:i:s');
        $estado = $enviado ? 'ENVIADO_OK' : 'PENDIENTE_ENVIO_REAL';
        $linea = "[$timestamp] [$estado] [$metodo] Para: $destinatario | Asunto: $asunto" . ($error ? " | Info: $error" : "") . "\n";
        @file_put_contents($logFile, $linea, FILE_APPEND);
    }
}
