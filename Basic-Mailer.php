<?php
// Ensure all variables are set
foreach (['name', 'send_to', 'subject', 'redirect', 'path_to_backend', 'user'] as $var) {
    if (!isset($$var)) {
        die("Error: Fehler in der Konfiguration");
    }
}

// Load SMTP credentials
$config = require $path_to_backend . 'Basic-Mailer/ENV/' . $user . '/env.php';

// PHPMailer
require $path_to_backend . 'PHPMailer6/src/Exception.php';
require $path_to_backend . 'PHPMailer6/src/PHPMailer.php';
require $path_to_backend . 'PHPMailer6/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Honeypot bot trap
if(array_key_exists('contact_notes', $_POST) && !empty($_POST['contact_notes'])) {
  echo 'Thank you for your request!';
  sleep(5);
  exit;
}

// Remove honeypot field
unset($_POST['contact_notes']);

// Validate `email_from` field
if (empty($_POST['email_from']) || !filter_var($_POST['email_from'], FILTER_VALIDATE_EMAIL)) {
    echo 'Bitte geben Sie ein gültige E-Mail Adresse ein!';
    exit;
}

$email_from = $_POST['email_from'];

// Prevent header injection
if (preg_match('/[\r\n]/', $email_from)) {
    echo 'Error: Invalid email address.';
    exit;
}

// Create a new PHPMailer instance
$mail = new PHPMailer();

// SMTP configuration using $config values
$mail->isSMTP();
$mail->Host       = $config['smtp_host'];
$mail->SMTPAuth   = true;
$mail->Username   = $config['smtp_user'];
$mail->Password   = $config['smtp_pass'];
$mail->Port       = $config['smtp_port'];

if ($config['smtp_secure'] === 'ssl') {
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
} elseif ($config['smtp_secure'] === 'tls') {
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
} else {
    $mail->SMTPSecure = '';
}

// From and To
$mail->setFrom($config['smtp_user'], $name);
$mail->addAddress($send_to);

// Reply-to from the form
$mail->addReplyTo($email_from);

// Email subject and encoding
$mail->Subject = $subject;
$mail->CharSet = 'UTF-8';

// Build the email body
$mail->isHTML(true); // Enable HTML mode

$body = "<h2>$subject</h2>\n\n";
$body .= "<table cellpadding='6' cellspacing='0' border='1' style='border-collapse:collapse; font-family: monospace;'>";

foreach ($_POST as $field => $value) {
    $value = trim($value ?? '');
    $clean_field = htmlspecialchars($field, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $formatted_field = strtoupper(str_replace('_', ' ', $clean_field));
    $clean_value = nl2br(htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

    $body .= "<tr>";
    $body .= "<td><strong>$formatted_field</strong></td>";
    $body .= "<td>$clean_value</td>";
    $body .= "</tr>";
}

$body .= "</table>";

$mail->Body = $body;

// Plain-text fallback
$mail->AltBody = strip_tags(preg_replace('#<br\s*/?>#i', "\n", $body));

// Send
if (!$mail->send()) {
    die("Leider konnte Ihre Email nicht zugestellt werden. Das tut uns leid! <br />Bitte versuchen Sie es zu einem sp&auml;teren Zeitpunkt noch einmal.<br /><br />");
} else {
    echo "<script>window.location.href='" . $redirect . "';</script>"; 
    exit;
}