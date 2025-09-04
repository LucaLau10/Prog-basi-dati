<?php
// Connessione al database
$host = "localhost";
$user = "root";
$password = "sofia";
$dbname = "banca";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
  die("Connessione fallita: " . $conn->connect_error);
}

// Ricezione dati dal form
$nome = $_POST['nome'];
$cognome = $_POST['cognome'];
$codice_fiscale = $_POST['codice_fiscale'];
$data_nascita = $_POST['data_nascita'];
$email = $_POST['email'];
$telefono = $_POST['telefono'];
$indirizzo = $_POST['indirizzo'];
$username = $_POST['username'];
$password = $_POST['password'];
$confirm = $_POST['confirm'];

// Controllo password
if ($password !== $confirm) {
  echo("Le password non coincidono.");
  header("Location: signup.php");
  exit();
}

$stmt = $conn->prepare("SELECT * FROM Cliente WHERE username = ?");
$stmt->execute([$username]);
$result = $stmt->get_result();
if ($result->num_rows == 1) {
  // Ricostruisci la query string
  $query_string = http_build_query([
    'nome' => $nome,
    'cognome' => $cognome,
    'codice_fiscale' => $codice_fiscale,
    'data_nascita' => $data_nascita,
    'email' => $email,
    'telefono' => $telefono,
    'indirizzo' => $indirizzo,
    'password' => $password,
    'confirm' => $confirm,
    'error' => 'username'
  ]);
  
  header("Location: signup.php?$query_string");
  exit();
}


// Inserimento nel database
$sql = "INSERT INTO Cliente (nome, cognome, codice_fiscale, data_nascita, email, telefono, indirizzo, username, password)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssss", $nome, $cognome, $codice_fiscale, $data_nascita, $email, $telefono, $indirizzo, $username, $password);

if ($stmt->execute()) {

  echo "Registrazione avvenuta con successo!";
  header("Location: index.html");
  exit();
  
} else {
  echo "Errore: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>