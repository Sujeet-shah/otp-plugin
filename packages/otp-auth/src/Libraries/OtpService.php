<?php

namespace OtpAuth\Libraries;

use OtpAuth\Models\OtpModel;
use OtpAuth\Config\OtpAuth;
use OtpAuth\Interfaces\SmsProviderInterface;

class OtpService
{
    protected $model;
    protected $config;
    protected $smsProvider;

    public function __construct(OtpAuth $config = null, SmsProviderInterface $smsProvider = null)
    {
        $this->config = $config ?? new OtpAuth();
        $this->model = new OtpModel();

        if ($smsProvider) {
            $this->smsProvider = $smsProvider;
        } else {
            // Default to Twilio if configured
            if ($this->config->twilioSid && $this->config->twilioToken && $this->config->twilioFrom) {
                $this->smsProvider = new TwilioProvider(
                    $this->config->twilioSid,
                    $this->config->twilioToken,
                    $this->config->twilioFrom
                );
            }
        }
    }



    /**
     * custom body tosend OTP
     */

    public function getBody(string $otp, ?string $body = null): string
    {
        // If user provides custom body, replace OTP placeholder
        if (!empty($body)) {
            return str_replace('{otp}', $otp, $body);
        }

        // Otherwise load package default template
        $data = ['otp' => $otp];

        if ($this->config->auth_mode === 'phone') {
            return view('OtpAuth\Views\otpPhoneTamplate', $data);
        }

        return view('OtpAuth\Views\otpEmailTemplate', $data);
    }


    /**
     * Generate and send OTP
     */
    public function generate(string $identifier, $body = null): bool
    {
        $count = $this->model->otpCount($identifier);
        $otpCount = $count + 1;
        if ($otpCount <= getenv('OTP_LIMIT_IN_MINUTS')) {
            // Cleanup old OTPs
            $this->model->cleanupExpired();

            // Generate Code
            $code = '';
            for ($i = 0; $i < $this->config->codeLength; $i++) {
                $code .= mt_rand(0, 9);
            }

            // Hash Code
            $hashedCode = $code;
            $data['otp'] = $code;


            $body = $this->getBody($code, $body);

            // Save to DB
            $this->model->createOtp($identifier, $hashedCode, $this->config->expirySeconds);
            if ($this->config->auth_mode == 'phone') {
                // Send SMS
                $body = view('OtpAuth\Views\otpPhoneTamplate', $data);
                if ($this->smsProvider) {

                    return true;
                    // $this->smsProvider->send($identifier, $body);
                }
            }
            if ($this->config->auth_mode == 'email') {
                $body = view('OtpAuth\Views\otpEmailTemplate', $data);
                return true;
                // $this->sendEmailOtp($identifier, $body);
            }
            log_message('warning', 'OtpService: No SMS provider configured. OTP generated but not sent.');

            return true;
        } else {
            return false;
        }
    }

    /**
     * Send OTP via Email
     */
    private function sendEmailOtp(string $email, string $message): bool
    {
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer();
            $mail->isSMTP();
            $mail->Host = getenv('MAIL_HOST') ?: 'localhost';
            $mail->Port = getenv('MAIL_PORT') ?: 587;
            $mail->SMTPAuth = true;
            $mail->Username = getenv('MAIL_USERNAME') ?: '';
            $mail->Password = getenv('MAIL_PASSWORD') ?: '';
            $mail->SMTPSecure = getenv('MAIL_ENCRYPTION') ?: 'tls';
            $mail->setFrom(getenv('MAIL_FROM') ?: 'noreply@example.com', 'OTP Service');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body = $message;

            return $mail->send();
        } catch (\Exception $e) {
            log_message('error', 'OtpService: Failed to send email OTP - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify OTP
     */
    public function verify(string $identifier, string $code): bool
    {
        $otp = $this->model->findValidOtp($identifier);

        if (!$otp) {
            return false;
        }

        // Check attempts
        if ($otp['attempts'] >= $this->config->maxAttempts) {
            return false;
        }

        // Verify Hash
        if ($code == $otp['code']) {
            // Success - Delete OTP (or mark verified)
            $this->model->delete($otp['id']);
            return true;
        } else {
            // Increment attempts
            $this->model->incrementAttempts($otp['id']);
            return false;
        }
    }
}
