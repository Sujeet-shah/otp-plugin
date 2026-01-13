<?php

namespace OtpAuth\Controllers;

use CodeIgniter\RESTful\ResourceController;
use OtpAuth\Traits\OtpAuthentication;

class OtpController extends ResourceController
{
    use OtpAuthentication;

    protected $format = 'json';

    public function send()
    {
        $phone = $this->request->getVar('phone');

        if (!$phone) {
            return $this->fail('phone number is required', 400);
        }

        if ($this->sendOtpTo($phone)) {
            return $this->respond(['message' => 'OTP sent successfully']);
        }

        return $this->fail('Failed to send OTP', 500);
    }

    public function verify()
    {
        $phone = $this->request->getVar('phone');
        $code = $this->request->getVar('code');

        if (!$phone || !$code) {
            return $this->fail('phone number and code are required', 400);
        }

        if ($this->verifyOtpFor($phone, $code)) {
            return $this->respond(['message' => 'OTP verified successfully']);
        }

        return $this->fail('Invalid or expired OTP', 400);
    }
}
