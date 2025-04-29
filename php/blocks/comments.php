<?php
require '../mysql_connect.php';

$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$gameName = basename($urlPath, '.php');

// Заміна дефісів на пробіли для правильного пошуку гри в БД
$gameName = str_replace('-', ' ', $gameName);
?>

<?php if (isset($_COOKIE['role']) && $_COOKIE['role'] !== 'dev'): ?>
    <div class="card mb-4 mt-5" style="max-width: 400px;">
        <div class="card-body">
            <h5 class="card-title">Leave a comment</h5>
            <form id="addCommentForm" class="text-center">
            <label for="rating">Rating (0-10):</label>
            <span id="rating-value">0</span>
            <input type="range" name="rating" id="rating" min="0" max="10" step="1" value="0" required class="form-control mb-2">
                <textarea name="commentText" id="commentText" required placeholder="Your comment..." class="form-control mb-2" maxlength="1000"></textarea>
                <input type="hidden" name="gameName" id="gameName" value="<?php echo htmlspecialchars($gameName); ?>">
                <div class="alert alert-danger mt-2 d-none" id="commentError"></div>
                <button type="button" id="submitComment" class="btn btn-primary mt-2">Add Comment</button>
            </form>
        </div>
    </div>
<?php endif; ?>

<div id="commentsList" class="mt-4">
<?php
require '../mysql_connect.php';

// Заміна дефісів на пробіли для правильного пошуку гри в БД
$gameName = str_replace('-', ' ', $gameName);

// Отримання ID гри за її назвою
$stmt = $pdo->prepare("SELECT id FROM games WHERE name = ?");
$stmt->execute([$gameName]);
$gameId = $stmt->fetchColumn();

