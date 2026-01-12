<?php

namespace OtpAuth\Traits;

use OtpAuth\Libraries\OtpService;

trait OtpAuthentication
{
    protected $otpService;

    protected function getOtpService()
    {
        if (!$this->otpService) {
            $this->otpService = new OtpService();
        }
        return $this->otpService;
    }

    public function sendOtpTo(string $identifier)
    {
        return $this->getOtpService()->generate($identifier);
    }

    public function verifyOtpFor(string $identifier, string $code)
    {
        return $this->getOtpService()->verify($identifier, $code);
    }
}
