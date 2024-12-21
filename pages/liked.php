<?php
// Подключаем файл с базой данных
require_once __DIR__ . '/../includes/db.php';
// Подключаем файл с вспомогательными функциями
require_once __DIR__ . '/../includes/functions.php';

// Проверяем, авторизован ли пользователь. Если нет, перенаправляем на страницу входа
if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

// Получаем ID авторизованного пользователя
$userId = $_SESSION['user_id'];

// Подготавливаем SQL-запрос для получения избранных треков пользователя
$stmt = $pdo->prepare("
    SELECT t.title, t.file 
    FROM likes l 
    JOIN tracks t ON l.track_id = t.id 
    WHERE l.user_id = ?
");

// Выполняем запрос с передачей ID пользователя
$stmt->execute([$userId]);

// Извлекаем все треки, добавленные пользователем в избранное
$likedTracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Избранное</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        /* Стили для аудиоплеера */
        #audioPlayer {
            width: 100%; /* Устанавливаем ширину на 100% родителя */
            max-width: 600px; /* Ограничиваем максимальную ширину */
            margin: 0 auto 20px; /* Центрируем и добавляем отступ снизу */
            display: none; /* По умолчанию скрываем */
        }
    </style>
</head>
<body>
    <!-- Навигация -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">🎵 MusicPlayer</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center mb-4">❤️ Избранные треки</h1>

        <!-- Аудиоплеер -->
        <audio id="audioPlayer" controls>
            <source src="" type="audio/mp3">
            Ваш браузер не поддерживает аудио.
        </audio>

        <!-- Проверяем, есть ли избранные треки -->
        <?php if (!empty($likedTracks)): ?>
            <ul class="list-group">
                <?php foreach ($likedTracks as $track): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($track['title']) ?>
                        <!-- Кнопка для воспроизведения -->
                        <button class="btn btn-primary btn-sm play-track" data-url="../uploads/<?= $track['file'] ?>">▶</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-center">Нет избранных треков. Лайкните что-нибудь!</p>
        <?php endif; ?>
    </div>

    <!-- Подключение Bootstrap и пользовательского JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Логика воспроизведения треков
        document.querySelectorAll('.play-track').forEach(button => {
            button.addEventListener('click', function () {
                const audioPlayer = document.getElementById('audioPlayer');
                const trackUrl = this.dataset.url;

                // Если трек отличается от текущего, загружаем и воспроизводим
                if (audioPlayer.src !== trackUrl) {
                    audioPlayer.src = trackUrl;
                    audioPlayer.style.display = 'block'; // Показываем плеер
                    audioPlayer.play();
                } else if (audioPlayer.paused) {
                    // Если трек уже загружен, но на паузе, воспроизводим
                    audioPlayer.play();
                } else {
                    // Если трек воспроизводится, ставим на паузу
                    audioPlayer.pause();
                }
            });
        });
    </script>
</body>
</html>
