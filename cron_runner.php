<?php
/**
 * Cron Runner - Simple HTTP endpoint pour exécuter fx_fetch_daily
 * À placer dans le répertoire racine de l'application
 */

// Configuration
$valid_token = '147489da98c2c7c3b55045c29b453886e34b80c72f6a68dd6caae2689b327e4d';
$app_path = __DIR__; // Répertoire courant

// Headers pour JSON
header('Content-Type: application/json');

// Récupération du token
$token = $_GET['token'] ?? '';
$action = $_GET['action'] ?? '';

// Validation du token
if (empty($token) || $token !== $valid_token) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Token invalide',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// Validation de l'action
if ($action !== 'fx_fetch_daily') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Action non supportée',
        'available_actions' => ['fx_fetch_daily'],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// Exécution de la commande
$command = "cd $app_path && php index.php cron fx_fetch_daily 2>&1";
$start_time = microtime(true);

exec($command, $output, $return_code);

$execution_time = round(microtime(true) - $start_time, 2);

// Analyse du résultat
$success = $return_code === 0 && strpos(implode("\n", $output), '[OK]') !== false;

$response = [
    'success' => $success,
    'action' => $action,
    'execution_time' => $execution_time . 's',
    'return_code' => $return_code,
    'timestamp' => date('Y-m-d H:i:s')
];

if ($success) {
    http_response_code(200);
    $response['message'] = 'Tâche exécutée avec succès';

    // Extraire les taux si disponibles
    $output_text = implode("\n", $output);
    if (preg_match('/Rates:\s*(.+?)(?:\n\n|\$)/s', $output_text, $matches)) {
        $rates_text = trim($matches[1]);
        $rates = [];
        foreach (explode("\n", $rates_text) as $line) {
            if (preg_match('/(\w+):\s*([\d.]+)/', $line, $rate_match)) {
                $rates[$rate_match[1]] = (float)$rate_match[2];
            }
        }
        if (!empty($rates)) {
            $response['rates'] = $rates;
        }
    }
} else {
    http_response_code(500);
    $response['message'] = 'Échec de l\'exécution';
}

$response['output'] = array_slice($output, 0, 20); // Limiter la sortie

echo json_encode($response, JSON_PRETTY_PRINT);
?>
