<?php
// SMTP-Test für PHPMailer - Detaillierte Fehleranalyse
echo "<h1>🔧 SMTP-Verbindungstest</h1>";
echo "<style>body{font-family:Arial;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";

// Ihre SMTP-Konfiguration (dieselben Werte wie in send_gespraech.php)
$smtp_config = [
    'host' => 'smtp.ionos.de',
    'port' => 587,
    'encryption' => 'tls',
    'username' => 'no-reply@vereins-web.de',
    'password' => '17Tobbse10+',
];

$recipient_email = 'tobias.westrich@gmx.de';

echo "<h2>📋 Konfiguration:</h2>";
echo "<div class='info'>";
echo "Host: " . $smtp_config['host'] . "<br>";
echo "Port: " . $smtp_config['port'] . "<br>";
echo "Verschlüsselung: " . $smtp_config['encryption'] . "<br>";
echo "Benutzer: " . $smtp_config['username'] . "<br>";
echo "Test-Empfänger: " . $recipient_email . "<br>";
echo "</div>";

echo "<h2>🧪 Test wird durchgeführt...</h2>";

try {
    require_once 'phpmailer/Exception.php';
    require_once 'phpmailer/PHPMailer.php';
    require_once 'phpmailer/SMTP.php';
    
    echo "<p class='success'>✅ PHPMailer-Dateien erfolgreich geladen</p>";
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    // Debug-Ausgabe aktivieren
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level) {
        echo "<div style='background:#f0f0f0;padding:5px;margin:2px;font-family:monospace;'>" . 
             htmlspecialchars(trim($str)) . "</div>";
    };
    
    // SMTP-Konfiguration
    $mail->isSMTP();
    $mail->Host = $smtp_config['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_config['username'];
    $mail->Password = $smtp_config['password'];
    $mail->SMTPSecure = $smtp_config['encryption'];
    $mail->Port = $smtp_config['port'];
    $mail->CharSet = 'UTF-8';
    
    // Timeout erhöhen
    $mail->Timeout = 60;
    
    echo "<h3>🔌 Verbindungstest...</h3>";
    
    // E-Mail-Details
    $mail->setFrom($smtp_config['username'], 'SMTP Test');
    $mail->addAddress($recipient_email, 'Test-Empfänger');
    
    $mail->isHTML(true);
    $mail->Subject = 'SMTP-Test von ' . date('Y-m-d H:i:s');
    $mail->Body = '<h1>Test erfolgreich!</h1><p>Diese E-Mail wurde erfolgreich über SMTP gesendet.</p>';
    $mail->AltBody = 'Test erfolgreich! Diese E-Mail wurde erfolgreich über SMTP gesendet.';
    
    echo "<h3>📤 E-Mail wird gesendet...</h3>";
    
    $mail->send();
    
    echo "<h2 class='success'>🎉 SUCCESS!</h2>";
    echo "<p class='success'>✅ E-Mail wurde erfolgreich gesendet!</p>";
    echo "<p>Überprüfen Sie den Posteingang von: " . $recipient_email . "</p>";
    
} catch (Exception $e) {
    echo "<h2 class='error'>❌ FEHLER!</h2>";
    echo "<div class='error'>";
    echo "<strong>Fehler:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<strong>Code:</strong> " . $e->getCode() . "<br>";
    echo "<strong>Datei:</strong> " . $e->getFile() . " (Zeile " . $e->getLine() . ")<br>";
    echo "</div>";
    
    echo "<h3>🔍 Mögliche Lösungen:</h3>";
    
    if (strpos($e->getMessage(), 'SMTP connect()') !== false) {
        echo "<div class='error'>";
        echo "• Verbindungsproblem zum SMTP-Server<br>";
        echo "• Überprüfen Sie Host und Port<br>";
        echo "• Firewall könnte Port " . $smtp_config['port'] . " blockieren<br>";
        echo "</div>";
    }
    
    if (strpos($e->getMessage(), 'SMTP Auth') !== false || strpos($e->getMessage(), 'authentication') !== false) {
        echo "<div class='error'>";
        echo "• Anmeldedaten sind falsch<br>";
        echo "• Überprüfen Sie Benutzername und Passwort<br>";
        echo "• Eventuell App-Passwort erforderlich<br>";
        echo "</div>";
    }
    
    if (strpos($e->getMessage(), 'certificate') !== false) {
        echo "<div class='error'>";
        echo "• SSL/TLS-Zertifikatsproblem<br>";
        echo "• Versuchen Sie 'ssl' statt 'tls'<br>";
        echo "• Oder Port 465 statt 587<br>";
        echo "</div>";
    }
}

echo "<hr>";
echo "<p><a href='gespraech.html'>← Zurück zum Formular</a></p>";
?>
