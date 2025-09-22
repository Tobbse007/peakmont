<?php
// =====================================
// KONFIGURATION - HIER IHRE DATEN EINGEBEN
// =====================================

// SMTP-Konfiguration für den E-Mail-Versand
$smtp_config = [
    'host' => 'smtp.ionos.de',          // SMTP-Server (z.B. smtp.gmail.com, smtp.ionos.de, etc.)
    'port' => 587,                       // SMTP-Port (587 für TLS, 465 für SSL)
    'encryption' => 'tls',               // 'tls' oder 'ssl'
    'username' => 'no-reply@vereins-web.de', // ← HIER IHRE E-MAIL-ADRESSE EINGEBEN
    'password' => '17Tobbse10+',     // ← HIER IHR E-MAIL-PASSWORT/APP-PASSWORT EINGEBEN
];

// E-Mail-Empfänger
$recipient_email = 'tobias.westrich@gmx.de';  // ← HIER DIE E-MAIL-ADRESSE EINGEBEN, DIE DIE ANFRAGEN ERHALTEN SOLL
$recipient_name = 'Peakmont Team';

// Absender-Informationen
$sender_email = 'no-reply@vereins-web.de';    // ← SOLLTE DIESELBE WIE SMTP USERNAME SEIN
$sender_name = 'Peakmont Kontaktformular';

// =====================================
// ENDE KONFIGURATION
// =====================================

// Fehlerbehandlung und Validierung
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Eingabedaten sammeln und validieren
    $data = [
        'vorname' => trim($_POST['vorname'] ?? ''),
        'nachname' => trim($_POST['nachname'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'telefon' => trim($_POST['telefon'] ?? ''),
        'unternehmen' => trim($_POST['unternehmen'] ?? ''),
        'branche' => $_POST['branche'] ?? '',
        'position' => trim($_POST['position'] ?? ''),
        'mitarbeiter' => $_POST['mitarbeiter'] ?? '',
        'ziele' => $_POST['ziele'] ?? [],
        'budget' => $_POST['budget'] ?? '',
        'beschreibung' => trim($_POST['beschreibung'] ?? ''),
        'social_media_erfahrung' => $_POST['social_media_erfahrung'] ?? '',
        'plattformen' => $_POST['plattformen'] ?? [],
        'leistungen_interesse' => $_POST['leistungen_interesse'] ?? [],
        'bisherige_versuche' => trim($_POST['bisherige_versuche'] ?? ''),
        'sonstiges' => trim($_POST['sonstiges'] ?? '')
    ];
    
    // Validierung der Pflichtfelder
    if (empty($data['vorname'])) $errors[] = 'Vorname ist erforderlich';
    if (empty($data['nachname'])) $errors[] = 'Nachname ist erforderlich';
    if (empty($data['email'])) $errors[] = 'E-Mail-Adresse ist erforderlich';
    if (empty($data['telefon'])) $errors[] = 'Telefonnummer ist erforderlich';
    if (empty($data['unternehmen'])) $errors[] = 'Unternehmensname ist erforderlich';
    if (empty($data['branche'])) $errors[] = 'Branche ist erforderlich';
    if (empty($data['position'])) $errors[] = 'Position ist erforderlich';
    if (empty($data['ziele'])) $errors[] = 'Mindestens ein Ziel muss ausgewählt werden';
    
    // E-Mail-Format validieren
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Ungültige E-Mail-Adresse';
    }
    
    // Wenn keine Fehler, E-Mail senden
    if (empty($errors)) {
        
        // E-Mail-Inhalt generieren
        $html_content = generateEmailHTML($data);
        $text_content = generateEmailText($data);
        
        // E-Mail senden
        $email_result = sendEmail($html_content, $text_content, $data, $smtp_config, $recipient_email, $recipient_name, $sender_email, $sender_name);
        
        if ($email_result) {
            $success = true;
            // Entferne Debug-Informationen bei Erfolg
            $errors = [];
        } else {
            // Debug-Informationen nur bei Fehler
            $debug_errors = [
                "🔧 Debug-Modus aktiviert - Konfiguration wird geprüft...",
                "📧 Empfänger: " . $recipient_email,
                "📤 Absender: " . $sender_email,
                "🌐 SMTP-Host: " . $smtp_config['host'],
                "🔌 SMTP-Port: " . $smtp_config['port'],
                "🔐 Verschlüsselung: " . $smtp_config['encryption']
            ];
            $errors = array_merge($debug_errors, $errors);
            $errors[] = '❌ E-Mail konnte nicht gesendet werden - siehe Details oben.';
        }
    }
}

