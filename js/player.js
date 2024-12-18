// Включение музыки
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
        e.preventDefault(); // Останавливаем отправку формы

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

