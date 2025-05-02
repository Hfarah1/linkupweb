<?php

require_once __DIR__.'/vendor/autoload.php';

use App\Service\RtcTokenBuilder2;

// Configuration Agora
$appId = "788fe3b38e104edba009da276cb137ca";
// Remplacez ceci par votre App Certificate de la console Agora
$appCertificate = "VOTRE_APP_CERTIFICATE";
$channelName = "linkup";
$uid = 0;
$role = 1; // 1 = Publisher
$expireTimeInSeconds = 365 * 24 * 3600; // 1 an
$currentTimestamp = time();
$privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

// Générer le token
$token = RtcTokenBuilder2::buildTokenWithUid(
    $appId,
    $appCertificate,
    $channelName,
    $uid,
    $role,
    $privilegeExpiredTs
);

echo "Token généré : " . $token . "\n"; 