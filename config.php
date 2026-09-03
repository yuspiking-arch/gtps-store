<?php
// config.php

// Path ke database server (akan di-set via environment variable)
define('DB_SERVER_PATH', getenv('DB_SERVER_PATH') ?: '/app/db/players.db');

// Path database store (gunakan volume persisten Railway)
define('DB_STORE_PATH', getenv('DB_STORE_PATH') ?: '/app/db/store.db');

// Midtrans (ambil dari environment variables)
define('MIDTRANS_SERVER_KEY', getenv('MIDTRANS_SERVER_KEY') ?: 'SB-Mid-server-xxxxx');
define('MIDTRANS_CLIENT_KEY', getenv('MIDTRANS_CLIENT_KEY') ?: 'SB-Mid-client-xxxxx');
define('MIDTRANS_IS_PRODUCTION', getenv('MIDTRANS_IS_PRODUCTION') === 'true');

// Base URL (otomatis dari Railway)
define('BASE_URL', getenv('RAILWAY_STATIC_URL') ? 'https://' . getenv('RAILWAY_STATIC_URL') : 'http://localhost:8000');

// Session timeout
define('SESSION_TIMEOUT', 3600);

// Role admin (ambil dari environment variable)
define('ADMIN_ROLE', (int)(getenv('ADMIN_ROLE') ?: 2));

// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
