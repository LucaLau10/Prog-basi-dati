<?php
session_start();
require 'config.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Cerca utente
$stmt = $pdo->prepare("SELECT * FROM cliente WHERE username=?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ( $password == $user['password']) {
    $_SESSION['username']  = $user['username'];
    $_SESSION['id_utente'] = $user['id'];
    $_SESSION['id_cliente']   = $user['id_cliente'];
    header("Location: dashboard.php");
    exit;
}

$_SESSION['errore']   = $user ? "Password non corretta." : "Username non valido.";
$_SESSION['username'] = $username;
header("Location: index.php");
exit;
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
