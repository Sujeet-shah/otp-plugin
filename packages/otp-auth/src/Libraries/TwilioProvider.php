<?php

namespace OtpAuth\Libraries;

use OtpAuth\Interfaces\SmsProviderInterface;
use Exception;

class TwilioProvider implements SmsProviderInterface
{
    protected $sid;
    protected $token;
    protected $from;

    public function __construct(string $sid, string $token, string $from)
    {
        $this->sid = $sid;
        $this->token = $token;
        $this->from = $from;

        if (empty($this->sid) || empty($this->token) || empty($this->from)) {
            throw new Exception('Twilio credentials are missing. Please check your configuration.');
        }
    }

    public function send(string $to, string $message): bool
    {
        $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages.json";
        $data = [
            'From' => $this->from,
            'To' => $to,
            'Body' => $message,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->sid}:{$this->token}");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        // Log error if needed
        log_message('error', 'Twilio SMS failed: ' . $response);
        return false;
    }
}
