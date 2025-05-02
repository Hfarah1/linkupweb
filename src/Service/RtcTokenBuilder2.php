<?php

namespace App\Service;

class RtcTokenBuilder2
{
    const Role_Publisher = 1;
    const Role_Subscriber = 2;
    const Role_Admin = 101;

    const PrivilegeJoinChannel = 1;
    const PrivilegePublishAudioStream = 2;
    const PrivilegePublishVideoStream = 3;
    const PrivilegePublishDataStream = 4;

    public static function buildTokenWithUid($appId, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs)
    {
        $token = new AgoraAccessToken();
        $token->appID = $appId;
        $token->appCertificate = $appCertificate;
        $token->channelName = $channelName;
        $token->uid = (string)$uid;
        $token->expire = $privilegeExpiredTs;

        return $token->build();
    }

    public static function buildTokenWithUserAccount($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs)
    {
        $token = new AccessToken();
        $token->appID = $appID;
        $token->appCertificate = $appCertificate;
        $token->channelName = $channelName;
        $token->uid = $uid;
        $token->messages = array();

        $message = new Message();
        $message->salt = rand(0, 100000);
        $message->ts = time() + 24 * 3600;
        $message->privileges = array();

        $message->privileges[AccessToken::Privileges["kJoinChannel"]] = $privilegeExpiredTs;
        if ($role == RtcTokenBuilder2::Role_Publisher || $role == RtcTokenBuilder2::Role_Admin) {
            $message->privileges[AccessToken::Privileges["kPublishVideoStream"]] = $privilegeExpiredTs;
            $message->privileges[AccessToken::Privileges["kPublishAudioStream"]] = $privilegeExpiredTs;
            $message->privileges[AccessToken::Privileges["kPublishDataStream"]] = $privilegeExpiredTs;
        }

        $token->messages = $message;

        return $token->build();
    }
}

class AgoraAccessToken
{
    const VERSION = "007";
    
    public $appID;
    public $appCertificate;
    public $channelName;
    public $uid;
    public $expire;
    public $salt;
    public $ts;

    public function __construct()
    {
        $this->ts = time();
        $this->salt = rand(1, 99999999);
    }

    public function build()
    {
        // Construire les données dans le même format que le token qui fonctionne
        $data = array(
            "salt" => $this->salt,
            "ts" => $this->ts,
            "expire" => $this->expire,
            "channelName" => $this->channelName,
            "uid" => $this->uid
        );

        // Calculer la signature
        $signature = hash_hmac('sha256', 
            $this->appID . $this->channelName . $this->uid . $this->ts . $this->expire . $this->salt,
            $this->appCertificate,
            true
        );

        $data["signature"] = base64_encode($signature);

        // Retourner le token dans le format exact
        return self::VERSION . base64_encode(json_encode($data));
    }
}

class AgoraMessage
{
    public $salt;
    public $ts;
    public $privileges;

    public function __construct()
    {
        $this->privileges = array();
    }

    public function toBytes()
    {
        $buffer = pack("N", $this->salt);
        $buffer .= pack("N", $this->ts);
        
        $size = count($this->privileges);
        $buffer .= pack("n", $size);
        foreach ($this->privileges as $key => $value) {
            $buffer .= pack("n", $key);
            $buffer .= pack("N", $value);
        }
        return $buffer;
    }
} 