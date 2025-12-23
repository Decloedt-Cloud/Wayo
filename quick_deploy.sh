#!/bin/bash

echo "=== DÉPLOIEMENT RAPIDE DU CRON RUNNER ==="
echo ""

# Token correct
TOKEN="147489da98c2c7c3b55045c29b453886e34b80c72f6a68dd6caae2689b327e4d"

echo "Token: $TOKEN"
echo ""

# Créer le cron runner avec le bon token
cat > /tmp/cron_runner_final.php << EOF
<?php
\$valid_token = '$TOKEN';
\$app_path = __DIR__;

header('Content-Type: application/json');

\$token = \$_GET['token'] ?? '';
\$action = \$_GET['action'] ?? '';

if (empty(\$token) || \$token !== \$valid_token) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Token invalide', 'timestamp' => date('Y-m-d H:i:s')]);
    exit;
}

if (\$action !== 'fx_fetch_daily') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Action non supportée', 'available_actions' => ['fx_fetch_daily'], 'timestamp' => date('Y-m-d H:i:s')]);
    exit;
}

\$command = "cd \$app_path && php index.php cron fx_fetch_daily 2>&1";
\$start_time = microtime(true);

exec(\$command, \$output, \$return_code);
\$execution_time = round(microtime(true) - \$start_time, 2);

\$success = \$return_code === 0 && strpos(implode("\n", \$output), '[OK]') !== false;

\$response = [
    'success' => \$success,
    'action' => \$action,
    'execution_time' => \$execution_time . 's',
    'return_code' => \$return_code,
    'timestamp' => date('Y-m-d H:i:s')
];

if (\$success) {
    http_response_code(200);
    \$response['message'] = 'Tâche exécutée avec succès';
} else {
    http_response_code(500);
    \$response['message'] = 'Échec de l\'exécution';
}

\$response['output'] = array_slice(\$output, 0, 10);
echo json_encode(\$response, JSON_PRETTY_PRINT);
?>
EOF

echo "Cron runner créé localement"
echo ""

# Transfert et déploiement
echo "Transfert vers le serveur..."
scp /tmp/cron_runner_final.php ubuntu@ip-172-31-7-57:/tmp/ 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✅ Transfert réussi"

    ssh ubuntu@ip-172-31-7-57 << 'EOF'
        echo "Déploiement sur le serveur..."
        cp /tmp/cron_runner_final.php /var/www/html/School-Management-De/cron_runner.php
        chmod 644 /var/www/html/School-Management-De/cron_runner.php
        chown www-data:www-data /var/www/html/School-Management-De/cron_runner.php
        echo "✅ Cron runner déployé"

        # Test local
        echo "Test local..."
        RESPONSE=$(curl -s "http://localhost/School-Management-De/cron_runner.php?token=147489da98c2c7c3b55045c29b453886e34b80c72f6a68dd6caae2689b327e4d&action=fx_fetch_daily" | head -3)
        if echo "$RESPONSE" | grep -q "success.*true"; then
            echo "✅ Test local réussi"
        else
            echo "⚠️ Test local: $RESPONSE"
        fi
EOF

    echo ""
    echo "=== TEST FINAL ==="
    TEST_URL="https://preprod.wayo.site/cron_runner.php?token=$TOKEN&action=fx_fetch_daily"
    echo "URL de test: $TEST_URL"
    echo ""

    # Test externe
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$TEST_URL")
    RESPONSE=$(curl -s "$TEST_URL")

    echo "Code HTTP: $HTTP_CODE"
    echo "Réponse: $RESPONSE"

    if [ "$HTTP_CODE" = "200" ] && echo "$RESPONSE" | grep -q "success.*true"; then
        echo ""
        echo "🎉 SUCCÈS! CRON RUNNER OPÉRATIONNEL"
        echo ""
        echo "📋 URL POUR CRON-JOB.ORG:"
        echo "$TEST_URL"
        echo ""
        echo "⚙️ CONFIGURATION CRON-JOB.ORG:"
        echo "Title: FX Rates Daily Update"
        echo "URL: $TEST_URL"
        echo "Method: GET"
        echo "Schedule: Daily at 02:00"
    else
        echo "❌ Échec - Code HTTP: $HTTP_CODE"
        echo "Réponse complète:"
        echo "$RESPONSE"
    fi

else
    echo "❌ Échec du transfert SSH"
    echo ""
    echo "📋 DÉPLOIEMENT MANUEL:"
    echo ""
    echo "1. Copiez le contenu de cron_runner.php sur votre serveur"
    echo "2. Placez-le dans: /var/www/html/School-Management-De/cron_runner.php"
    echo "3. Donnez les permissions: chmod 644 cron_runner.php"
    echo ""
    echo "4. Testez: https://preprod.wayo.site/cron_runner.php?token=$TOKEN&action=fx_fetch_daily"
fi

# Nettoyage
rm -f /tmp/cron_runner_final.php

echo ""
echo "=== DÉPLOIEMENT TERMINÉ ==="
