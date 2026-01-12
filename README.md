# 🔐 CodeIgniter 4 OTP Auth Plugin

A production-ready One-Time Password (OTP) authentication plugin for CodeIgniter 4. Easily integrate SMS-based OTP verification into your CodeIgniter applications using Twilio.

---

## ✨ Features

- ✅ Generate and send OTPs via SMS (Twilio)
- ✅ Verify OTPs with customizable expiration
- ✅ Automatic cleanup of expired OTPs
- ✅ Attempt limiting (prevent brute force)
- ✅ Database-backed OTP storage
- ✅ Simple REST API endpoints
- ✅ Easy integration with existing CodeIgniter projects
- ✅ Fully tested and documented

---

## 📋 Requirements

- PHP 8.1 or higher
- CodeIgniter 4.0 or higher
- MySQL 5.7+ or any CodeIgniter-compatible database
- Twilio account (for sending SMS)

---

## 🚀 Quick Start

### Step 1: Install the Plugin

Add the package to your CodeIgniter 4 project via Composer:

```bash
composer require sujeet-shah/otp-plugin:^1.0
```

Or if developing locally with a path repository, update your `composer.json`:

```json
{
    "require": {
        "php": "^8.1",
        "codeigniter4/framework": "^4.0",
        "sujeet-shah/otp-plugin": "^1.0"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/Sujeet-shah/otp-plugin.git"
        }
    ]
}
```

Then run:
```bash
composer update
```

---

### Step 2: Configure Your Database

Update your **`app/Config/Database.php`**:

```php
public array $default = [
    'DSN'      => '',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => 'password',
    'database' => 'your_database_name',  // ← Set your database name
    'DBDriver' => 'MySQLi',
    'DBPrefix' => '',
    // ... other settings
];
```

Create the database if it doesn't exist:

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS your_database_name;"
```

---

### Step 3: Run Database Migrations

Create the required `otp_requests` table:

```bash
php spark migrate -n OtpAuth
```

This will create a table with the following structure:
- `id` - Auto-increment primary key
- `identifier` - Phone number or user identifier
- `code` - Hashed OTP code
- `created_at` - Creation timestamp
- `expires_at` - Expiration timestamp
- `attempts` - Number of verification attempts
- `is_verified` - Verification status

---

### Step 4: Configure Twilio Credentials

Add your Twilio credentials to your **`.env`** file:

```env
TWILIO_SID=your_twilio_account_sid
TWILIO_TOKEN=your_twilio_auth_token
TWILIO_FROM=+1234567890
```

**Where to get these values:**
1. Sign up at [Twilio.com](https://www.twilio.com)
2. Go to your Console Dashboard
3. Copy your **Account SID** and **Auth Token**
4. Add a verified phone number as **TWILIO_FROM**

---

## 📖 Usage

### Method 1: Using the Trait (In Controllers)

Add the `OtpAuthentication` trait to your controller:

```php
<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use OtpAuth\Traits\OtpAuthentication;

class AuthController extends Controller
{
    use OtpAuthentication;

    /**
     * Send OTP to phone number
     */
    public function sendOtp()
    {
        $phone = $this->request->getPost('phone');

        if (empty($phone)) {
            return $this->response->setJSON([
                'error' => 'Phone number is required'
            ]);
        }

        if ($this->sendOtpTo($phone)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'OTP sent successfully to ' . $phone
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to send OTP'
        ]);
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp()
    {
        $phone = $this->request->getPost('phone');
        $code = $this->request->getPost('code');

        if (empty($phone) || empty($code)) {
            return $this->response->setJSON([
                'error' => 'Phone and code are required'
            ]);
        }

        if ($this->verifyOtpFor($phone, $code)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'OTP verified successfully!'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid or expired OTP'
        ]);
    }
}
```

---

### Method 2: Using the OtpService Directly

```php
<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use OtpAuth\Libraries\OtpService;

class AuthController extends Controller
{
    public function sendOtp()
    {
        $phone = $this->request->getPost('phone');
        
        $otpService = new OtpService();
        
        if ($otpService->generate($phone)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'OTP sent to ' . $phone
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to send OTP'
        ]);
    }

    public function verifyOtp()
    {
        $phone = $this->request->getPost('phone');
        $code = $this->request->getPost('code');
        
        $otpService = new OtpService();
        
        if ($otpService->verify($phone, $code)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'OTP verified!'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid OTP'
        ]);
    }
}
```

---

## 🛣️ Setting Up Routes

Add these routes to your **`app/Config/Routes.php`**:

```php
<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

$routes = service('routes');

// OTP Routes
$routes->post('otp/send', 'AuthController::sendOtp');
$routes->post('otp/verify', 'AuthController::verifyOtp');

// Or using the built-in OtpController
$routes->group('otp', ['namespace' => 'OtpAuth\Controllers'], function ($routes) {
    $routes->post('send', 'OtpController::send');
    $routes->post('verify', 'OtpController::verify');
});
```

---

## 🔌 API Endpoints

### Send OTP

**Request:**
```bash
POST /otp/send
Content-Type: application/x-www-form-urlencoded

phone=+1234567890
```

**Response (Success):**
```json
{
    "success": true,
    "message": "OTP sent successfully to +1234567890"
}
```

**Response (Error):**
```json
{
    "error": "Phone number is required"
}
```

---

### Verify OTP

**Request:**
```bash
POST /otp/verify
Content-Type: application/x-www-form-urlencoded

