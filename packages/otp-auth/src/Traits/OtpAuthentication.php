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

    public function sendOtpTo(string $phone)
    {
        return $this->getOtpService()->generate($phone);
    }

    public function verifyOtpFor(string $phone, string $code)
    {
        return $this->getOtpService()->verify($phone, $code);
    }
}
