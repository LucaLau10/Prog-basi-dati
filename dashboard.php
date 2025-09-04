<?php
session_start();
require 'config.php';

if (!isset($_SESSION['id_cliente'])) {
    header('Location: index.php');
    exit();
}

$stmt = $pdo->query("SELECT * FROM ContoCorrente WHERE id_cliente= " . $_SESSION['id_cliente']);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <link rel="stylesheet" href="style/style_dashboard.css">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Apri Conto - Oakridge Bank</title>
    </head>
    <body class="no-account">
        <h1>Non hai ancora un conto corrente associato</h1>
        <button class="open-account-btn" onclick="location.href='apri_conto.php'">Apri Conto Corrente</button>
    </body>
    </html>
    <?php
    exit();
}

$stmt = $pdo->query("SELECT * FROM ContoCorrente WHERE id_cliente= " . $_SESSION['user_id']);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$saldo = $row ? $row['saldo'] : 0;

$stmt = $pdo->prepare("
    SELECT 
        b.id_bonifico,
        u.Username AS utente_destinatario,
        b.importo,
        b.causale,
        b.data,
        b.stato
    FROM Bonifico b
    JOIN ContoCorrente c ON b.id_conto_destinatario = c.id_conto
    JOIN cliente u ON c.id_cliente = u.id_cliente
    WHERE b.id_conto_mittente = :id_conto
    ORDER BY b.data DESC
    LIMIT 5
");
$stmt->execute(['id_conto' => $row['id_conto']]);
$bonifici = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style/style_dashboard.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <p>Il tuo saldo è: € <?= number_format($saldo, 2) ?></p>
  </div>
  <?php if (count($bonifici) > 0): ?>
  <div class="bonifici-storico">
  <h3>Ultimi 5 Bonifici</h3>
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
          <td data-label="Destinatario"><?= htmlspecialchars($b['utente_destinatario']) ?></td>
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
