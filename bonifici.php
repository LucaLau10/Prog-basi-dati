<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

$pdo = new PDO("mysql:host=localhost;dbname=banca", "root", "sofia");

// Recupero conto corrente del mittente
$stmt = $pdo->prepare("SELECT * FROM ContoCorrente WHERE id_cliente = ?");
$stmt->execute([$_SESSION['user_id']]);
$mittente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mittente) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <link rel="stylesheet" href="style/style_bonifici.css">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bonifici - Oakridge Bank</title>
    </head>
    <body class="no-account">
        <h1>Non hai ancora un conto corrente associato</h1>
        <button class="open-account-btn" onclick="location.href='apri_conto.php'">Apri Conto Corrente</button>
    </body>
    </html>
    <?php
    exit();
}

$errors = [];
$numero_conto = '';
$importo = '';
$causale = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_conto = trim($_POST['numero_conto'] ?? '');
    $importo = trim($_POST['importo'] ?? '');
    $causale = trim($_POST['causale'] ?? '');
    $data = date('Y-m-d H:i:s');
    $stato = 'in_attesa';

    // Recupero conto destinatario completo
    $stmt = $pdo->prepare("SELECT id_conto, stato FROM ContoCorrente WHERE numero_conto = ?");
    $stmt->execute([$numero_conto]);
    $destinatario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Validazioni
    if (empty($numero_conto)) {
        $errors['numero_conto'] = "Inserisci il codice del conto destinatario.";
    } else {
        // Recupero ID del conto destinatario
        $stmt = $pdo->prepare("SELECT id_conto FROM ContoCorrente WHERE numero_conto = ?");
        $stmt->execute([$numero_conto]);
        $id_destinatario = $stmt->fetchColumn();

        if (!$id_destinatario) {
            $errors['numero_conto'] = "Il conto destinatario non esiste.";
        }

        if (strtolower($destinatario['stato']) !== 'attivo') {
            $errors['numero_conto'] = "Il conto destinatario è '" . htmlspecialchars($destinatario['stato']) . "' e non può ricevere bonifici.";
        }
    }

    if ($importo > $mittente['saldo']) {
    $errors['importo'] = "Saldo insufficiente per completare il bonifico.";
    }


    if (!is_numeric($importo) || $importo <= 0) {
        $errors['importo'] = "L'importo deve essere un numero positivo.";
    }

    if (empty($causale)) {
        $errors['causale'] = "La causale è obbligatoria.";
    }

    // Se tutto è ok
    if (empty($errors)) {
        $stato = 'eseguito';
        $stmt = $pdo->prepare("
            INSERT INTO Bonifico (id_conto_mittente, id_conto_destinatario, importo, causale, data, stato)
            VALUES (:mittente, :destinatario, :importo, :causale, :data, :stato)
        ");
        $stmt->execute([
            'mittente' => $mittente['id_conto'],
            'destinatario' => $id_destinatario,
            'importo' => $importo,
            'causale' => $causale,
            'data' => $data,
            'stato' => $stato
        ]);
        // Aggiorno saldo mittente
        $stmt = $pdo->prepare("
            UPDATE ContoCorrente SET saldo = saldo - :importo WHERE id_conto = :mittente
        ");
        $stmt->execute([
            'importo' => $importo,
            'mittente' => $mittente['id_conto']
        ]);

        header('Location: dashboard.php');
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Invia Bonifico - Oakridge Bank</title>
    <link rel="stylesheet" href="style/style_bonifici.css">
</head>
<body>
  <div class="header">
    <div class="dropdown">
      <button>Menu</button>
      <div class="dropdown-content">
        <a href="dashboard.php">Dashboard</a>
        <a href="profilo_user.php">Profilo</a>
        <a href="movimenti.php">Movimenti</a>
        <a href="logout.php">Logout</a>
      </div>
    </div>
    <h1>Invia Bonifico</h1>
  </div>

  <div class="form-container">
    <h2>Compila il modulo per inviare un bonifico</h2>
      <form method="POST">
    <label for="numero_conto">Codice Conto Destinatario</label>
    <input type="text" name="numero_conto" id="numero_conto" value="<?= htmlspecialchars($numero_conto ?? '') ?>">
    <?php if (isset($errors['numero_conto'])): ?>
      <div class="error"><?= $errors['numero_conto'] ?></div>
    <?php endif; ?>

    <label for="importo">Importo (€)</label>
    <input type="number" name="importo" id="importo" step="0.01" value="<?= htmlspecialchars($importo ?? '') ?>">
    <?php if (isset($errors['importo'])): ?>
      <div class="error"><?= $errors['importo'] ?></div>
    <?php endif; ?>

    <label for="causale">Causale</label>
    <input type="text" name="causale" id="causale" maxlength="256" value="<?= htmlspecialchars($causale ?? '') ?>">
    <?php if (isset($errors['causale'])): ?>
      <div class="error"><?= $errors['causale'] ?></div>
    <?php endif; ?>

    <button type="submit">Invia Bonifico</button>
  </form>
  </div>
</body>
</html>
