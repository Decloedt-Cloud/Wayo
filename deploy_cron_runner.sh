#!/bin/bash

echo "=== DÉPLOIEMENT CRON RUNNER POUR FX_FETCH_DAILY ==="
echo ""

# Configuration
TOKEN="147489da98c2c7c3b55045c29b453886e34b80c72f6a68dd6caae2689b327e4d"

echo "Token utilisé: $TOKEN"
echo ""

# Créer un fichier temporaire avec le contenu du cron runner
cat > /tmp/cron_runner_temp.php << 'EOF'
<?php
/**
 * Cron Runner - Simple HTTP endpoint pour exécuter fx_fetch_daily
 */

// Configuration
$valid_token = 'TOKEN_PLACEHOLDER';
$app_path = __DIR__;

// Headers
header('Content-Type: application/json');

$token = $_GET['token'] ?? '';
$action = $_GET['action'] ?? '';

if (empty($token) || $token !== $valid_token) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Token invalide', 'timestamp' => date('Y-m-d H:i:s')]);
    exit;
}

if ($action !== 'fx_fetch_daily') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Action non supportée', 'available_actions' => ['fx_fetch_daily'], 'timestamp' => date('Y-m-d H:i:s')]);
    exit;
}

$command = "cd $app_path && php index.php cron fx_fetch_daily 2>&1";
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

$response['output'] = array_slice($output, 0, 20);
echo json_encode($response, JSON_PRETTY_PRINT);
?>
EOF

# Remplacer le placeholder du token
sed -i "s/TOKEN_PLACEHOLDER/$TOKEN/" /tmp/cron_runner_temp.php

# Déploiement sur le serveur
echo "Déploiement sur le serveur..."
echo "Tentative de connexion SSH..."

# Essayer différentes méthodes de connexion
if ssh -o ConnectTimeout=10 ubuntu@ip-172-31-7-57 "echo 'SSH OK'" 2>/dev/null; then
    echo "✅ Connexion SSH réussie"

    # Transfert du fichier
    scp /tmp/cron_runner_temp.php ubuntu@ip-172-31-7-57:/tmp/cron_runner.php

    # Déploiement
    ssh ubuntu@ip-172-31-7-57 << EOF
        echo "Déploiement du cron runner..."
        cp /tmp/cron_runner.php /var/www/html/School-Management-De/cron_runner.php
        chmod 644 /var/www/html/School-Management-De/cron_runner.php
        chown www-data:www-data /var/www/html/School-Management-De/cron_runner.php
        echo "✅ Cron runner déployé"

        # Test rapide
        echo "Test du cron runner..."
        RESPONSE=\$(curl -s "http://localhost/School-Management-De/cron_runner.php?token=$TOKEN&action=fx_fetch_daily" | head -5)
        if echo "\$RESPONSE" | grep -q "success.*true"; then
            echo "✅ Cron runner fonctionnel en local"
        else
            echo "⚠️ Test local: \$RESPONSE"
        fi
EOF

    echo ""
    echo "=== TEST FINAL ==="
    CRON_URL="https://preprod.wayo.site/cron_runner.php?token=$TOKEN&action=fx_fetch_daily"
    echo "URL à utiliser: $CRON_URL"
    echo ""

    # Test externe
    echo "Test externe..."
    HTTP_CODE=\$(curl -s -o /dev/null -w "%{http_code}" "$CRON_URL")
    echo "Code HTTP: \$HTTP_CODE"

    if [ "\$HTTP_CODE" = "200" ]; then
        echo "✅ CRON RUNNER OPÉRATIONNEL!"
        echo ""
        echo "🎉 URL PRÊTE POUR CRON-JOB.ORG:"
        echo "\$CRON_URL"
        echo ""
        echo "📋 Configuration cron-job.org:"
        echo "   Title: FX Rates Daily Update"
        echo "   URL: \$CRON_URL"
        echo "   Method: GET"
        echo "   Schedule: Daily at 02:00"
    else
        echo "❌ Code HTTP: \$HTTP_CODE"
        echo "Vérifiez les logs du serveur"
    fi

else
    echo "❌ Connexion SSH échouée"
    echo ""
    echo "📋 INSTRUCTIONS MANUELLES:"
    echo ""
    echo "1. Copiez le contenu de cron_runner.php sur votre serveur"
    echo "2. Placez-le dans: /var/www/html/School-Management-De/cron_runner.php"
    echo "3. Rendez-le exécutable: chmod 644 cron_runner.php"
    echo ""
    echo "4. Testez l'URL:"
    echo "   https://preprod.wayo.site/cron_runner.php?token=$TOKEN&action=fx_fetch_daily"
fi

# Nettoyage
rm -f /tmp/cron_runner_temp.php

echo ""
echo "=== DÉPLOIEMENT TERMINÉ ==="
