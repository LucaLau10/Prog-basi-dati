<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

$pdo = new PDO("mysql:host=localhost;dbname=banca", "root", "sofia");

// Recupero conto corrente dell'utente
$stmt = $pdo->prepare("SELECT id_conto FROM ContoCorrente WHERE id_cliente = ?");
$stmt->execute([$_SESSION['user_id']]);
$conto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$conto) {
    echo "<p>Conto corrente non trovato.</p>";
    exit();
}

$id_conto = $conto['id_conto'];

// Gestione ordinamento
$ordina_per = $_GET['ordina'] ?? 'data_desc';
switch ($ordina_per) {
    case 'data_asc':        $order = 'b.data ASC'; break;
    case 'data_desc':       $order = 'b.data DESC'; break;
    case 'importo_asc':     $order = 'b.importo ASC'; break;
    case 'importo_desc':    $order = 'b.importo DESC'; break;
    case 'nome_asc':        $order = 'u.Username ASC'; break;
    case 'nome_desc':       $order = 'u.Username DESC'; break;
    default:                $order = 'b.data DESC';
}

// Bonifici inviati
$stmt = $pdo->prepare("
    SELECT b.*, u.Username AS destinatario
    FROM Bonifico b
    JOIN ContoCorrente c ON b.id_conto_destinatario = c.id_conto
    JOIN cliente u ON c.id_cliente = u.id_cliente
    WHERE b.id_conto_mittente = ?
    ORDER BY $order
");
$stmt->execute([$id_conto]);
$inviati = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Bonifici ricevuti
$stmt = $pdo->prepare("
    SELECT b.*, u.Username AS mittente
    FROM Bonifico b
    JOIN ContoCorrente c ON b.id_conto_mittente = c.id_conto
    JOIN cliente u ON c.id_cliente = u.id_cliente
    WHERE b.id_conto_destinatario = ?
    ORDER BY $order
");
$stmt->execute([$id_conto]);
$ricevuti = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Movimenti - Oakridge Bank</title>
  <link rel="stylesheet" href="style/style_movimenti.css">
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
 <div class="top-bar">
  <h1>Movimenti Bancari</h1>
  <form method="get">
  <label for="ordina">Ordina per:</label>
  <select name="ordina" id="ordina" onchange="this.form.submit()">
    <option value="data_desc" <?= $ordina_per === 'data_desc' ? 'selected' : '' ?>>Data ↓</option>
    <option value="data_asc" <?= $ordina_per === 'data_asc' ? 'selected' : '' ?>>Data ↑</option>
    <option value="importo_desc" <?= $ordina_per === 'importo_desc' ? 'selected' : '' ?>>Importo ↓</option>
    <option value="importo_asc" <?= $ordina_per === 'importo_asc' ? 'selected' : '' ?>>Importo ↑</option>
    <option value="nome_asc" <?= $ordina_per === 'nome_asc' ? 'selected' : '' ?>>Nome A→Z</option>
    <option value="nome_desc" <?= $ordina_per === 'nome_desc' ? 'selected' : '' ?>>Nome Z→A</option>
  </select>
</form>


<div class="movimenti-container">
  <section class="movimenti-section">
    <h2>Bonifici Inviati</h2>
    <table>
      <thead>
        <tr>
          <th>Destinatario</th>
          <th>Importo</th>
          <th>Causale</th>
          <th>Data</th>
          <th>Stato</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($inviati as $b): ?>
        <tr>
          <td><?= htmlspecialchars($b['destinatario']) ?></td>
          <td>€ <?= number_format($b['importo'], 2) ?></td>
          <td><?= htmlspecialchars($b['causale']) ?></td>
          <td><?= date('d/m/Y H:i:s', strtotime($b['data'])) ?></td>
          <td><?= ucfirst($b['stato']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <section class="movimenti-section">
    <h2>Bonifici Ricevuti</h2>
    <table>
      <thead>
        <tr>
          <th>Mittente</th>
          <th>Importo</th>
          <th>Causale</th>
          <th>Data</th>
          <th>Stato</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($ricevuti as $b): ?>
        <tr>
          <td><?= htmlspecialchars($b['mittente']) ?></td>
          <td>€ <?= number_format($b['importo'], 2) ?></td>
          <td><?= htmlspecialchars($b['causale']) ?></td>
          <td><?= date('d/m/Y H:i:s', strtotime($b['data'])) ?></td>
          <td><?= ucfirst($b['stato']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</div>

</body>
</html>