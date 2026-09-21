<?php
/**
 * Cron Runner - Simple HTTP endpoint pour exécuter fx_fetch_daily
 * À placer dans le répertoire racine de l'application
 */

// Load .env (CI4 is not bootstrapped here)
$envFile = __DIR__ . DIRECTORY_SEPARATOR . '.env';
if (is_readable($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
            continue;
        }
        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        $value = trim($value, "\"'");
        if ($name !== '' && getenv($name) === false) {
            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
        }
    }
}

$valid_token = (string) (getenv('cron.token') ?: ($_ENV['cron.token'] ?? ''));
$app_path = __DIR__;

header('Content-Type: application/json');

$token = $_GET['token'] ?? '';
$action = $_GET['action'] ?? '';

if ($valid_token === '' || $token === '' || !hash_equals($valid_token, (string) $token)) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Token invalide',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

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

$command = 'cd ' . escapeshellarg($app_path) . ' && php index.php cron fx_fetch_daily 2>&1';
$start_time = microtime(true);

exec($command, $output, $return_code);

$execution_time = round(microtime(true) - $start_time, 2);
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

    $output_text = implode("\n", $output);
    if (preg_match('/Rates:\s*(.+?)(?:\n\n|$)/s', $output_text, $matches)) {
        $rates_text = trim($matches[1]);
        $rates = [];
        foreach (explode("\n", $rates_text) as $line) {
            if (preg_match('/(\w+):\s*([\d.]+)/', $line, $rate_match)) {
                $rates[$rate_match[1]] = (float) $rate_match[2];
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

$response['output'] = array_slice($output, 0, 20);

echo json_encode($response, JSON_PRETTY_PRINT);
