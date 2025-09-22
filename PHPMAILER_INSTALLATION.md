# PHPMailer Installation & Setup Guide

## 🚨 WICHTIG: PHPMailer muss installiert werden!

Der Fehler "Fehler beim Senden der E-Mail" kommt daher, dass PHPMailer nicht auf dem Server installiert ist.

## Option 1: Composer Installation (Empfohlen)

### Schritt 1: Composer installieren
Falls Composer nicht auf dem Server ist:
```bash
# Download composer
curl -sS https://getcomposer.org/installer | php

# Global verfügbar machen
sudo mv composer.phar /usr/local/bin/composer
```

### Schritt 2: PHPMailer installieren
```bash
# In Ihr Projekt-Verzeichnis wechseln
cd /pfad/zu/ihrem/projekt

# PHPMailer installieren
composer require phpmailer/phpmailer
```

### Schritt 3: Autoloader in PHP einbinden
In `send_gespraech.php` ganz oben nach `<?php` hinzufügen:
```php
require_once 'vendor/autoload.php';
```

## Option 2: Manuelle Installation

### Schritt 1: PHPMailer herunterladen
1. Gehen Sie zu: https://github.com/PHPMailer/PHPMailer/releases
2. Laden Sie die neueste Version herunter
3. Entpacken Sie die Datei

### Schritt 2: Dateien hochladen
Laden Sie diese Dateien in einen `phpmailer/` Ordner auf Ihren Server:
- `src/PHPMailer.php`
- `src/SMTP.php` 
- `src/Exception.php`

### Schritt 3: Dateien in PHP einbinden
In `send_gespraech.php` ganz oben nach `<?php` hinzufügen:
```php
require_once 'phpmailer/src/Exception.php';
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';
```

## Server-Konfiguration prüfen

### SMTP-Einstellungen für gängige Provider:

**Gmail:**
```php
'host' => 'smtp.gmail.com',
'port' => 587,
'encryption' => 'tls',
```

**Ionos/1&1:**
```php
'host' => 'smtp.ionos.de',
'port' => 587,
'encryption' => 'tls',
```

**Strato:**
```php
'host' => 'smtp.strato.de',
'port' => 587,
'encryption' => 'tls',
```

## Fehlerdiagnose

### Debug-Modus aktivieren
In `send_gespraech.php` nach der Zeile `$mail = new PHPMailer(true);` hinzufügen:
```php
$mail->SMTPDebug = 2; // Debug-Ausgabe aktivieren
$mail->Debugoutput = 'html'; // Debug als HTML ausgeben
```

### Häufige Probleme:

1. **PHPMailer nicht installiert**
   - Lösung: Option 1 oder 2 befolgen

2. **SMTP-Anmeldedaten falsch**
   - Überprüfen Sie Username/Passwort
   - Bei Gmail: App-Passwort verwenden

3. **Firewall blockiert SMTP**
   - Port 587 oder 465 freigeben

4. **SSL-Zertifikat Probleme**
   - `$mail->SMTPOptions` konfigurieren

## Test der Installation

Nach der Installation können Sie testen mit:
```bash
php -m | grep openssl  # SSL-Support prüfen
php send_gespraech.php # Script direkt testen
```

## ⚡ Schnelle Lösung

Falls Sie schnell eine Lösung brauben, verwenden Sie die native PHP `mail()` Funktion:

1. Erstellen Sie `send_gespraech_simple.php`:
```php
<?php
if ($_POST) {
    $to = "tobias.westrich@gmx.de";
    $subject = "Neue Anfrage von " . $_POST['name'];
    $message = "Name: " . $_POST['name'] . "\n";
    $message .= "Email: " . $_POST['email'] . "\n";
    $message .= "Nachricht: " . $_POST['message'];
    $headers = "From: " . $_POST['email'];
    
    if (mail($to, $subject, $message, $headers)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
```

2. In `gespraech.html` die Formular-Action ändern zu: `action="send_gespraech_simple.php"`

Dies funktioniert ohne PHPMailer, ist aber weniger sicher und professionell.