if ($gameId) {
    // Виведення середнього рейтингу гри
    $stmt = $pdo->prepare("SELECT AVG(rating) FROM reviews WHERE game = ?");
    $stmt->execute([$gameId]);
    $averageRating = $stmt->fetchColumn();

    if ($averageRating !== null) {
        $averageRating = round($averageRating, 1);
        echo '<div class="alert alert-info">Average Rating: ' . $averageRating . '/10</div>';
    } else {
        echo '<div class="alert alert-info">No ratings yet</div>';
    }

    // Виведення коментарів
    $stmt = $pdo->prepare("SELECT reviews.id, reviews.rating, reviews.text, users.login, users.avatar_path, reviews.date_add 
                           FROM reviews
                           JOIN users ON reviews.user = users.id
                           WHERE reviews.game = ? ORDER BY reviews.date_add DESC");
    $stmt->execute([$gameId]);
    $comments = $stmt->fetchAll();

    // Перевіряємо, чи користувач — розробник цієї гри
    $developerId = null;
    if (isset($_COOKIE['role']) && $_COOKIE['role'] === 'dev') {
        // Отримати developer_id гри
        $stmt = $pdo->prepare("SELECT developer FROM games WHERE id = ?");
        $stmt->execute([$gameId]);
        $developerId = $stmt->fetchColumn();
    }

    if ($comments) {
        foreach ($comments as $comment) {
            $formattedDate = date("F j, Y, g:i a", strtotime($comment['date_add']));
            $avatar = htmlspecialchars($comment['avatar_path']);
            echo '<div class="card mb-2" id="comment_' . $comment['id'] . '">';
            echo '  <div class="card-body d-flex align-items-start">';
            echo '    <img src="' . $avatar . '" alt="Avatar" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">';
            echo '    <div>';
            echo '      <h6 class="card-title">' . htmlspecialchars($comment['login']) . ' - Rating: ' . $comment['rating'] . '</h6>';
            echo '      <p class="card-text">' . nl2br(htmlspecialchars($comment['text'])) . '</p>';
            echo '      <p class="text-muted">Posted on: ' . $formattedDate . '</p>';
            echo '    </div>';
            echo '  </div>';
            echo '</div>';

            // Якщо користувач — розробник цієї гри
            if (isset($_COOKIE['id']) && $_COOKIE['id'] == $developerId) {
                // Перевірити, чи є відповідь на цей коментар
                $stmtReply = $pdo->prepare("SELECT id FROM replies WHERE comment_id = ?");
                $stmtReply->execute([$comment['id']]);
                if (!$stmtReply->fetch()) {
                    // Вивести кнопку для відповіді
                    echo '<button class="btn btn-sm btn-success reply-btn mb-3" data-bs-toggle="modal" data-bs-target="#replyModal" data-comment-id="' . $comment['id'] . '">Reply</button>';
                } else {
                    // Якщо відповідь існує, вивести її текст
                    $stmtReplyText = $pdo->prepare("SELECT reply_text, reply_date FROM replies WHERE comment_id = ?");
                    $stmtReplyText->execute([$comment['id']]);
                    $reply = $stmtReplyText->fetch();
                    if ($reply) {
                        echo '<div class="mt-2 p-2 bg-light rounded">';
                        echo '<strong>Developer reply:</strong><br>' . nl2br(htmlspecialchars($reply['reply_text']));
                        echo '<p class="text-muted">Replied on: ' . date("F j, Y, g:i a", strtotime($reply['reply_date'])) . '</p>';
                        echo '</div>';
                    }
                }
            } else {
                // Якщо відповідь вже є, вивести її текст для всіх користувачів
                $stmtReplyText = $pdo->prepare("SELECT reply_text, reply_date FROM replies WHERE comment_id = ?");
                $stmtReplyText->execute([$comment['id']]);
                $reply = $stmtReplyText->fetch();
                if ($reply) {
                    echo '<div class="mt-2 p-2 bg-light rounded">';
                    echo '<strong>Developer reply:</strong><br>' . nl2br(htmlspecialchars($reply['reply_text']));
                    echo '<p class="text-muted">Replied on: ' . date("F j, Y, g:i a", strtotime($reply['reply_date'])) . '</p>';
                    echo '</div>';
                }
            }
        }
    } else {
        echo "No comments yet.";
    }
} else {
    echo "Game not found";
}
?>
</div>

<!-- МОДАЛЬНЕ ВІКНО -->
<div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="replyForm">
        <div class="modal-header">
          <h5 class="modal-title" id="replyModalLabel">Reply to Comment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <textarea class="form-control" id="replyText" name="replyText" rows="4" required placeholder="Your reply..."></textarea>
          <input type="hidden" id="replyCommentId" name="commentId">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Send Reply</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- СКРИПТ НА ВІДПРАВКУ КОМЕНТАРЯ -->
<script>
$('#submitComment').click(function () {
    var formData = new FormData($('#addCommentForm')[0]);

    $.ajax({
        url: '../ajax/comments.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            if (data.trim() == 'Ready') {
                $('#commentError').hide();
                location.reload(); 
            } else {
                $('#commentError').removeClass('d-none').show();
                $('#commentError').text(data);
            }
        }
    });
});

// Відкриття модального вікна та заповнення прихованого поля ID коментаря
$(document).on('click', '.reply-btn', function () {
    var commentId = $(this).data('comment-id');
    console.log('Comment ID:', commentId); // Додаємо для перевірки
    $('#replyCommentId').val(commentId); 
    var myModal = new bootstrap.Modal(document.getElementById('replyModal'));
    myModal.show();
});

// Відправка відповіді на коментар
$('#replyForm').submit(function (e) {
    e.preventDefault();

    $.ajax({
        url: '../ajax/reply.php',
        type: 'POST',
        data: $(this).serialize(),
        success: function (response) {
            if (response.trim() == 'Ready') {
                $('#replyModal').modal('hide');
                location.reload();
            } else {
                alert(response);
            }
        }
    });
});

$('#replyModal').on('hidden.bs.modal', function () {
    // Знімаємо будь-які затемнювальні стилі
    $('body').css('overflow', 'auto'); // Відновлюємо прокручування сторінки, якщо воно було заблоковано
    $('.modal-backdrop').remove(); // Видаляємо затемнення
});

document.getElementById('rating').addEventListener('input', function() {
    document.getElementById('rating-value').textContent = this.value;
});
</script>
