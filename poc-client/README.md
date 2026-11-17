# poc-client — Encryption Simulation Client

Laravel web client: **menyimpan key** di sisi client (session) dan mengirim payload sudah terenkripsi `{ "key", "content" }` ke `poc-api`.

## Halaman
- `/symmetric` — generate & simpan AES key, enkripsi data personal + kirim payload terenkripsi ke API.
- `/asymmetric` — generate keypair, daftarkan public key ke API (dapat `key_id`), enkripsi data + kirim payload terenkripsi.
- `/data` — tampilkan data tersimpan (`personal_data_symmetric` / `personal_data_asymmetric`) via API.

## Key preset (tidak digenerate lewat UI)
Key sudah diset dari awal project, UI hanya submit data:
- `POC_AES_KEY` (default di `config/crypto.php`) untuk symmetric.
- `storage/app/keys/preset-public.pem` + `key_id=preset-demo` untuk asymmetric.
- `storage/app/keys/preset-private.pem` disimpan untuk demo (tidak dipakai enkripsi client).

## Payload
- **Symmetric**: `key` = AES key (base64 32B) dienkripsi RSA dengan server public key; `content` = JSON data dienkripsi AES-256-CBC (iv+cipher) dengan key tsb.
- **Asymmetric**: `key` = `key_id` terdaftar dienkripsi RSA server (transport); `content` = envelope JSON `{key, content}` (data-key AES dibungkus RSA public key terdaftar, data dienkripsi AES) — simulasi hybrid.

## Setup
```bash
cp .env.example .env
php artisan key:generate
# .env: ENCRYPTION_API_URL=http://127.0.0.1:8001
php artisan serve --port=8002
```
Pastikan `poc-api` berjalan dan termigrasi.


## Diagram alur
- Symmetric: `../docs/symmetric-flow.png`
- Asymmetric: `../docs/asymmetric-flow.png`

Lihat juga [README utama](../README.md#diagram-alur).
