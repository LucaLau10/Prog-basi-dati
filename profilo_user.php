<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

$pdo = new PDO("mysql:host=localhost;dbname=banca", "root", "sofia");

// Recupero dati utente
$stmt = $pdo->prepare("SELECT * FROM Cliente WHERE id_cliente = ?");
$stmt->execute([$_SESSION['user_id']]);
$utente = $stmt->fetch(PDO::FETCH_ASSOC);

// Recupero conti correnti associati
$stmt = $pdo->prepare("SELECT * FROM ContoCorrente WHERE id_cliente = ?");
$stmt->execute([$_SESSION['user_id']]);
$conti = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Profilo Utente - Oakridge Bank</title>
  <link rel="stylesheet" href="style/style_dashboard.css">
</head>
<body>
  <div class="header">
    <div class="dropdown">
      <button>Menu</button>
      <div class="dropdown-content">
        <a href="dashboard.php">Dashboard</a>
        <a href="bonifici.php">Bonifici</a>
        <a href="movimenti.php">Movimenti</a>
        <a href="logout.php">Logout</a>
      </div>
    </div>
    <h1>Oakridge Bank</h1>
  </div>

  <div class="form-container">
    <h2>Dati Personali</h2>
    <p><strong>Nome:</strong> <?= htmlspecialchars($utente['nome']) ?></p>
    <p><strong>Cognome:</strong> <?= htmlspecialchars($utente['cognome']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($utente['email']) ?></p>
    <p><strong>Telefono:</strong> <?= htmlspecialchars($utente['telefono']) ?></p>
  </div>

  <div class="bonifici-storico">
    <h3>Conti Correnti Associati</h3>
    <table>
      <thead>
        <tr>
          <th>Numero Conto</th>
          <th>Saldo (€)</th>
          <th>Data Apertura</th>
          <th>Stato</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($conti as $conto): ?>
          <tr>
            <td><?= htmlspecialchars($conto['numero_conto']) ?></td>
            <td><?= number_format($conto['saldo'], 2) ?></td>
            <td><?= date('d/m/Y', strtotime($conto['data_apertura'])) ?></td>
            <td><?= ucfirst($conto['stato']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
