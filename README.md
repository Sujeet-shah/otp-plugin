# CodeIgniter 4 OTP Auth Plugin Development

This project contains a production-ready OTP (One-Time Password) authentication plugin for CodeIgniter 4.

## 📂 Important Files and Folders

| Path | Description |
| :--- | :--- |
| `packages/otp-auth/` | **The Core Plugin**. This is the standalone package you will integrate into other projects. |
| `packages/otp-auth/src/` | Contains the plugin logic (Controllers, Models, Libraries, etc.). |
| `app/Config/Routes.php` | Where you define the API endpoints for sending and verifying OTPs. |
| `.env` | Store your Twilio credentials and database settings here. |
| `composer.json` | Root dependency file. Used to link the local package for development. |
| `spark` | CodeIgniter's command-line tool for running migrations and tests. |

---

## 🚀 How to Integrate the Plugin

To use this plugin in an existing CodeIgniter 4 project, follow these steps:

### 1. Add the Package to your Project

If you are developing locally, add the package as a path repository in your **root `composer.json`**:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../path-to-this-folder/packages/otp-auth"
        }
    ],
    "require": {
        "sujeet/codeigniter4-otp-auth": "@dev"
    }
}
```

Then run:
```bash
composer update
```

### 2. Run Database Migrations

Create the required `otp_codes` table:

```bash
php spark migrate -n OtpAuth
```

### 3. Configure Twilio

Add your credentials to your `.env` file:

```env
TWILIO_SID=your_sid
TWILIO_TOKEN=your_token
TWILIO_FROM=your_phone_number
```

### 4. Basic Usage

#### Using the Service
```php
$otp = service('otp');
$otp->generate('+1234567890'); // Sends OTP
$otp->verify('+1234567890', '123456'); // Verifies OTP
```

#### Using Routes
Add these to your `app/Config/Routes.php`:
```php
$routes->post('otp/send', '\OtpAuth\Controllers\OtpController::send');
$routes->post('otp/verify', '\OtpAuth\Controllers\OtpController::verify');
```

---

## 🛠 Development

- **Tests**: Run `./vendor/bin/phpunit` inside `packages/otp-auth` to run plugin tests.
- **Coding Standard**: Use `./vendor/bin/php-cs-fixer fix` to format code.
```php
    public function sendOtp()
    {
        $phone = $this->request->getPost('phone');

        if (empty($phone)) {
            return $this->response->setJSON(['error' => 'Phone number is required']);
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

    public function verifyOtp()
    {
        $phone = $this->request->getPost('phone');
        $code = $this->request->getPost('code');

        if (empty($phone) || empty($code)) {
            return $this->response->setJSON(['error' => 'Phone and code are required']);
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

    ```