<?php
session_start();
$errore   = $_SESSION['errore']   ?? '';
$username = $_SESSION['username'] ?? '';
unset($_SESSION['errore'], $_SESSION['username']);
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
    <?php if ($errore): ?>
      <div class="error-message"><?= htmlspecialchars($errore) ?></div>
    <?php endif; ?>
    <form action="login.php" method="post">
      <input type="text" name="username" placeholder="Username"
             value="<?= htmlspecialchars($username) ?>" required><br>
      <input type="password" name="password" placeholder="Password" required><br>
      <input type="submit" value="Login">
    </form>
    <p>Non hai un account? <a href="signup.php">Registrati qui</a></p>
  </div>
</body>
</html>
