<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$search = $_GET['search'] ?? '';
$userId = $_SESSION['user_id'] ?? null;

// Получение треков с учётом поиска
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM tracks WHERE title LIKE ? ORDER BY id DESC");
    $stmt->execute(['%' . $search . '%']);
} else {
    $stmt = $pdo->query("SELECT * FROM tracks ORDER BY id DESC");
}

$tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Обработка лайков через AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like']) && $userId) {
    $trackId = $_POST['track_id'];
    try {
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, track_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE user_id=user_id");
        $stmt->execute([$userId, $trackId]);
        echo json_encode(['success' => true]);
        exit;
    } catch (Exception $e) {
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
            button.addEventListener('click', function () {
                const audioPlayer = document.getElementById('audioPlayer');
                const trackUrl = this.dataset.url;

                if (audioPlayer.src !== trackUrl) {
                    audioPlayer.src = trackUrl;
                    audioPlayer.play();
                } else if (audioPlayer.paused) {
                    audioPlayer.play();
                } else {
                    audioPlayer.pause();
                }
            });
        });

        // Обработка лайков
        document.querySelectorAll('.like-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Останавливаем стандартное поведение

                const formData = new FormData(this);
                fetch('index.php', {
                    method: 'POST',
                    body: formData,
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Трек добавлен в избранное!');
                        } else {
                            alert('Ошибка: трек не удалось добавить в избранное.');
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка при лайке:', error);
                    });
            });
        });
    </script>
</body>
</html>
