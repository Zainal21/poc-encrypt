# Encryption API (Laravel 11)

Backend API for Proof of Concept data encryption using two methods: **Symmetric Encryption (AES-256)** and **Asymmetric Encryption (RSA 2048-bit)**.

## Features

### 1. Symmetric Encryption (AES-256-CBC)
Uses Laravel `Crypt` facade (APP_KEY).
- `POST /api/symmetric/encrypt` — Encrypt plaintext
- `POST /api/symmetric/decrypt` — Decrypt ciphertext

### 2. Asymmetric Encryption (RSA 2048-bit)
Uses native PHP OpenSSL (`openssl_pkey_new`, `openssl_public_encrypt`, `openssl_private_decrypt`).
- `POST /api/asymmetric/generate-keys` — Generate key pair, save to `storage/app/keys/`
- `POST /api/asymmetric/encrypt` — Encrypt using public key from storage
- `POST /api/asymmetric/decrypt` — Decrypt using private key from storage

**Key Management:**
- Key pair stored as files: `storage/app/keys/public.pem` and `storage/app/keys/private.pem`
- Private key **never** returned in response
- Each project (API & Client) has its own key pair

### 3. Personal Data (Symmetric + Database)
- `POST /api/personal-data` — Store name, NIK, phone (AES-256 encrypted in DB)
- `GET /api/personal-data` — List all data (auto-decrypted)
- `GET /api/personal-data/{id}` — Detail data

---

## Symmetric vs Asymmetric Encryption

| Aspect | Symmetric (AES-256) | Asymmetric (RSA) |
|--------|---------------------|------------------|
| **Key** | Single key for both encrypt & decrypt | Key pair: public (encrypt), private (decrypt) |
| **Speed** | Fast, suitable for large data | Slow, suitable for small data / key exchange |
| **Use Case** | Encrypt data at rest, files, DB columns | Key exchange, digital signature, TLS |
| **Key Distribution** | Must share key securely | Public key can be shared freely |

**When to use Symmetric:** Encrypt sensitive data in database, file backups, session data, internal payloads.
**When to use Asymmetric:** Key exchange (like TLS), digital signature, encrypt symmetric key (hybrid), inter-service communication without shared secret.

---

## Key Storage Flow (Storage)

```
generate-keys (AsymmetricController)
    → AsymmetricEncryptionService::generateKeys()
        → openssl_pkey_new(2048-bit RSA)
        → openssl_pkey_export() → private.pem
        → openssl_pkey_get_details()['key'] → public.pem
        → file_put_contents(storage/app/keys/private.pem, 0600)
        → file_put_contents(storage/app/keys/public.pem, 0644)

encrypt/decrypt
    → AsymmetricEncryptionService::readKey(path)
        → file_get_contents(storage/app/keys/*.pem)
        → openssl_public_encrypt / openssl_private_decrypt
```

**Key Points:**
- Keys **never** pass through request body or response
- Generate once, then encrypt/decrypt loads from files
- Private key permission `0600` (owner read/write only)

---

## Key Management (Production)

> ⚠️ **Private key and APP_KEY must NOT be stored on the same server as encrypted data.**

### Production Recommendations:

| Solution | Use Case |
|----------|----------|
| **AWS KMS** | Managed keys, envelope encryption, audit trail, automatic rotation |
| **HashiCorp Vault** | Dynamic secrets, leases, transit engine for crypto operations |
| **Azure Key Vault** | HSM-backed, key rotation policy, RBAC |
| **Google Cloud KMS** | Symmetric/asymmetric, IAM integration |
| **HSM (Hardware Security Module)** | FIPS 140-2 Level 3, physical tamper resistance |

### Best Practices:
1. **Key Rotation** — Rotate keys periodically (e.g., 90 days), re-encrypt old data
2. **Envelope Encryption** — Data encrypted with DEK (Data Encryption Key), DEK encrypted with KEK (Key Encryption Key) in KMS
3. **Separation of Duties** — App server only has decrypt access via KMS API, never holds raw private key
4. **Audit Logging** — Every key operation logged
5. **Backup & Recovery** — Offline key backups, tested restore procedures

---

## Getting Started

```bash
cd encryption-api

# Copy env and generate key
cp .env.example .env
php artisan key:generate

# Create SQLite database
touch database/database.sqlite

# Run migrations
php artisan migrate

# (optional) generate Swagger docs
php artisan l5-swagger:generate

# Start server (port 8001)
php artisan serve --port=8001 --host=127.0.0.1
```

Server runs at `http://127.0.0.1:8001`

### Swagger / OpenAPI Docs
- UI: `http://127.0.0.1:8001/api/documentation`
- JSON spec: `http://127.0.0.1:8001/docs` (stored at `storage/api-docs/api-docs.json`)
- Regenerate: `php artisan l5-swagger:generate` (also auto-generates on request if `L5_SWAGGER_GENERATE_ALWAYS=true`)

---

## Example Requests (cURL)

### Symmetric Encrypt
```bash
curl -X POST http://127.0.0.1:8001/api/symmetric/encrypt \
  -H "Content-Type: application/json" \
  -d '{"text":"secret123"}'
```

### Symmetric Decrypt
```bash
curl -X POST http://127.0.0.1:8001/api/symmetric/decrypt \
  -H "Content-Type: application/json" \
  -d '{"ciphertext":"eyJpdiI6I...","tag":"..."}'
```

### Asymmetric Generate Keys
```bash
curl -X POST http://127.0.0.1:8001/api/asymmetric/generate-keys
```

### Asymmetric Encrypt
```bash
curl -X POST http://127.0.0.1:8001/api/asymmetric/encrypt \
  -H "Content-Type: application/json" \
  -d '{"text":"secret message"}'
```

### Asymmetric Decrypt
```bash
curl -X POST http://127.0.0.1:8001/api/asymmetric/decrypt \
  -H "Content-Type: application/json" \
  -d '{"ciphertext":"base64ciphertext..."}'
```

### Personal Data Store
```bash
curl -X POST http://127.0.0.1:8001/api/personal-data \
  -H "Content-Type: application/json" \
  -d '{
    "full_name": "Budi Santoso",
    "nik": "3273010101010001",
    "phone_number": "08123456789"
  }'
```

### Personal Data List
```bash
curl http://127.0.0.1:8001/api/personal-data
```

### Personal Data Detail
```bash
curl http://127.0.0.1:8001/api/personal-data/1
```

---

## API Response Format

```json
{
  "success": true,
  "message": "Result description",
  "data": { ... }
}
```

Error:
```json
{
  "success": false,
  "message": "Error description",
  "data": null
}
```

---

## Project Structure

```
encryption-api/
├── app/
│   ├── Http/Controllers/Api/
│   │   ├── SymmetricController.php
│   │   ├── AsymmetricController.php
│   │   └── PersonalDataController.php
│   ├── Models/
│   │   └── PersonalData.php
│   └── Services/
│       ├── SymmetricEncryptionService.php
│       └── AsymmetricEncryptionService.php
├── database/
│   └── migrations/
│       └── create_personal_data_table.php
├── routes/
│   └── api.php
├── storage/app/keys/
│   ├── public.pem
│   └── private.pem
└── .env
```

---

## Technology Stack
- Laravel 11
- PHP 8.3+
- SQLite (dev), MySQL/PostgreSQL (prod)
- OpenSSL (native PHP)
- Laravel Crypt (AES-256-CBC)
- Swagger/OpenAPI: `darkaonline/l5-swagger` (OpenAPI annotations)