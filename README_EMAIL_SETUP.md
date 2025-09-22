# 📧 PHP E-Mail Setup - Anleitung

## 🔧 Konfiguration

### 1. E-Mail-Einstellungen anpassen
Öffnen Sie die Datei `send_gespraech.php` und passen Sie die folgenden Zeilen an:

```php
// SMTP-Konfiguration für den E-Mail-Versand
$smtp_config = [
    'host' => 'smtp.gmail.com',          // ← IHR SMTP-SERVER
    'port' => 587,                       // ← SMTP-PORT
    'encryption' => 'tls',               // ← VERSCHLÜSSELUNG
    'username' => 'IHRE_EMAIL@gmail.com', // ← IHRE E-MAIL-ADRESSE
    'password' => 'IHR_APP_PASSWORD',     // ← IHR PASSWORT
];

// E-Mail-Empfänger  
$recipient_email = 'kontakt@peakmont.de';  // ← EMPFÄNGER-E-MAIL
$sender_email = 'IHRE_EMAIL@gmail.com';    // ← ABSENDER-E-MAIL
```

## 📝 Beispiel-Konfigurationen

### Gmail:
```php
'host' => 'smtp.gmail.com',
'port' => 587,
'encryption' => 'tls',
'username' => 'ihr.name@gmail.com',
'password' => 'abcd efgh ijkl mnop',  // App-Passwort, nicht normales Passwort!
```

### IONOS/1&1:
```php
'host' => 'smtp.ionos.de',
'port' => 587,
'encryption' => 'tls',
'username' => 'ihre@domain.de',
'password' => 'IhrPasswort123',
```

### Strato:
```php
'host' => 'smtp.strato.de',
'port' => 587,
'encryption' => 'tls',
'username' => 'ihre@domain.de',
'password' => 'IhrPasswort123',
```

### All-Inkl:
```php
'host' => 'smtp.all-inkl.com',
'port' => 587,
'encryption' => 'tls',
'username' => 'ihre@domain.de',
'password' => 'IhrPasswort123',
```

## 🚀 Installation (Empfohlen: PHPMailer)

### Option 1: PHPMailer herunterladen
1. Gehen Sie zu: https://github.com/PHPMailer/PHPMailer/releases
2. Laden Sie die neueste Version herunter
3. Entpacken Sie den Ordner "PHPMailer" in Ihr Webverzeichnis

### Option 2: Mit Composer (falls verfügbar)
```bash
composer require phpmailer/phpmailer
```

## 📁 Datei-Struktur
```
Ihr Website-Ordner/
├── gespraech.html
├── send_gespraech.php
├── PHPMailer/              ← Hier PHPMailer-Dateien
│   ├── src/
│   │   ├── PHPMailer.php
│   │   ├── SMTP.php
│   │   └── Exception.php
│   └── ...
└── andere-dateien...
```

## ⚙️ Wichtige Hinweise

### Gmail App-Passwörter:
1. Gehen Sie zu Ihrem Google-Konto
2. Sicherheit → 2-Faktor-Authentifizierung aktivieren
3. App-Passwörter erstellen
4. Das generierte Passwort (16 Zeichen) verwenden

### Testen des PHP-Skripts:
1. Laden Sie alle Dateien auf Ihren Webserver hoch
2. Öffnen Sie `gespraech.html` in Ihrem Browser
3. Füllen Sie das Formular aus
4. Prüfen Sie, ob die E-Mail ankommt

### Fehlersuche:
- Prüfen Sie PHP-Fehlerprotokolle
- Testen Sie SMTP-Einstellungen mit einem E-Mail-Client
- Kontaktieren Sie Ihren Hosting-Provider bei Problemen

## 🎨 E-Mail-Design Features

Das PHP-Skript erstellt automatisch:
- ✅ Professionelles HTML-E-Mail-Design
- ✅ Mobile-responsives Layout
- ✅ Strukturierte Darstellung aller Formularfelder
- ✅ Text-Alternative für bessere Zustellbarkeit
- ✅ Automatische Prioritätskennzeichnung
- ✅ Zeitstempel der Anfrage

## 🔒 Sicherheit

- ✅ Eingabevalidierung
- ✅ XSS-Schutz durch htmlspecialchars()
- ✅ E-Mail-Format-Validierung
- ✅ Fehlerbehandlung

## 📞 Support

Falls Sie Hilfe bei der Einrichtung benötigen:
1. Prüfen Sie Ihre SMTP-Einstellungen beim Hosting-Provider
2. Testen Sie zuerst mit einfachen E-Mail-Clients
3. Aktivieren Sie PHP-Fehlerberichte für Debugging
