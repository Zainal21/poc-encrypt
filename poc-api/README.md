# poc-api — Encryption Simulation API

Laravel API untuk simulasi enkripsi **symmetric** & **asymmetric**. Client menyimpan key dan mengirim payload terenkripsi `{ "key", "content" }`; API mendekripsi, memvalidasi, dan menyimpan ke tabel migrasi `personal_data_symmetric` & `personal_data_asymmetric`.

## Endpoint
| Method | Path | Deskripsi |
|---|---|---|
| GET | `/api/crypto/server-public-key` | Ambil public key server (transport encrypt `key`) |
| POST | `/api/crypto/keys/register` | Daftarkan public key client (`name`, `public_key`, opsional `private_key` demo) → `key_id` |
| POST | `/api/personal-data/symmetric` | Simpan data (payload: `key`=AES key terenkripsi RSA server, `content`=JSON terenkripsi AES) |
| GET | `/api/personal-data/symmetric` | List `personal_data_symmetric` |
| POST | `/api/personal-data/asymmetric` | Simpan data (payload: `key`=`key_id` terenkripsi transport, `content`=envelope hybrid terenkripsi key terdaftar) |
| GET | `/api/personal-data/asymmetric` | List `personal_data_asymmetric` |

## Swagger
- UI: `/api/documentation` (`L5_SWAGGER_CONST_HOST`/`APP_URL`).
- Generate docs: `php artisan l5-swagger:generate` (annotation di `app/Http/Controllers`).

## Setup
```bash
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve --port=8001
```
Env relevan: `DB_CONNECTION=sqlite`, `L5_SWAGGER_GENERATE_ALWAYS`, `L5_SWAGGER_CONST_HOST`.

## Storage key (demo)
- Key preset (sekaligus transport RSA & asymmetric): `storage/app/keys/registered/{public,private}/preset-demo.pem`.
- `key_id=preset-demo` otomatis dibuat di tabel `registered_keys` saat pertama dipakai.
- Tidak ada generate key dari client/UI; key sudah diset dari awal project.
- `shortcut:` gitignore dilonggarkan (`.gitignore`, `storage/app/.gitignore`) agar folder keys bisa disertakan untuk demo; jangan commit private key produksi.


## Preset key (demo)
Key **tidak digenerate dari client/UI**. Nilai preset sudah disiapkan dari awal project:

- AES preset (dipakai client): `POC_AES_KEY` (default di `poc-client/config/crypto.php`).
- Asymmetric preset: `storage/app/keys/registered/public/preset-demo.pem` &
  `storage/app/keys/registered/private/preset-demo.pem`, `key_id=preset-demo`.
- API membuat record `registered_keys` otomatis saat pertama kali dipakai (`ensurePresetKey`).

Ganti key preset dengan env/path sendiri bila perlu; jangan pakai key demo ini di produksi.


## Diagram alur
- Symmetric: `../docs/symmetric-flow.png`
- Asymmetric: `../docs/asymmetric-flow.png`

Lihat juga [README utama](../README.md#diagram-alur).
