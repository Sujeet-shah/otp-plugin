# CodeIgniter 4 OTP Authentication Plugin

[![Latest Stable Version](https://img.shields.io/packagist/v/sujeet-shah/otp-plugin.svg?style=flat-square)](https://packagist.org/packages/sujeet-shah/otp-plugin)
[![Total Downloads](https://img.shields.io/packagist/dt/sujeet-shah/otp-plugin.svg?style=flat-square)](https://packagist.org/packages/sujeet-shah/otp-plugin)
[![License](https://img.shields.io/packagist/l/sujeet-shah/otp-plugin.svg?style=flat-square)](https://packagist.org/packages/sujeet-shah/otp-plugin)
[![PHP Version](https://img.shields.io/packagist/php-v/sujeet-shah/otp-plugin.svg?style=flat-square)](https://packagist.org/packages/sujeet-shah/otp-plugin)

A production-ready, plug-and-play OTP (One-Time Password) authentication library for CodeIgniter 4. Add secure OTP functionality via SMS (Twilio) or Email to your application in under 5 minutes.

---

## 🚀 Features

- **✅ Plug-and-Play**: Seamless integration with CodeIgniter 4.
- **📧 Multi-Channel Support**: Send OTPs via SMS (Twilio) or Email (SMTP).
- **🗄️ Database Support**: Fully compatible with MySQL and PostgreSQL.
- **🛡️ Rate Limiting**: Built-in protection against OTP flooding.
- **⚙️ Highly Configurable**: Customize OTP length, expiry duration, and maximum retry attempts.
- **🛠️ Flexible Usage**: Use via Service, Trait, or pre-built API endpoints.

---

## 📦 Installation

### 1. Install via Composer

```bash
composer require sujeet-shah/otp-plugin
```

### 2. Run Migrations

Create the necessary database tables:

```bash
php spark migrate -n OtpAuth
```

### 3. Configure Environment

Add the following to your `.env` file:

```env
# Authentication Mode (phone or email)
AUTH_MODE=phone

# OTP Settings
OTP_LENGTH=6
EXPIRY_DURATION_IN_SECOND=300
MAX_ATTEMPTS=3
OTP_LIMIT_IN_MINUTS=5 # Max OTPs allowed per minute

# Twilio Credentials (if AUTH_MODE=phone)
TWILIO_SID=your_account_sid
TWILIO_TOKEN=your_auth_token
TWILIO_FROM=your_twilio_phone_number

# Email Credentials (if AUTH_MODE=email)
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM=noreply@example.com
```

---

## 🛠️ Usage

### Option 1: Using the Service (Recommended)

The most flexible way to use the plugin is via the `otp` service.

```php
$otp = service('otp');

// 1. Generate and send OTP
$identifier = '+1234567890'; // or 'user@example.com'
if ($otp->generate($identifier)) {
    echo "OTP sent successfully!";
} else {
    echo "Failed to send OTP (possibly rate limited).";
}

// 2. Verify an OTP entered by the user
if ($otp->verify($identifier, '123456')) {
    echo "Verification successful!";
} else {
    echo "Invalid or expired OTP.";
}
```

### Option 2: Using the Trait in Controllers

Easily add OTP capabilities to any controller using the `OtpAuthentication` trait.

```php
namespace App\Controllers;

use OtpAuth\Traits\OtpAuthentication;

class AuthController extends BaseController
{
    use OtpAuthentication;

    public function send()
    {
        $identifier = $this->request->getPost('identifier');
        if ($this->sendOtpTo($identifier)) {
            return $this->response->setJSON(['status' => 'success']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Rate limit exceeded'], 429);
    }

    public function verify()
    {
        $identifier = $this->request->getPost('identifier');
        $code       = $this->request->getPost('code');

        if ($this->verifyOtpFor($identifier, $code)) {
            return $this->response->setJSON(['status' => 'verified']);
        }

        return $this->response->setJSON(['status' => 'failed'], 401);
    }
}
```

### Option 3: Pre-built API Endpoints

The package includes a controller with ready-to-use endpoints. Register them in your `app/Config/Routes.php`:

```php
$routes->get('otp', '\OtpAuth\Controllers\OtpController::sendView');
$routes->post('otp/send', '\OtpAuth\Controllers\OtpController::send');
$routes->post('otp/verify', '\OtpAuth\Controllers\OtpController::verify');
```

**Note:** The pre-built `send` endpoint expects `phone` or `email` field in the request depending on your `AUTH_MODE`.

---

## ⚙️ Configuration

| Key | Environment Variable | Default | Description |
| :--- | :--- | :--- | :--- |
| `auth_mode` | `AUTH_MODE` | `''` | `phone` or `email`. |
| `codeLength` | `OTP_LENGTH` | `6` | Length of the generated OTP code. |
| `expirySeconds` | `EXPIRY_DURATION_IN_SECOND` | `300` | Time in seconds before OTP expires. |
| `maxAttempts` | `MAX_ATTEMPTS` | `3` | Maximum verification attempts allowed per OTP. |
| `otpLimit` | `OTP_LIMIT_IN_MINUTS` | `''` | Max OTPs allowed per minute for an identifier. |

---

## 🎨 Customizing Views

The plugin comes with default views for the OTP form and email templates. You can find them in `packages/otp-auth/src/Views/`. To customize them, you can override them in your application's `app/Views/` directory or modify the controller to point to your own views.

---

## 🧪 Testing

The package comes with a comprehensive testing guide. See [TESTING.md](TESTING.md) for details on how to run tests and mock SMS/Email providers.

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

**Sujeet Shah**
- GitHub: [@Sujeet-shah](https://github.com/Sujeet-shah)
