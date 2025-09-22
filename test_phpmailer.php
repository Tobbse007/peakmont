<?php
// Schneller Test für PHPMailer-Installation
echo "🔧 PHPMailer Test\n\n";

// Teste ob die Dateien geladen werden können
try {
    require_once 'phpmailer/Exception.php';
    require_once 'phpmailer/PHPMailer.php';
    require_once 'phpmailer/SMTP.php';
    
    echo "✅ PHPMailer-Dateien erfolgreich geladen\n";
    
    // Teste ob die Klasse existiert
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        echo "✅ PHPMailer-Klasse verfügbar\n";
        
        // Erstelle eine Test-Instanz
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        echo "✅ PHPMailer-Instanz erfolgreich erstellt\n";
        
        echo "\n🎉 PHPMailer ist korrekt installiert und einsatzbereit!\n";
        echo "Die E-Mail-Formulare können jetzt professionelle HTML-E-Mails mit SMTP versenden.\n";
        
    } else {
        echo "❌ PHPMailer-Klasse nicht gefunden\n";
    }
    
} catch (Exception $e) {
    echo "❌ Fehler beim Laden von PHPMailer: " . $e->getMessage() . "\n";
}

echo "\n📋 Nächste Schritte:\n";
echo "1. Diese Datei auf Ihrem Server ausführen\n";
echo "2. Bei erfolgreichem Test: Das Kontaktformular testen\n";
echo "3. SMTP-Daten in send_gespraech.php anpassen (falls nötig)\n";
?>
