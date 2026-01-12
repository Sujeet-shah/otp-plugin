<?php

namespace OtpAuth\Interfaces;

interface SmsProviderInterface
{
    /**
     * Send an SMS to the specified number.
     *
     * @param string $to
     * @param string $message
     * @return bool
     */
    public function send(string $to, string $message): bool;
}