/**
 * E-Mail-HTML-Template generieren
 */
function generateEmailHTML($data) {
    
    // Arrays zu lesbaren Strings konvertieren
    $ziele_str = !empty($data['ziele']) ? implode(', ', array_map('ucfirst', $data['ziele'])) : 'Nicht angegeben';
    $plattformen_str = !empty($data['plattformen']) ? implode(', ', array_map('ucfirst', $data['plattformen'])) : 'Nicht angegeben';
    $leistungen_str = !empty($data['leistungen_interesse']) ? implode(', ', array_map('ucfirst', $data['leistungen_interesse'])) : 'Nicht angegeben';
    
    // Branche und andere Selects lesbar machen
    $branche_readable = [
        'e-commerce' => 'E-Commerce',
        'saas' => 'SaaS/Software',
        'beratung' => 'Beratung/Coaching',
        'gesundheit' => 'Gesundheit/Wellness',
        'immobilien' => 'Immobilien',
        'finanzdienstleistungen' => 'Finanzdienstleistungen',
        'bildung' => 'Bildung/E-Learning',
        'automotive' => 'Automotive',
        'tourismus' => 'Tourismus/Reisen',
        'andere' => 'Andere'
    ];
    
    $budget_readable = [
        'unter-5000' => 'Unter 5.000€',
        '5000-15000' => '5.000€ - 15.000€',
        '15000-50000' => '15.000€ - 50.000€',
        'ueber-50000' => 'Über 50.000€'
    ];
    
    $mitarbeiter_readable = [
        '1' => 'Nur ich (Solopreneur)',
        '2-10' => '2-10 Mitarbeiter',
        '11-50' => '11-50 Mitarbeiter',
        '51-200' => '51-200 Mitarbeiter',
        '200+' => 'Über 200 Mitarbeiter'
    ];
    
    $erfahrung_readable = [
        'nein' => 'Nein, bin Anfänger',
        'etwas' => 'Etwas Erfahrung',
        'viel' => 'Viel Erfahrung'
    ];
    
    $branche_display = $branche_readable[$data['branche']] ?? $data['branche'];
    $budget_display = $budget_readable[$data['budget']] ?? ($data['budget'] ?: 'Nicht angegeben');
    $mitarbeiter_display = $mitarbeiter_readable[$data['mitarbeiter']] ?? ($data['mitarbeiter'] ?: 'Nicht angegeben');
    $erfahrung_display = $erfahrung_readable[$data['social_media_erfahrung']] ?? ($data['social_media_erfahrung'] ?: 'Nicht angegeben');
    
    $html = '
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neue Gesprächsanfrage</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f8fafc;
        }
        
        .email-container {
            max-width: 700px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .section {
            margin-bottom: 35px;
            padding-bottom: 25px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .section-icon {
            width: 24px;
            height: 24px;
            margin-right: 10px;
            background: #3b82f6;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        }
        
        .info-item {
            background: #f8fafc;
            padding: 18px;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }
        
        .info-label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            color: #1f2937;
            font-weight: 500;
        }
        
        .full-width {
            grid-column: 1 / -1;
        }
        
        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }
        
        .tag {
            background: #dbeafe;
            color: #1d4ed8;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .text-content {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #10b981;
            margin-top: 15px;
        }
        
        .text-content p {
            margin: 0;
            color: #374151;
            line-height: 1.7;
        }
        
        .footer {
            background: #1f2937;
            color: #9ca3af;
            padding: 30px;
            text-align: center;
            font-size: 14px;
        }
        
        .footer strong {
            color: #ffffff;
        }
        
        .priority-badge {
            display: inline-block;
            background: #ef4444;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🎯 Neue Gesprächsanfrage</h1>
            <p>Ein potenzieller Kunde möchte ein Strategiegespräch vereinbaren</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="priority-badge">⚡ Priorität: Hoch</div>
            
            <!-- Kontaktdaten -->
            <div class="section">
                <h2 class="section-title">
                    <span class="section-icon">👤</span>
                    Kontaktdaten
                </h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Vollständiger Name</div>
                        <div class="info-value">' . htmlspecialchars($data['vorname']) . ' ' . htmlspecialchars($data['nachname']) . '</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">E-Mail-Adresse</div>
                        <div class="info-value">
                            <a href="mailto:' . htmlspecialchars($data['email']) . '" style="color: #3b82f6; text-decoration: none;">
                                ' . htmlspecialchars($data['email']) . '
                            </a>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Telefonnummer</div>
                        <div class="info-value">
                            <a href="tel:' . htmlspecialchars($data['telefon']) . '" style="color: #3b82f6; text-decoration: none;">
                                ' . htmlspecialchars($data['telefon']) . '
                            </a>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Position</div>
                        <div class="info-value">' . htmlspecialchars($data['position']) . '</div>
                    </div>
                </div>
            </div>
            
            <!-- Unternehmensdaten -->
            <div class="section">
                <h2 class="section-title">
                    <span class="section-icon">🏢</span>
                    Unternehmen
                </h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Unternehmensname</div>
                        <div class="info-value">' . htmlspecialchars($data['unternehmen']) . '</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Branche</div>
                        <div class="info-value">' . htmlspecialchars($branche_display) . '</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Anzahl Mitarbeiter</div>
                        <div class="info-value">' . htmlspecialchars($mitarbeiter_display) . '</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Monatliches Budget</div>
                        <div class="info-value">' . htmlspecialchars($budget_display) . '</div>
                    </div>
                </div>
            </div>
            
            <!-- Herausforderungen & Ziele -->
            <div class="section">
                <h2 class="section-title">
                    <span class="section-icon">🎯</span>
                    Hauptziele & Herausforderungen
                </h2>
                <div class="info-item full-width">
                    <div class="info-label">Gewählte Ziele</div>
                    <div class="tags">';
                    
                    if (!empty($data['ziele'])) {
                        foreach ($data['ziele'] as $ziel) {
                            $ziel_labels = [
                                'leadgenerierung' => 'Leadgenerierung steigern',
                                'umsatz' => 'Umsatz erhöhen',
                                'reichweite' => 'Reichweite ausbauen',
                                'conversion' => 'Conversion optimieren',
                                'marke' => 'Marke stärken',
                                'automatisierung' => 'Prozesse automatisieren'
                            ];
                            $ziel_display = $ziel_labels[$ziel] ?? ucfirst($ziel);
                            $html .= '<span class="tag">' . htmlspecialchars($ziel_display) . '</span>';
                        }
                    }
                    
                $html .= '</div>
                </div>';
                
                if (!empty($data['beschreibung'])) {
                    $html .= '
                <div class="text-content">
                    <div class="info-label" style="margin-bottom: 10px;">AKTUELLE SITUATION</div>
                    <p>' . nl2br(htmlspecialchars($data['beschreibung'])) . '</p>
                </div>';
                }
                
            $html .= '</div>
            
            <!-- Erfahrung & Interessen -->
            <div class="section">
                <h2 class="section-title">
                    <span class="section-icon">📊</span>
                    Erfahrung & Interessen
                </h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Social Media Erfahrung</div>
                        <div class="info-value">' . htmlspecialchars($erfahrung_display) . '</div>
                    </div>
                </div>';
                
                if (!empty($data['plattformen'])) {
                    $html .= '
                <div class="info-item full-width">
                    <div class="info-label">Aktive Plattformen</div>
                    <div class="tags">';
                    foreach ($data['plattformen'] as $plattform) {
                        $html .= '<span class="tag">' . htmlspecialchars(ucfirst($plattform)) . '</span>';
                    }
                    $html .= '</div>
                </div>';
                }
                
                if (!empty($data['leistungen_interesse'])) {
                    $html .= '
                <div class="info-item full-width">
                    <div class="info-label">Interessante Leistungen</div>
                    <div class="tags">';
                    foreach ($data['leistungen_interesse'] as $leistung) {
                        $leistung_labels = [
                            'social_media_ads' => 'Social Media Advertising',
                            'content_creation' => 'Content Creation',
                            'strategy' => 'Marketing Strategie',
                            'automation' => 'Marketing Automation',
                            'analytics' => 'Analytics & Tracking',
                            'beratung' => 'Beratung & Coaching'
                        ];
                        $leistung_display = $leistung_labels[$leistung] ?? ucfirst($leistung);
                        $html .= '<span class="tag">' . htmlspecialchars($leistung_display) . '</span>';
                    }
                    $html .= '</div>
                </div>';
                }
                
                if (!empty($data['bisherige_versuche'])) {
                    $html .= '
                <div class="text-content">
                    <div class="info-label" style="margin-bottom: 10px;">BISHERIGE MARKETING-VERSUCHE</div>
                    <p>' . nl2br(htmlspecialchars($data['bisherige_versuche'])) . '</p>
                </div>';
                }
                
                if (!empty($data['sonstiges'])) {
                    $html .= '
                <div class="text-content">
                    <div class="info-label" style="margin-bottom: 10px;">ZUSÄTZLICHE INFORMATIONEN</div>
                    <p>' . nl2br(htmlspecialchars($data['sonstiges'])) . '</p>
                </div>';
                }
                
            $html .= '</div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>📧 Gesendet über das Peakmont Gesprächsformular</strong></p>
            <p>Eingegangen am: ' . date('d.m.Y H:i:s') . ' Uhr</p>
            <p style="margin-top: 15px; opacity: 0.7;">
                💡 <strong>Tipp:</strong> Antworten Sie schnell auf diese Anfrage, um die Conversion-Rate zu maximieren!
            </p>
        </div>
    </div>
</body>
</html>';
    
    return $html;
}

/**
 * E-Mail-Text-Version generieren (für bessere Zustellbarkeit)
 */
function generateEmailText($data) {
    $text = "🎯 NEUE GESPRÄCHSANFRAGE - PEAKMONT\n";
    $text .= "=======================================\n\n";
    
    $text .= "👤 KONTAKTDATEN:\n";
    $text .= "Name: " . $data['vorname'] . " " . $data['nachname'] . "\n";
    $text .= "E-Mail: " . $data['email'] . "\n";
    $text .= "Telefon: " . $data['telefon'] . "\n";
    $text .= "Position: " . $data['position'] . "\n\n";
    
    $text .= "🏢 UNTERNEHMEN:\n";
    $text .= "Name: " . $data['unternehmen'] . "\n";
    $text .= "Branche: " . $data['branche'] . "\n";
    $text .= "Mitarbeiter: " . ($data['mitarbeiter'] ?: 'Nicht angegeben') . "\n";
    $text .= "Budget: " . ($data['budget'] ?: 'Nicht angegeben') . "\n\n";
    
    $text .= "🎯 ZIELE:\n";
    if (!empty($data['ziele'])) {
        foreach ($data['ziele'] as $ziel) {
            $text .= "- " . ucfirst($ziel) . "\n";
        }
    }
    $text .= "\n";
    
    if (!empty($data['beschreibung'])) {
        $text .= "📝 AKTUELLE SITUATION:\n";
        $text .= $data['beschreibung'] . "\n\n";
    }
    
    $text .= "📊 ERFAHRUNG:\n";
    $text .= "Social Media: " . ($data['social_media_erfahrung'] ?: 'Nicht angegeben') . "\n";
    
    if (!empty($data['plattformen'])) {
        $text .= "Plattformen: " . implode(', ', $data['plattformen']) . "\n";
    }
    
    if (!empty($data['leistungen_interesse'])) {
        $text .= "Interesse an: " . implode(', ', $data['leistungen_interesse']) . "\n";
    }
    
    if (!empty($data['bisherige_versuche'])) {
        $text .= "\n🔄 BISHERIGE VERSUCHE:\n";
        $text .= $data['bisherige_versuche'] . "\n";
    }
    
    if (!empty($data['sonstiges'])) {
        $text .= "\n💭 SONSTIGES:\n";
        $text .= $data['sonstiges'] . "\n";
    }
    
    $text .= "\n=======================================\n";
    $text .= "Eingegangen am: " . date('d.m.Y H:i:s') . " Uhr\n";
    $text .= "Über: Peakmont Gesprächsformular";
    
    return $text;
}

/**
 * E-Mail senden mit SMTP
 */
function sendEmail($html_content, $text_content, $data, $smtp_config, $recipient_email, $recipient_name, $sender_email, $sender_name) {
    
    // Betreff
    $subject = "🎯 Neue Gesprächsanfrage von " . $data['vorname'] . " " . $data['nachname'] . " (" . $data['unternehmen'] . ")";
    
    // E-Mail-Headers
    $headers = [];
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-Type: multipart/alternative; boundary=\"boundary-" . md5(time()) . "\"";
    $headers[] = "From: " . $sender_name . " <" . $sender_email . ">";
    $headers[] = "Reply-To: " . $data['email'];
    $headers[] = "X-Mailer: PHP/" . phpversion();
    $headers[] = "X-Priority: 1";
    
    // Multipart-Nachricht erstellen
    $boundary = "boundary-" . md5(time());
    
    $message = "--" . $boundary . "\r\n";
    $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $message .= $text_content . "\r\n\r\n";
    
    $message .= "--" . $boundary . "\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\r\n";
    $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $message .= $html_content . "\r\n\r\n";
    
    $message .= "--" . $boundary . "--\r\n";
    
    // Versuche PHPMailer zu laden und zu verwenden
    try {
        require_once 'phpmailer/Exception.php';
        require_once 'phpmailer/PHPMailer.php';
        require_once 'phpmailer/SMTP.php';
        
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            return sendWithPHPMailer($subject, $html_content, $text_content, $data, $smtp_config, $recipient_email, $recipient_name, $sender_email, $sender_name);
        }
    } catch (Exception $e) {
        global $errors;
        $errors[] = "❌ Fehler beim Laden von PHPMailer: " . $e->getMessage();
    }
    
    // Fallback auf mail() Funktion
    global $errors;
    $errors[] = "⚠️ PHPMailer nicht verfügbar, verwende mail() Funktion";
    return mail($recipient_email, $subject, $message, implode("\r\n", $headers));
}

