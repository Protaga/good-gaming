document.addEventListener('DOMContentLoaded', function () {
    const buyButton = document.getElementById('buy_game');

    if (buyButton) {
        buyButton.addEventListener('click', function () {
            var gameId = document.getElementById('gameId').getAttribute('data-game-id');

            var formData = new FormData();
            formData.append('buy_game', true);
            formData.append('game_id', gameId);

            fetch('../ajax/buy_game.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    // Замінюємо кнопку "Buy" на кнопку "Download"
                    buyButton.style.display = 'none';
                    var downloadButton = document.createElement('a');
                    downloadButton.href = "<?php echo $gameFileUrl; ?>";
                    downloadButton.download = '';
                    downloadButton.classList.add('btn', 'btn-success', 'mt-3');
                    downloadButton.style.width = '150px';
                    downloadButton.textContent = 'Download';
                    document.querySelector('.col-md-4').appendChild(downloadButton);

                    // Перезавантажуємо сторінку після покупки
                    window.location.reload();
                } else {
                    alert(data);  // Виводимо помилку
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
});