phone=+1234567890&code=123456
```

**Response (Success):**
```json
{
    "success": true,
    "message": "OTP verified successfully!"
}
```

**Response (Error):**
```json
{
    "success": false,
    "message": "Invalid or expired OTP"
}
```

---

## 🧪 Testing with cURL

### Send OTP
```bash
curl -X POST http://localhost:8080/otp/send \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "phone=%2B1234567890"
```

### Verify OTP
```bash
curl -X POST http://localhost:8080/otp/verify \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "phone=%2B1234567890&code=123456"
```

### Send OTP with JSON
```bash
curl -X POST http://localhost:8080/otp/send \
  -H "Content-Type: application/json" \
  -d '{"phone":"+1234567890"}'
```

---

## ⚙️ Configuration

### Custom OTP Settings

Create or update **`app/Config/OtpAuth.php`**:

```php
<?php

namespace Config;

use OtpAuth\Config\OtpAuth as BaseOtpAuth;

class OtpAuth extends BaseOtpAuth
{
    /**
     * Length of OTP code (default: 6)
     */
    public $codeLength = 6;

    /**
     * OTP expiry time in seconds (default: 300 = 5 minutes)
     */
    public $expirySeconds = 300;

    /**
     * Maximum verification attempts before invalidating (default: 3)
     */
    public $maxAttempts = 3;

    /**
     * Twilio Configuration (loaded from .env)
     */
    public $twilioSid = '';
    public $twilioToken = '';
    public $twilioFrom = '';
}
```

---

## 📁 Project Structure

```
packages/otp-auth/
├── src/
│   ├── Config/
│   │   ├── OtpAuth.php          # Configuration class
│   │   └── Services.php         # Service provider
│   ├── Controllers/
│   │   └── OtpController.php    # REST API controller
│   ├── Database/
│   │   └── Migrations/          # Database migrations
│   ├── Libraries/
│   │   ├── OtpService.php       # Core OTP service
│   │   └── TwilioProvider.php   # SMS provider
│   ├── Models/
│   │   └── OtpModel.php         # OTP database model
│   ├── Traits/
│   │   └── OtpAuthentication.php # Trait for controllers
│   └── Interfaces/
│       └── SmsProviderInterface.php
├── tests/
│   └── ...                      # Unit tests
└── composer.json
```

---

## 🔒 Security Best Practices

1. **Never commit `.env` file** - Add it to `.gitignore`
2. **Use HTTPS** - Always send OTP requests over HTTPS in production
3. **Rate limiting** - Implement rate limiting on OTP endpoints
4. **Verify phone format** - Validate phone numbers before sending
5. **Log attempts** - Monitor failed OTP attempts for suspicious activity
6. **Expire OTPs** - Default 5-minute expiration (configurable)
7. **Hash codes** - OTP codes are bcrypt hashed in the database

---

## 🐛 Troubleshooting

### Issue: "Call to undefined method OtpAuth::sendOtpTo()"
**Solution:** Use the `OtpAuthentication` trait or the `OtpService` class directly, not the `OtpAuth` config class.

```php
// ❌ Wrong
use OtpAuth\Config\OtpAuth;
$config = new OtpAuth();
$config->sendOtpTo($phone);

// ✅ Correct
use OtpAuth\Traits\OtpAuthentication;
class MyController extends Controller {
    use OtpAuthentication;
    $this->sendOtpTo($phone);
}
```

### Issue: "Table 'otp_requests' doesn't exist"
**Solution:** Run the migration:
```bash
php spark migrate -n OtpAuth
```

### Issue: "Twilio credentials not configured"
**Solution:** Add credentials to `.env`:
```env
TWILIO_SID=your_sid
TWILIO_TOKEN=your_token
TWILIO_FROM=+1234567890
```

### Issue: "Failed to send OTP"
**Solution:** Check:
1. Twilio credentials are correct
2. Phone number format is valid (e.g., `+1234567890`)
3. Twilio account has credits
4. Database migrations ran successfully

---

## 📚 Advanced Usage

### Custom SMS Provider

Implement `SmsProviderInterface` to use a different SMS provider:

```php
<?php

namespace App\Libraries;

use OtpAuth\Interfaces\SmsProviderInterface;

class CustomSmsProvider implements SmsProviderInterface
{
    public function send(string $identifier, string $message): bool
    {
        // Your custom implementation
        return true;
    }
}
```

Then use it:

```php
use OtpAuth\Libraries\OtpService;
use App\Libraries\CustomSmsProvider;

$provider = new CustomSmsProvider();
$otpService = new OtpService(null, $provider);
$otpService->generate($phone);
```

---

## 🧪 Running Tests

Inside the plugin directory:

```bash
cd packages/otp-auth
./vendor/bin/phpunit
```

---

## 📝 License

MIT License - see LICENSE file for details

---

## 🤝 Contributing

Contributions are welcome! Please submit issues and pull requests on [GitHub](https://github.com/Sujeet-shah/otp-plugin).

---

## 📞 Support

For issues and questions:
- Open an issue on GitHub
- Check existing documentation
- Review error logs in `writable/logs/`

---

## 🎯 Quick Reference

| Task | Command |
|------|---------|
| Install | `composer require sujeet-shah/otp-plugin` |
| Migrate DB | `php spark migrate -n OtpAuth` |
| Run tests | `./vendor/bin/phpunit` |
| Send OTP | `POST /otp/send` with `phone` parameter |
| Verify OTP | `POST /otp/verify` with `phone` and `code` |

---

**Happy coding! 🚀**

## Server Requirements

PHP version 8.1 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
