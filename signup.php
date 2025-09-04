<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Registrazione - Oakridge Bank</title>
  <link rel="stylesheet" href="style/style_signup.css">
</head>
<body>
  <div class="container">
    <h1>Registrazione Oakridge Bank</h1>
    <form action="register.php" method="post">

    <?php if (isset($_GET['error']) && $_GET['error'] === 'username'): ?>
    <p style="color: red;">❌ Username già esistente. Scegli un altro.</p>
    <?php endif; ?>
  <div class="row">
    <div class="field">
      <label for="nome">Nome</label>
      <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>" placeholder="Mario" required>
    </div>
    <div class="field">
      <label for="cognome">Cognome</label>
      <input type="text" id="cognome" name="cognome" value="<?= htmlspecialchars($_GET['cognome'] ?? '') ?>" placeholder="Rossi" required>
    </div>
  </div>

  <label for="codice_fiscale">Codice Fiscale</label>
  <input type="text" id="codice_fiscale" name="codice_fiscale" value="<?= htmlspecialchars($_GET['codice_fiscale'] ?? '') ?>"  required>

  <div class="row">
    <div class="field">
      <label for="data_nascita">Data di Nascita</label>
      <input type="date" id="data_nascita" name="data_nascita" value="<?= htmlspecialchars($_GET['data_nascita'] ?? '') ?>" required>
    </div>
    <div class="field">
      <label for="telefono">Telefono</label>
      <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($_GET['telefono'] ?? '') ?>" required>
    </div>
  </div>

  <label for="email">Email</label>
  <input type="email" id="email" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>" placeholder="esempio@email.it" required>

  <label for="indirizzo">Indirizzo</label>
  <input type="text" id="indirizzo" name="indirizzo" value="<?= htmlspecialchars($_GET['indirizzo'] ?? '') ?>" required>

  <label for="username">Username</label>
  <input type="text" id="username" value="<?= htmlspecialchars($_GET['username'] ?? '') ?>" name="username" required>

  <label for="password">Password</label>
  <input type="password" id="password" name="password" required>

  <label for="confirm">Conferma Password</label>
  <input type="password" id="confirm" name="confirm" required>
   
  <button type="submit">Registrati</button>
  <div class="error"></div>
</form>
    <p>Hai già un account? <a href="index.php">Accedi qui</a></p>
  </div>
</body>
</html>
