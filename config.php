<?php
// Работа с сессией
// config.php
session_start();

define('BASE_URL', 'http://localhost:8000/'); // URL проекта
define('UPLOAD_DIR', __DIR__ . '/uploads/');  // Папка для загрузки треков

// Настройки базы данных
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password');
define('DB_NAME', 'music_service');
?>
