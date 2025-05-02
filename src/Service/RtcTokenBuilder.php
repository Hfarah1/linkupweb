<?php

namespace App\Service;

class RtcTokenBuilder
{
    const ROLE_PUBLISHER = 1;
    const ROLE_SUBSCRIBER = 2;

    const PRIVILEGE_JOIN_CHANNEL = 1;
    const PRIVILEGE_PUBLISH_AUDIO_STREAM = 2;
    const PRIVILEGE_PUBLISH_VIDEO_STREAM = 3;
    const PRIVILEGE_PUBLISH_DATA_STREAM = 4;

    public static function buildTokenWithUid(
        string $appId,
        string $appCertificate,
        string $channelName,
        int $uid,
        int $role,
        int $privilegeExpiredTs
    ): string {
        $token = AccessToken::init($appId, $appCertificate, $channelName, $uid);
        $token->addPrivilege(self::PRIVILEGE_JOIN_CHANNEL, $privilegeExpiredTs);
        if ($role == self::ROLE_PUBLISHER) {
            $token->addPrivilege(self::PRIVILEGE_PUBLISH_AUDIO_STREAM, $privilegeExpiredTs);
            $token->addPrivilege(self::PRIVILEGE_PUBLISH_VIDEO_STREAM, $privilegeExpiredTs);
            $token->addPrivilege(self::PRIVILEGE_PUBLISH_DATA_STREAM, $privilegeExpiredTs);
        }
        return $token->build();
    }
}

class AccessToken
{
    const VERSION = "006";
    const VERSION_LENGTH = 3;
    
    public $appID;
    public $appCertificate;
    public $channelName;
    public $uid;
    public $message;

    public function __construct()
    {
        $this->message = new Message();
    }

    public static function init(string $appID, string $appCertificate, string $channelName, int $uid): self
    {
        $accessToken = new AccessToken();
        $accessToken->appID = $appID;
        $accessToken->appCertificate = $appCertificate;
        $accessToken->channelName = $channelName;
        $accessToken->uid = $uid;
        return $accessToken;
    }

    public function addPrivilege(int $privilege, int $expireTimestamp): void
    {
        $this->message->privileges[$privilege] = $expireTimestamp;
    }

    public function build(): string
    {
        $this->message->salt = rand(0, 100000);
        $this->message->ts = time() + 24 * 3600;

        $buffer = Buffer::pack("S", self::VERSION_LENGTH);
        $buffer .= Buffer::pack("S", strlen(self::VERSION));
        $buffer .= self::VERSION;
        
        $content = Buffer::pack("S", strlen($this->appID));
        $content .= $this->appID;
        $content .= Buffer::pack("S", strlen($this->channelName));
        $content .= $this->channelName;
        $content .= Buffer::pack("I", $this->uid);
        $content .= Buffer::pack("I", $this->message->ts);
        $content .= Buffer::pack("I", $this->message->salt);
        $content .= Buffer::pack("S", 0);

        $signature = hash_hmac("sha256", $content, $this->appCertificate, true);

        $buffer .= Buffer::pack("S", strlen($signature));
        $buffer .= $signature;
        $buffer .= $content;

        return base64_encode($buffer);
    }
}

class Message
{
    public $salt;
    public $ts;
    public $privileges = [];

    public function __construct()
    {
        $this->salt = rand(0, 100000);
    }
}

class Buffer
{
    public static function pack(string $format, $data): string
    {
        return pack($format, $data);
    }
} 