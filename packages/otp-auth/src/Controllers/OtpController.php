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
        $identifier = $this->request->getVar('identifier');

        if (!$identifier) {
            return $this->fail('Identifier is required', 400);
        }

        if ($this->sendOtpTo($identifier)) {
            return $this->respond(['message' => 'OTP sent successfully']);
        }

        return $this->fail('Failed to send OTP', 500);
    }

    public function verify()
    {
        $identifier = $this->request->getVar('identifier');
        $code = $this->request->getVar('code');

        if (!$identifier || !$code) {
            return $this->fail('Identifier and code are required', 400);
        }

        if ($this->verifyOtpFor($identifier, $code)) {
            return $this->respond(['message' => 'OTP verified successfully']);
        }

        return $this->fail('Invalid or expired OTP', 400);
    }
}
