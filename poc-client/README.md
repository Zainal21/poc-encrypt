# Encryption Client (Laravel 11 + Blade)

Frontend/Client demo for encryption POC. Calls **encryption-api** via HTTP facade.

## Features

1. **Symmetric Encryption Demo** — Encrypt/decrypt text (AES-256 via API)
2. **Asymmetric Encryption Demo** — Generate key pair (stored in API storage), encrypt/decrypt RSA 2048-bit without key input fields in form
3. **Personal Data Demo** — Form to store sensitive data (name, NIK, phone), display list of decrypted data

## Prerequisites

- PHP 8.3+
- Composer
- `encryption-api` running at `http://127.0.0.1:8001`

## Getting Started

```bash
cd encryption-client

# Install dependencies
composer install

# Setup env
cp .env.example .env
php artisan key:generate

# Start server (port 8002)
php artisan serve --port=8002 --host=127.0.0.1
```

Open: `http://127.0.0.1:8002`

## API URL Configuration

Edit `.env`:

```env
ENCRYPTION_API_URL=http://127.0.0.1:8001
```

Config also available in `config/services.php`:

```php
'encryption_api' => [
    'url' => env('ENCRYPTION_API_URL', 'http://127.0.0.1:8001'),
],
```

## Page Structure

| Route | Page |
|-------|------|
| `/symmetric` | Symmetric encrypt/decrypt demo |
| `/asymmetric` | Generate keys, asymmetric encrypt/decrypt demo (no key fields) |
| `/personal-data` | Form & list of encrypted personal data |

## Project Structure

```
encryption-client/
├── app/Http/Controllers/
│   └── DemoController.php
├── resources/views/
│   ├── layout.blade.php
│   └── demos/
│       ├── symmetric.blade.php
│       ├── asymmetric.blade.php
│       └── personal.blade.php
├── routes/web.php
├── storage/app/keys/      # client's own key pair (separate demo)
└── .env
```

## Notes

- Client does **not** perform local encryption; all crypto operations go through the API.
- API key pair stored in `encryption-api/storage/app/keys/`.
- Client also has its own `storage/app/keys/` folder (separate) per POC specification.
- UI uses Bootstrap 5 CDN, with loading state on submit.