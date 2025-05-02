<?php

namespace App\Service;

use Agora\RtcTokenBuilder2;

class AgoraTokenService
{
    private const APP_ID = "788fe3b38e104edba009da276cb137ca";
    private const APP_CERTIFICATE = "fe3b436f1d074e469b4d3ed84df63c9a";
    private const CHANNEL_NAME = "linkup";

    public function __construct()
    {
        // Le certificat est maintenant une constante
    }

    public function generateToken(int $uid = 0): string
    {
        try {
            // Token avec une durée de validité plus courte (30 minutes)
            $privilegeExpiredTs = time() + (30 * 60);

            // Générer le token
            $token = RtcTokenBuilder2::buildTokenWithUid(
                self::APP_ID,
                self::APP_CERTIFICATE,
                self::CHANNEL_NAME,
                $uid,
                RtcTokenBuilder2::Role_Publisher,
                $privilegeExpiredTs
            );

            // Log pour debug
            error_log("Token généré: " . $token);
            error_log("Expire dans: " . date('Y-m-d H:i:s', $privilegeExpiredTs));

            return $token;
        } catch (\Exception $e) {
            error_log("Erreur de génération du token Agora: " . $e->getMessage());
            throw new \Exception("Impossible de générer le token Agora: " . $e->getMessage());
        }
    }

    public function getChannelName(): string
    {
        return self::CHANNEL_NAME;
    }

    public function getAppId(): string
    {
        return self::APP_ID;
    }
} 