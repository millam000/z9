<?php declare(strict_types=1);
require_once __DIR__ . '/geo_block.php';
require_once __DIR__ . '/db_config.php';

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$currentUser = (string) $_SESSION['username'];
$link = getDbConnection();
$res = mysqli_query($link, "SELECT username FROM users ORDER BY username ASC");
$users = [];
while ($row = mysqli_fetch_assoc($res)) {
    if ($row['username'] !== $currentUser) {
        $users[] = $row['username'];
    }
}
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wyślij plik – Portal Z9</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="twoj_css.css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="twoj_js.js"></script>
</head>
<body onload="myLoadHeader()" class="has-header d-flex flex-column min-vh-100">
    <div id="myHeader"></div>

    <main class="container py-5 flex-grow-1" style="max-width: 560px;">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-cloud-upload me-2"></i>Prześlij plik do rozmówcy</h5>
            </div>
            <div class="card-body">
                <form id="standaloneUploadForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="recipient" class="form-label">Odbiorca:</label>
                        <select name="recipient" id="recipient" class="form-select" required>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= htmlspecialchars($u) ?>"><?= htmlspecialchars($u) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fileToUpload" class="form-label">Wybierz plik (JPG, PNG, GIF, MP3, MP4, PDF...):</label>
                        <input type="file" class="form-control" name="fileToUpload" id="fileToUpload" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Wyślij plik</button>
                </form>
                <div id="uploadFeedback" class="mt-3"></div>
            </div>
        </div>
    </main>

    <?php require_once 'footer.php'; ?>

    <script>
    $('#standaloneUploadForm').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        $.ajax({
            url: 'upload_msg.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res) {
                $('#uploadFeedback').html('<div class="alert alert-success">Plik przesłany pomyślnie! <a href="z9.php">Wróć do komunikatora</a></div>');
                $('#standaloneUploadForm')[0].reset();
            },
            error: function (xhr) {
                $('#uploadFeedback').html('<div class="alert alert-danger">' + (xhr.responseJSON?.message || 'Błąd przesyłania') + '</div>');
            }
        });
    });
    </script>
</body>
</html>