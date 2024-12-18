<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['track'])) {
    $title = $_POST['title'] ?? '';
    $file = $_FILES['track'];
    $targetDir = __DIR__ . '/../uploads/';
    $filePath = $targetDir . basename($file['name']);

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        $stmt = $pdo->prepare("INSERT INTO tracks (title, file) VALUES (?, ?)");
        $stmt->execute([$title, basename($file['name'])]);
        $success = "Трек успешно загружен!";
    } else {
        $error = "Ошибка загрузки трека.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Загрузка трека</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">🎵 MusicPlayer</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center mb-4">⬆ Загрузить трек</h1>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php elseif (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Название трека</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="track" class="form-label">Файл трека</label>
                <input type="file" class="form-control" id="track" name="track" accept=".mp3" required>
            </div>
            <button type="submit" class="btn btn-primary">Загрузить</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
