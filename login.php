<?php
session_start();
require 'config.php';

$errore = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare('SELECT * FROM Cliente WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    $stmt = $pdo->prepare('SELECT * FROM dipendente WHERE username = ?');
    $stmt->execute([$username]);
    $user_dipendente = $stmt->fetch();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id_cliente'];
            header('Location: dashboard.php');
        } else if ($user_dipendente && password_verify($password, $user_dipendente['password'])) {
            $_SESSION['user_id'] = $user_dipendente['id_dipendente'];
            header('Location: dashboard_admin.php');
        } else {
            $errore = "Username o password non corretti.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
        exit();
    } else {
        $errore = "Username o password non corretti.";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login - Oakridge Bank</title>
    <link rel="stylesheet" href="style/style_index.css">
</head>
<body>
    <div class="login-container">
        <h2>Login Oakridge Bank</h2>
        <form action="login.php" method="post">
            <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($username ?? '') ?>" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" value="Login">
        </form>

        <?php if (!empty($errore)): ?>
            <div class="error-message"><?= $errore ?></div>
        <?php endif; ?>

        <p class="signup-link">Non hai ancora un account? <a href="signup.php">Registrati qui</a></p>
    </div>
</body>
</html>
