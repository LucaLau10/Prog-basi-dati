<?php
session_start();
require 'config.php';

// 1) Protezione: se non loggato, torno al login
if (!isset($_SESSION['id_cliente'])) {
    header('Location: index.php');
    exit();
}

// 2) Recupero tutti i conti di questo utente
$stmt = $pdo->prepare("
    SELECT id_conto, saldo
      FROM ContoCorrente
     WHERE id_cliente = :id_cliente
");
$stmt->execute(['id_cliente' => $_SESSION['id_cliente']]);
$conti = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3) Se l’utente non ha conti, mostro “Apri conto”
if (count($conti) === 0) {
    ?>
    <!DOCTYPE html>
    <html lang="it">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="style/style_dashboard.css">
      <title>Apri Conto - Oakridge Bank</title>
    </head>
    <body class="no-account">
      <h1>Non hai ancora un conto corrente associato</h1>
      <button class="open-account-btn" onclick="location.href='apri_conto.php'">
        Apri Conto Corrente
      </button>
    </body>
    </html>
    <?php
    exit();
}

// 4) Gestisco la selezione del conto via GET (o imposto il primo di default)
$selectedId = isset($_GET['id_conto']) 
    ? intval($_GET['id_conto']) 
    : $conti[0]['id_conto'];

// Verifico che esista realmente tra quelli dell’utente
$contoselezionato = null;
foreach ($conti as $c) {
    if ($c['id_conto'] === $selectedId) {
        $contoselezionato = $c;
        break;
    }
}
// Se il parametro era fantasma, ricado sul primo
if (!$contoselezionato) {
    $contoselezionato = $conti[0];
    $selectedId = $contoselezionato['id_conto'];
}

// 5) Preparo i dati da visualizzare
$saldo = $contoselezionato['saldo'];

// Ultimi 5 bonifici in uscita per il conto selezionato
$stmt = $pdo->prepare("
    SELECT 
      b.id_bonifico,
      u.Username      AS destinatario,
      b.importo,
      b.causale,
      b.data,
      b.stato
    FROM Bonifico b
    JOIN ContoCorrente c2 
      ON b.id_conto_destinatario = c2.id_conto
    JOIN cliente u 
      ON c2.id_cliente = u.id_cliente
    WHERE b.id_conto_mittente = :id_conto
    ORDER BY b.data DESC
    LIMIT 5
");
$stmt->execute(['id_conto' => $selectedId]);
$bonifici = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style/style_dashboard.css">
  <title>Dashboard - Oakridge Bank</title>
</head>
<body>
  <div class="header">
    <div class="dropdown">
      <button>Menu</button>
      <div class="dropdown-content">
        <a href="profilo_user.php">Profilo</a>
        <a href="bonifici.php">Bonifici</a>
        <a href="movimenti.php">Movimenti</a>
        <a href="logout.php">Logout</a>
      </div>
    </div>
    <h1>Oakridge Bank</h1>
  </div>

  <div class="main-content">
    <h2>Benvenuto nella tua dashboard!</h2>

    <!-- Se ho più conti, offro un selector -->
    <?php if (count($conti) > 1): ?>
      <form method="get" class="account-selector">
        <label for="id_conto">Seleziona il conto:</label>
        <select name="id_conto" onchange="this.form.submit()">
          <?php foreach ($conti as $c): ?>
            <option value="<?= $c['id_conto'] ?>"
              <?= $c['id_conto'] === $selectedId ? 'selected' : '' ?>>
              Conto #<?= $c['id_conto'] ?> — Saldo € <?= number_format($c['saldo'], 2) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>
    <?php endif; ?>

    <p>Saldo corrente: <strong>€ <?= number_format($saldo, 2) ?></strong></p>
  </div>

  <?php if (count($bonifici) > 0): ?>
    <div class="bonifici-storico">
      <h3>Ultimi 5 bonifici inviati (Conto #<?= $selectedId ?>)</h3>
      <table>
        <thead>
          <tr>
            <th>Destinatario</th>
            <th>Importo (€)</th>
            <th>Causale</th>
            <th>Data</th>
            <th>Stato</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($bonifici as $b): ?>
            <tr>
              <td data-label="Destinatario">
                <?= htmlspecialchars($b['destinatario']) ?>
              </td>
              <td><?= number_format($b['importo'], 2) ?></td>
              <td><?= htmlspecialchars($b['causale']) ?></td>
              <td><?= date('d/m/Y H:i:s', strtotime($b['data'])) ?></td>
              <td><?= ucfirst($b['stato']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</body>
</html>
