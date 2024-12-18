<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT t.title, t.file FROM likes l JOIN tracks t ON l.track_id = t.id WHERE l.user_id = ?");
$stmt->execute([$userId]);
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
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">🎵 MusicPlayer</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center mb-4">❤️ Избранные треки</h1>
        <?php if (!empty($likedTracks)): ?>
            <ul class="list-group">
                <?php foreach ($likedTracks as $track): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($track['title']) ?>
                        <button class="btn btn-primary btn-sm play-track" data-url="../uploads/<?= $track['file'] ?>">▶</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-center">Нет избранных треков. Лайкните что-нибудь!</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
