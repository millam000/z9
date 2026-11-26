<?php
require_once __DIR__ . '/geo_block.php';
session_start();
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Rejestracja – Portal Z9</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="twoj_css.css">
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<main class="container mt-5" style="max-width:500px;padding:30px 15px;">
    <h1 class="h3 mb-3 text-center">Rejestracja</h1>
    <?php
        if ($error === '1') echo '<div class="alert alert-danger">Hasła nie są identyczne</div>';
        if ($error === '2') echo '<div class="alert alert-danger">Pola nie mogą być puste</div>';
        if ($error === '3') echo '<div class="alert alert-danger">Taki użytkownik już istnieje</div>';
        if ($error === '4') echo '<div class="alert alert-danger">Login może zawierać tylko litery, cyfry, _ oraz -</div>';
    ?>
    <form method="post" action="add.php" enctype="multipart/form-data" id="registerForm">
        <div class="mb-3">
            <label for="user" class="form-label">Login</label>
            <input type="text" class="form-control" id="user" name="user" maxlength="30" required>
        </div>
        <div class="mb-3">
            <label for="pass" class="form-label">Hasło</label>
            <input type="password" class="form-control" id="pass" name="pass" maxlength="30" required>
        </div>
        <div class="mb-3">
            <label for="pass2" class="form-label">Powtórz hasło</label>
            <input type="password" class="form-control" id="pass2" name="pass2" maxlength="30" required>
            <div id="passFeedback" class="form-text"></div>
        </div>
        <div class="mb-3">
            <label for="avatar" class="form-label">Avatar (opcjonalnie)</label>
            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
        </div>
        <button class="w-100 btn btn-primary" type="submit" id="submitBtn">Zarejestruj</button>
        <p class="mt-3 text-center"><a href="login.php">Masz już konto? Zaloguj się</a></p>
    </form>
</main>

<script>
(function () {
    const pass      = document.getElementById('pass');
    const pass2     = document.getElementById('pass2');
    const feedback  = document.getElementById('passFeedback');
    const submitBtn = document.getElementById('submitBtn');
    const form      = document.getElementById('registerForm');

    function checkPasswords() {
        const p1 = pass.value, p2 = pass2.value;
        if (p2 === '') {
            feedback.textContent = '';
            feedback.className = 'form-text';
            pass2.classList.remove('is-valid', 'is-invalid');
            submitBtn.disabled = false;
            return;
        }
        if (p1 === p2) {
            feedback.textContent = 'Hasła są zgodne ✓';
            feedback.className = 'form-text text-success';
            pass2.classList.remove('is-invalid');
            pass2.classList.add('is-valid');
            submitBtn.disabled = false;
        } else {
            feedback.textContent = 'Hasła nie są identyczne';
            feedback.className = 'form-text text-danger';
            pass2.classList.remove('is-valid');
            pass2.classList.add('is-invalid');
            submitBtn.disabled = true;
        }
    }

    pass.addEventListener('input', checkPasswords);
    pass2.addEventListener('input', checkPasswords);

    form.addEventListener('submit', function (e) {
        if (pass.value !== pass2.value) {
            e.preventDefault();
            checkPasswords();
            pass2.focus();
        }
    });
})();
</script>

<script src="twoj_js.js"></script>
</body>
</html>