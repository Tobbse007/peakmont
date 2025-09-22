<?php
// Direkte Formular-Simulation für Debug
echo "<h1>🧪 Formular-Debug-Test</h1>";
echo "<style>body{font-family:Arial;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";

// Simuliere Formular-POST-Daten
$_POST = [
    'vorname' => 'Test',
    'nachname' => 'Benutzer',
    'email' => 'test@example.com',
    'telefon' => '0123456789',
    'unternehmen' => 'Test GmbH',
    'branche' => 'tech',
    'position' => 'Manager',
    'mitarbeiter' => '10-50',
    'ziele' => ['umsatz_steigern'],
    'budget' => '5000-10000',
    'beschreibung' => 'Test-Beschreibung',
    'social_media_erfahrung' => 'wenig',
    'plattformen' => ['linkedin'],
    'leistungen_interesse' => ['lead_generation'],
    'bisherige_versuche' => 'Keine',
    'sonstiges' => 'Test'
];

$_SERVER['REQUEST_METHOD'] = 'POST';

echo "<h2>📋 Simulierte POST-Daten:</h2>";
echo "<pre style='background:#f0f0f0;padding:10px;'>";
print_r($_POST);
echo "</pre>";

echo "<h2>🔧 Test startet...</h2>";

// Lade die originale send_gespraech.php und führe sie aus
ob_start();
include 'send_gespraech.php';
$output = ob_get_clean();

echo "<h2>📤 Ausgabe:</h2>";
echo "<div style='background:#f0f0f0;padding:10px;'>";
echo $output;
echo "</div>";

echo "<hr>";
echo "<p><a href='gespraech.html'>← Zurück zum Formular</a></p>";
?>