/**
 * E-Mail mit PHPMailer senden (empfohlen für SMTP)
 */
function sendWithPHPMailer($subject, $html_content, $text_content, $data, $smtp_config, $recipient_email, $recipient_name, $sender_email, $sender_name) {
    global $errors; // Zugriff auf globale $errors Variable
    
    try {
        require_once 'phpmailer/Exception.php';
        require_once 'phpmailer/PHPMailer.php';
        require_once 'phpmailer/SMTP.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Debug-Modus aktivieren für detaillierte Fehleranalyse
        $mail->SMTPDebug = 0; // Temporär ausschalten für saubere Ausgabe
        $mail->Debugoutput = function($str, $level) {
            global $errors;
            $errors[] = "SMTP: " . trim($str);
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
        
        // Timeout erhöhen für langsame Server
        $mail->Timeout = 60;
        $mail->SMTPKeepAlive = true;
        
        // Absender und Empfänger
        $mail->setFrom($sender_email, $sender_name);
        $mail->addAddress($recipient_email, $recipient_name);
        $mail->addReplyTo($data['email'], $data['vorname'] . ' ' . $data['nachname']);
        
        // E-Mail-Inhalt
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html_content;
        $mail->AltBody = $text_content;
        
        // Priorität
        $mail->Priority = 1;
        
        // Debug-Info hinzufügen
        $errors[] = "📧 Versuche E-Mail zu senden an: " . $recipient_email;
        $errors[] = "📡 SMTP Server: " . $smtp_config['host'] . ":" . $smtp_config['port'];
        $errors[] = "🔐 Benutzer: " . $smtp_config['username'];
        
        $send_result = $mail->send();
        
        if ($send_result) {
            // Erfolg-Info
            $errors[] = "✅ E-Mail erfolgreich gesendet!";
            return true;
        } else {
            $errors[] = "❌ mail->send() gab false zurück";
            return false;
        }
        
    } catch (Exception $e) {
        // Detaillierte Fehlermeldung
        $errors[] = "❌ PHPMailer Fehler: " . $e->getMessage();
        $errors[] = "🔍 Fehler-Code: " . $e->getCode();
        $errors[] = "📁 Datei: " . $e->getFile() . " (Zeile " . $e->getLine() . ")";
        
        // Spezifische SMTP-Fehler
        if (strpos($e->getMessage(), 'SMTP connect()') !== false) {
            $errors[] = "🌐 Verbindungsproblem: Kann keine Verbindung zum SMTP-Server herstellen";
            $errors[] = "💡 Mögliche Ursachen: Falscher Server, Port blockiert, Firewall";
        }
        
        if (strpos($e->getMessage(), 'SMTP Auth') !== false || strpos($e->getMessage(), 'authentication') !== false) {
            $errors[] = "🔑 Authentifizierungsfehler: Benutzername oder Passwort falsch";
            $errors[] = "💡 Überprüfen Sie die SMTP-Anmeldedaten in der Konfiguration";
        }
        
        if (strpos($e->getMessage(), 'certificate') !== false || strpos($e->getMessage(), 'SSL') !== false) {
            $errors[] = "🔒 SSL/TLS-Zertifikatsproblem";
            $errors[] = "💡 Versuchen Sie 'ssl' statt 'tls' oder deaktivieren Sie die Zertifikatsprüfung";
        }
        
        error_log("PHPMailer Error: " . $e->getMessage());
        return false;
    }
}

// JSON-Response für AJAX-Anfragen
if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
    header('Content-Type: application/json');
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Ihre Anfrage wurde erfolgreich gesendet! Wir melden uns in Kürze bei Ihnen.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'errors' => $errors
        ]);
    }
    exit;
}

// HTML-Antwort für normale Form-Submits
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $success ? 'Anfrage gesendet' : 'Fehler'; ?> - Peakmont</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <?php if ($success): ?>
            <div class="text-center">
                <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Vielen Dank!</h1>
                <p class="text-gray-600 mb-6">Ihre Gesprächsanfrage wurde erfolgreich gesendet. Wir melden uns in Kürze bei Ihnen, um einen Termin zu vereinbaren.</p>
                <a href="index.html" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Zurück zur Startseite
                </a>
            </div>
        <?php else: ?>
            <div class="text-center">
                <div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Fehler</h1>
                <div class="text-red-600 mb-6">
                    <ul class="list-disc list-inside space-y-1">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button onclick="history.back()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Zurück zum Formular
                </button>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
