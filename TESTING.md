# OTP Plugin Testing Guide

## Quick Start

### 1. Configure Twilio (Required for SMS)

Create a `.env` file in the root directory:

```bash
# Twilio Configuration
TWILIO_SID=your_twilio_account_sid
TWILIO_TOKEN=your_twilio_auth_token
TWILIO_FROM=your_twilio_phone_number

# Database (using SQLite for demo)
database.tests.database = :memory:
database.tests.DBDriver = SQLite3
```

**Don't have Twilio?** Sign up at https://www.twilio.com/try-twilio (free trial includes credits)

### 2. Run Database Migration

```bash
php spark migrate
```

If this fails, run:
```bash
php spark migrate -n OtpAuth
```

### 3. Start the Development Server

```bash
php spark serve
```

The server will start at `http://localhost:8080`

### 4. Test in Browser

Open your browser and go to: **http://localhost:8080**

You'll see a demo interface where you can:
1. Enter a phone number (format: +1234567890)
2. Click "Send OTP" - you'll receive an SMS
3. Enter the 6-digit code
4. Click "Verify OTP"

## Testing Without Twilio (For Development)

If you want to test without actually sending SMS:

### Option 1: Check Database Directly

1. Send OTP (it will be saved to database even if SMS fails)
2. Check the database to see the OTP:

```bash
sqlite3 writable/database.db "SELECT * FROM otp_requests;"
```

Note: The code is hashed, so you won't see the actual OTP. You'd need to modify `OtpService.php` temporarily to log it.

### Option 2: Mock Provider

Edit `packages/otp-auth/src/Libraries/OtpService.php` and modify the `generate()` method to log the OTP:

```php
// After generating the code
log_message('info', 'Generated OTP: ' . $code . ' for ' . $identifier);
```

Then check `writable/logs/log-YYYY-MM-DD.log`

## API Testing with cURL

### Send OTP
```bash
curl -X POST http://localhost:8080/send-otp \
  -H "Content-Type: application/json" \
  -d '{"phone":"+1234567890"}'
```

### Verify OTP
```bash
curl -X POST http://localhost:8080/verify-otp \
  -H "Content-Type: application/json" \
  -d '{"phone":"+1234567890","code":"123456"}'
```

### Using Pre-built Controller
```bash
# Send
curl -X POST http://localhost:8080/otp/send \
  -d "identifier=+1234567890"

# Verify  
curl -X POST http://localhost:8080/otp/verify \
  -d "identifier=+1234567890&code=123456"
```

## Manual Testing Steps

1. **Generate OTP**: POST to `/send-otp` with phone number
2. **Check SMS**: You should receive a 6-digit code
3. **Verify OTP**: POST to `/verify-otp` with phone + code
4. **Test Expiry**: Wait 5 minutes and try to verify (should fail)
5. **Test Max Attempts**: Try wrong code 3 times (should lock)

## Troubleshooting

### "Could not open input file: spark"
✅ Fixed! The spark file has been created.

### "Migration not found"
Run: `php spark migrate -n OtpAuth`

### "Twilio credentials missing"
Make sure `.env` file exists with your Twilio credentials.

### "Class 'OtpAuth\...' not found"
Run: `composer dump-autoload`

### Database errors
Ensure `writable/` directory is writable:
```bash
chmod -R 777 writable/
```

## Project Structure

```
/var/www/html/auth-plugin/
├── packages/otp-auth/          # The OTP plugin package
├── app/
│   ├── Controllers/
│   │   ├── Home.php           # Demo controller with OTP
│   │   └── BaseController.php
│   ├── Views/
│   │   └── welcome_message.php # Demo UI
│   └── Config/                 # CI4 config files
├── spark                       # CI4 CLI tool
├── .env                        # Your Twilio credentials (create this!)
└── composer.json
```

## Next Steps

Once testing is complete, you can:
1. Extract the `packages/otp-auth/` folder
2. Publish it to GitHub
3. Add to Packagist
4. Use in any CodeIgniter 4 project via `composer require`
