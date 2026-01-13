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

    You can install this package either locally (for development) or directly from GitHub.

    ### Option A: Install from GitHub (Recommended)

    Add the repository to your project's `composer.json`:

    ```json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/Sujeet-shah/otp-plugin"
        }
    ],
    ```

    Then run:

    ```bash
    composer require sujeet-shah/otp-plugin
    ```

    ### Option B: Local Installation (For Development)

    Add the local path to your project's `composer.json`:

    ```json
    "repositories": [
        {
            "type": "path",
            "url": "/var/www/html/auth-plugin/packages/otp-auth"
        }
    ],
    ```

    Then run:

    ```bash
    composer require sujeet-shah/otp-plugin
    ```

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
