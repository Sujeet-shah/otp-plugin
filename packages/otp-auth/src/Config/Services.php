<?php

namespace OtpAuth\Config;

use CodeIgniter\Config\BaseService;
use OtpAuth\Libraries\OtpService;

class Services extends BaseService
{
    public static function otp($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('otp');
        }

        return new OtpService();
    }
}
