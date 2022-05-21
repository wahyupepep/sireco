## Project Admin Stok Setiawan Jaya

Langkah-langkah penggunaan project ini

1. Gunakan command composer install untuk menginstall vendor
2. Copy file .env.example menjadi .env
3. Berikan nilai dari DB_DATABASE = {terserah} di file .env
4. Gunakan command php artisan key:generate untuk mengisi value APP_KEY pada file .env
5. Gunakan command php artisan migrate --seed untuk mengimpor seluruh table database ke nama database yang tadi telah dibuat
