# OTP Authentication Plugin for CodeIgniter 4

A production-ready, plug-and-play OTP authentication library for CodeIgniter 4+ that enables any developer to add OTP functionality to their application in under 5 minutes.

## Features

- **Plug-and-Play**: Install via Composer and start using.
- **Database Support**: MySQL and PostgreSQL compatible.
- **Twilio Integration**: Built-in support for sending SMS via Twilio.
- **Secure**: OTPs are hashed before storage.
- **Configurable**: Customize OTP length, expiry time, and max attempts.

## Installation

1.  **Install via Composer**

    ```bash
    composer require sujeet/codeigniter4-otp-auth
    ```

    *Note: Since this is a local package for now, you might need to add it as a path repository in your root `composer.json` if you are developing locally.*

2.  **Run Migrations**

    ```bash
    php spark migrate -n -g OtpAuth
    ```

3.  **Configure Environment**

    Add your Twilio credentials to your `.env` file:

    ```env
    TWILIO_SID=your_sid
    TWILIO_TOKEN=your_token
    TWILIO_FROM=your_twilio_number
    ```

## Usage

### Using the Service

You can access the OTP service via the global `service()` helper:

```php
$otp = service('otp');

// Generate and send OTP
$otp->generate('+1234567890');

// Verify OTP
if ($otp->verify('+1234567890', '123456')) {
    echo "Verified!";
} else {
    echo "Invalid or Expired";
}
```

### Using the Trait in Controllers

Add the `OtpAuthentication` trait to your controller:

```php
use OtpAuth\Traits\OtpAuthentication;

class Auth extends BaseController
{
    use OtpAuthentication;

    public function login()
    {
        $phone = $this->request->getPost('phone');
        $this->sendOtpTo($phone);
        return view('verify_otp');
    }

    public function verify()
    {
        $phone = $this->request->getPost('phone');
        $code  = $this->request->getPost('code');

        if ($this->verifyOtpFor($phone, $code)) {
            // Log user in
        }
    }
}
```

### Using Pre-built Endpoints

The package comes with a controller that exposes `/otp/send` and `/otp/verify` endpoints. You may need to set up routes to point to `OtpAuth\Controllers\OtpController`.

```php
$routes->post('otp/send', '\OtpAuth\Controllers\OtpController::send');
$routes->post('otp/verify', '\OtpAuth\Controllers\OtpController::verify');
```
