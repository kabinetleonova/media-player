// Включение музыки: добавляем обработчики событий на кнопки воспроизведения
document.querySelectorAll('.play-track').forEach(button => {
    button.addEventListener('click', function () {
        const audioPlayer = document.getElementById('audioPlayer'); // Получаем элемент аудиоплеера
        const trackUrl = this.dataset.url; // URL трека из data-атрибута кнопки

        if (audioPlayer.src !== trackUrl) { 
            // Если текущий трек в плеере отличается от выбранного, обновляем источник и начинаем воспроизведение
            audioPlayer.src = trackUrl;
            audioPlayer.play();
        } else if (audioPlayer.paused) {
            // Если трек совпадает, но плеер находится на паузе, продолжаем воспроизведение
            audioPlayer.play();
        } else {
            // Если трек воспроизводится, ставим его на паузу
            audioPlayer.pause();
        }
    });
});

// Обработка лайков: добавляем обработчики событий на формы лайков
document.querySelectorAll('.like-form').forEach(form => {
    form.addEventListener('submit', function (e) {
        e.preventDefault(); // Останавливаем стандартное поведение формы (перезагрузку страницы)

        const formData = new FormData(this); // Собираем данные формы
        fetch('index.php', { // Отправляем POST-запрос на сервер
            method: 'POST',
            body: formData,
        })
            .then(response => response.json()) // Парсим JSON-ответ сервера
            .then(data => {
                if (data.success) {
                    // Если запрос успешный, показываем сообщение пользователю
                    alert('Трек добавлен в избранное!');
                } else {
                    // Если возникла ошибка на сервере, отображаем её
                    alert('Ошибка: трек не удалось добавить в избранное.');
                }
            })
            .catch(error => {
                // Обрабатываем возможные ошибки сети или выполнения запроса
                console.error('Ошибка при лайке:', error);
            });
    });
});
