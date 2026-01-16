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

        // Validate phone number
        if (getenv('AUTH_MODE') == 'phone') {
            $identifier = $this->request->getVar('phone');
            if (empty($identifier) || !preg_match('/^\+[1-9][0-9]{9,14}$/', $identifier)) {
                $data['error'] = 'Enter valid Phone number ';
                return view('OtpAuth\Views\otp', $data);
            } else {
                $data['phone'] = $identifier;
                if ($this->sendOtpTo($identifier)) {
                    return view('OtpAuth\Views\verify', $data);
                }
            }
        } else if (getenv('AUTH_MODE') == 'email') {
            $identifier = $this->request->getVar('email');
            if (empty($identifier) || !filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $data['error'] = 'Valid email is required';
                return view('OtpAuth\Views\otp', $data);
            } else {
                $data['phone'] = $identifier;
                if ($this->sendOtpTo($identifier)) {
                    return view('OtpAuth\Views\verify', $data);
                }
            }
        } else {
            $data['error'] = 'AUTH_MODE not configured';
            return view('OtpAuth\Views\otp', $data);
        }


        $data['error'] = 'Too many requests, try after sometime';
        return view('OtpAuth\Views\otp', $data);
    }

    public function verify()
    {
        $identifier = $this->request->getVar('phone');
        $code = $this->request->getVar('code');

        if (!$identifier || !$code) {

            $data['error'] = "phone and code are required";
            $data['phone'] = $identifier;
            return view('OtpAuth\Views\verify', $data);
        }

        if ($this->verifyOtpFor($identifier, $code)) {
            return redirect()->to(site_url('/'));
        }
        $data['error'] = "Invalid or expired OTP";
        $data['phone'] = $identifier;
        return view('OtpAuth\Views\verify', $data);
        // return $this->fail('Invalid or expired OTP', 400);
    }

    public function sendView()
    {
        return view('OtpAuth\Views\otp');
    }
}
