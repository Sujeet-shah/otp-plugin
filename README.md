# CodeIgniter 4 OTP Authentication Plugin

[![Latest Stable Version](https://img.shields.io/packagist/v/sujeet-shah/otp-plugin.svg?style=flat-square)](https://packagist.org/packages/sujeet-shah/otp-plugin)
[![Total Downloads](https://img.shields.io/packagist/dt/sujeet-shah/otp-plugin.svg?style=flat-square)](https://packagist.org/packages/sujeet-shah/otp-plugin)
[![License](https://img.shields.io/packagist/l/sujeet-shah/otp-plugin.svg?style=flat-square)](https://packagist.org/packages/sujeet-shah/otp-plugin)
[![PHP Version](https://img.shields.io/packagist/php-v/sujeet-shah/otp-plugin.svg?style=flat-square)](https://packagist.org/packages/sujeet-shah/otp-plugin)

A production-ready, plug-and-play OTP (One-Time Password) authentication library for CodeIgniter 4. Add secure OTP functionality to your application in under 5 minutes.

---

## 🚀 Features

- **✅ Plug-and-Play**: Seamless integration with CodeIgniter 4.
- **🗄️ Database Support**: Fully compatible with MySQL and PostgreSQL.
- **📱 Twilio Integration**: Built-in provider for sending SMS via Twilio.
- **🔒 Secure by Design**: OTPs are hashed before storage for maximum security.
- **⚙️ Highly Configurable**: Customize OTP length, expiry duration, and maximum retry attempts.
- **🛠️ Flexible Usage**: Use via Service, Trait, or pre-built API endpoints.

---

## 📦 Installation

### 1. Install via Composer

You can install the package via composer:

```bash
composer require sujeet-shah/otp-plugin
```

### 2. Run Migrations

Create the necessary database tables:

```bash
php spark migrate -n -g OtpAuth
```

### 3. Configure Environment

Add your Twilio credentials and optional settings to your `.env` file:

```env
# Twilio Credentials
TWILIO_SID=your_account_sid
TWILIO_TOKEN=your_auth_token
TWILIO_FROM=your_twilio_phone_number

# Optional OTP Settings
OTP_LENGTH=6
EXPIRY_DURATION_IN_SECOND=300
```

---

## 🛠️ Usage

### Option 1: Using the Service (Recommended)

The most flexible way to use the plugin is via the `otp` service.

```php
$otp = service('otp');

// 1. Generate and send OTP to a phone number
$otp->generate('+1234567890');

// 2. Verify an OTP entered by the user
if ($otp->verify('+1234567890', '123456')) {
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
        $phone = $this->request->getPost('phone');
        $this->sendOtpTo($phone);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function verify()
    {
        $phone = $this->request->getPost('phone');
        $code  = $this->request->getPost('code');

        if ($this->verifyOtpFor($phone, $code)) {
            // Logic for successful login
            return $this->response->setJSON(['status' => 'verified']);
        }

        return $this->response->setJSON(['status' => 'failed'], 401);
    }
}
```

### Option 3: Pre-built API Endpoints

The package includes a controller with ready-to-use endpoints. Simply register them in your `app/Config/Routes.php`:

```php
$routes->post('api/otp/send', '\OtpAuth\Controllers\OtpController::send');
$routes->post('api/otp/verify', '\OtpAuth\Controllers\OtpController::verify');
```

---

## ⚙️ Configuration

You can publish the configuration file to customize the plugin behavior:

```bash
# (Optional) If you want to customize the config class directly
```

| Key | Environment Variable | Default | Description |
| :--- | :--- | :--- | :--- |
| `codeLength` | `OTP_LENGTH` | `6` | Length of the generated OTP code. |
| `expirySeconds` | `EXPIRY_DURATION_IN_SECOND` | `300` | Time in seconds before OTP expires. |
| `maxAttempts` | - | `3` | Maximum verification attempts allowed. |

---

## 🧪 Testing

The package comes with a comprehensive testing guide. See [TESTING.md](TESTING.md) for details on how to run tests and mock SMS providers.

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

**Sujeet Shah**
- GitHub: [@Sujeet-shah](https://github.com/Sujeet-shah)
