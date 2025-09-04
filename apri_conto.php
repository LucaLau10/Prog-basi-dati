<?php
session_start();
if (!isset($_SESSION['id_cliente'])) {
    header('Location: index.php');
    exit();
}

function generaNumeroContoUnico($pdo) {
    do {
        $numero = 'IT' . rand(1000000000, 9999999999); // Esempio: IT1234567890
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM ContoCorrente WHERE numero_conto = ?");
        $stmt->execute([$numero]);
        $esiste = $stmt->fetchColumn();
    } while ($esiste > 0);
    return $numero;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = new PDO("mysql:host=localhost;dbname=banca", "root", "sofia");

    $numero_conto = generaNumeroContoUnico($pdo);
    $saldo = $_POST['primo_versamento'];
    $data_apertura = date('Y-m-d'); // Data del server
    $stato = 'attivo';

    $stmt = $pdo->prepare("
        INSERT INTO ContoCorrente (id_cliente, numero_conto, saldo, data_apertura, stato)
        VALUES (:id_cliente, :numero_conto, :saldo, :data_apertura, :stato)
    ");
    $stmt->execute([
        'id_cliente' => $_SESSION['user_id'],
        'numero_conto' => $numero_conto,
        'saldo' => $saldo,
        'data_apertura' => $data_apertura,
        'stato' => $stato
    ]);

    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Apri Conto - Oakridge Bank</title>
    <link rel="stylesheet" href="style/style_apri_conto.css">
</head>
<body>
    <div class="form-container">
        <h1>Apri un nuovo conto corrente</h1>
        <form method="POST">
            <label for="primo_versamento">Primo Versamento (€)</label>
            <input type="number" name="primo_versamento" id="primo_versamento"  required>

            <button type="submit">Apri Conto</button>
        </form>
    </div>
</body>
</html>
