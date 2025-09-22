<?php
// =====================================
// EINFACHE EMAIL-VERSION OHNE PHPMAILER
// =====================================

// Nur POST-Requests erlauben
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Nur POST-Requests erlaubt']);
    exit;
}

// Input validieren
$required_fields = ['name', 'email', 'phone', 'message'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Feld '$field' ist erforderlich"]);
        exit;
    }
}

// E-Mail validieren
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Ungültige E-Mail-Adresse']);
    exit;
}

// Daten bereinigen
$name = htmlspecialchars($_POST['name']);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars($_POST['phone']);
$company = isset($_POST['company']) ? htmlspecialchars($_POST['company']) : '';
$website = isset($_POST['website']) ? htmlspecialchars($_POST['website']) : '';
$goals = isset($_POST['goals']) ? $_POST['goals'] : [];
$budget = isset($_POST['budget']) ? htmlspecialchars($_POST['budget']) : '';
$timeline = isset($_POST['timeline']) ? htmlspecialchars($_POST['timeline']) : '';
$message = htmlspecialchars($_POST['message']);

// E-Mail-Konfiguration
$to = 'tobias.westrich@gmx.de';  // ← IHRE E-MAIL HIER EINGEBEN
$subject = '🎯 Neue Strategiegespräch-Anfrage von ' . $name;

// E-Mail-Inhalt erstellen
$email_content = "NEUE STRATEGIEGESPRÄCH-ANFRAGE\n";
$email_content .= "================================\n\n";
$email_content .= "Kontaktdaten:\n";
$email_content .= "Name: $name\n";
$email_content .= "E-Mail: $email\n";
$email_content .= "Telefon: $phone\n";

if (!empty($company)) {
    $email_content .= "Unternehmen: $company\n";
}

if (!empty($website)) {
    $email_content .= "Website: $website\n";
}

$email_content .= "\nProjektdetails:\n";

if (!empty($goals)) {
    $email_content .= "Ziele:\n";
    foreach ($goals as $goal) {
        $email_content .= "- " . htmlspecialchars($goal) . "\n";
    }
}

if (!empty($budget)) {
    $email_content .= "Budget: $budget\n";
}

if (!empty($timeline)) {
    $email_content .= "Zeitrahmen: $timeline\n";
}

$email_content .= "\nNachricht:\n";
$email_content .= "$message\n\n";
$email_content .= "================================\n";
$email_content .= "Gesendet über Peakmont Kontaktformular\n";
$email_content .= "Zeitpunkt: " . date('d.m.Y H:i:s') . "\n";

// E-Mail-Header
$headers = array(
    'From' => $email,
    'Reply-To' => $email,
    'Return-Path' => $email,
    'X-Mailer' => 'PHP/' . phpversion(),
    'Content-Type' => 'text/plain; charset=UTF-8'
);

// Header-String erstellen
$header_string = '';
foreach ($headers as $key => $value) {
    $header_string .= $key . ': ' . $value . "\r\n";
}

// E-Mail senden
if (mail($to, $subject, $email_content, $header_string)) {
    echo json_encode([
        'success' => true, 
        'message' => 'Vielen Dank! Ihre Anfrage wurde erfolgreich gesendet.'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Fehler beim Senden der E-Mail. Bitte versuchen Sie es später erneut.'
    ]);
}
?>
