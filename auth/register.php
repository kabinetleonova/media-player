<?php
// Подключаем файл для работы с базой данных
require_once __DIR__ . '/../includes/db.php';
// Подключаем файл с вспомогательными функциями
require_once __DIR__ . '/../includes/functions.php';

// Проверяем, если запрос к серверу сделан методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем email из отправленных данных формы
    $email = $_POST['email'];
    // Хешируем пароль для безопасного хранения в базе данных
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Подготавливаем SQL-запрос для добавления нового пользователя в базу данных
    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    // Выполняем запрос, передавая email и хешированный пароль
    $stmt->execute([$email, $password]);

    // Перенаправляем пользователя на страницу входа после успешной регистрации
    redirect('login.php');
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Регистрация</h1>
        <form method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
        </form>
        <p class="mt-3 text-center"><a href="login.php">Войти</a></p>
    </div>
</body>
</html>
