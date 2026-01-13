<?php

namespace OtpAuth\Config;

use CodeIgniter\Config\BaseConfig;

class OtpAuth extends BaseConfig
{
    /**
     * OTP Code Length
     */
    public $codeLength = '';

    /**
     * OTP Expiry Time in Seconds
     */
    public $expirySeconds = ""; // 5 minutes

    /**
     * Max Attempts before invalidating
     */
    public $maxAttempts = 3;

    /**
     * Twilio Configuration
     */
    public $twilioSid = '';
    public $twilioToken = '';
    public $twilioFrom = '';

    public function __construct()
    {
        parent::__construct();
        $this->expirySeconds = getenv('EXPIRY_DURATION_IN_SECOND') ?: 300; // 5 minutes
        $this->codeLength = getenv('OTP_LENGTH') ?: 6;
        // Load from .env if available
        $this->twilioSid = getenv('TWILIO_SID') ?: '';
        $this->twilioToken = getenv('TWILIO_TOKEN') ?: '';
        $this->twilioFrom = getenv('TWILIO_FROM') ?: '';
    }
}
