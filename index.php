<?php
// Подключение необходимых файлов
require_once __DIR__ . '/includes/db.php'; // Файл подключения к базе данных
require_once __DIR__ . '/includes/functions.php'; // Вспомогательные функции

// Получение параметра поиска (если есть)
$search = $_GET['search'] ?? '';

// Получение ID пользователя из сессии
$userId = $_SESSION['user_id'] ?? null;

// Получение треков из базы данных
if ($search) {
    // Если указан поиск, получаем только подходящие треки
    $stmt = $pdo->prepare("SELECT * FROM tracks WHERE title LIKE ? ORDER BY id DESC");
    $stmt->execute(['%' . $search . '%']);
} else {
    // Если поиска нет, получаем все треки
    $stmt = $pdo->query("SELECT * FROM tracks ORDER BY id DESC");
}
$tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Обработка лайков через AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like']) && $userId) {
    $trackId = $_POST['track_id']; // ID трека, который пользователь хочет лайкнуть

    try {
        // Добавление лайка или игнорирование, если уже существует
        $stmt = $pdo->prepare("
            INSERT INTO likes (user_id, track_id)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE user_id=user_id
        ");
        $stmt->execute([$userId, $trackId]);
        echo json_encode(['success' => true]);
        exit;
    } catch (Exception $e) {
        // Обработка ошибок
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Музыкальный плеер</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">🎵 MusicPlayer</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if ($userId): ?>
                        <li class="nav-item"><a class="nav-link" href="pages/liked.php">❤️ Избранное</a></li>
                        <li class="nav-item"><a class="nav-link" href="pages/upload.php">⬆ Загрузить</a></li>
                        <li class="nav-item"><a class="nav-link" href="auth/logout.php">🚪 Выйти</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="auth/login.php">Вход</a></li>
                        <li class="nav-item"><a class="nav-link" href="auth/register.php">Регистрация</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center mb-4">🎶 Музыкальный плеер</h1>
        <div class="row">
            <div class="col-md-8">
                <audio id="audioPlayer" controls class="w-100 mb-4">
                    <source src="" type="audio/mp3">
                    Ваш браузер не поддерживает аудио.
                </audio>
                <ul class="list-group">
                    <?php foreach ($tracks as $track): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?= htmlspecialchars($track['title']) ?></span>
                            <div>
                                <button class="btn btn-primary btn-sm play-track" data-url="uploads/<?= $track['file'] ?>">▶</button>
                                <?php if ($userId): ?>
                                    <form class="d-inline like-form" method="post">
                                        <input type="hidden" name="track_id" value="<?= $track['id'] ?>">
                                        <input type="hidden" name="like" value="1">
                                        <button class="btn btn-success btn-sm like-track" type="submit">❤️</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-md-4">
                <form class="mb-4" method="get" action="index.php">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="🔍 Найти трек" value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-outline-secondary" type="submit">Поиск</button>
                    </div>
                </form>
                <h5>Советы по поиску:</h5>
                <p>Ищите треки по названию или артисту. Лайкайте любимые треки!</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Воспроизведение треков
        document.querySelectorAll('.play-track').forEach(button => {
            // Для каждой кнопки воспроизведения добавляем обработчик события клика
            button.addEventListener('click', function () {
                const audioPlayer = document.getElementById('audioPlayer'); // Получаем элемент аудиоплеера
                const trackUrl = this.dataset.url; // Получаем URL трека из data-атрибута кнопки

                if (audioPlayer.src !== trackUrl) {
                    // Если трек в плеере отличается от выбранного
                    audioPlayer.src = trackUrl; // Обновляем источник трека в плеере
                    audioPlayer.play(); // Начинаем воспроизведение
                } else if (audioPlayer.paused) {
                    // Если трек совпадает, но плеер находится на паузе
                    audioPlayer.play(); // Продолжаем воспроизведение
                } else {
                    // Если трек воспроизводится
                    audioPlayer.pause(); // Ставим воспроизведение на паузу
                }
            });
        });

        // Обработка лайков
        document.querySelectorAll('.like-form').forEach(form => {
            // Для каждой формы лайков добавляем обработчик события отправки
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Останавливаем стандартное поведение формы (перезагрузку страницы)

                const formData = new FormData(this); // Собираем данные из формы
                fetch('index.php', { // Отправляем POST-запрос на сервер
                    method: 'POST', // Метод запроса
                    body: formData, // Передаём данные формы
                })
                    .then(response => response.json()) // Обрабатываем JSON-ответ от сервера
                    .then(data => {
                        if (data.success) {
                            // Если запрос успешен
                            alert('Трек добавлен в избранное!'); // Показываем уведомление пользователю
                        } else {
                            // Если сервер вернул ошибку
                            alert('Ошибка: трек не удалось добавить в избранное.'); // Показываем сообщение об ошибке
                        }
                    })
                    .catch(error => {
                        // Обработка ошибок, связанных с сетью или выполнением запроса
                        console.error('Ошибка при лайке:', error); // Выводим ошибку в консоль
                    });
            });
        });
    </script>

</body>
</html>
