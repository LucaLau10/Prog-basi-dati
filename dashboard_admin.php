<?php
session_start();
// Simulazione login admin (in futuro da proteggere con sessione o login)
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Oakridge Bank</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>

<header>
    <h1>Admin Dashboard - Oakridge Bank</h1>
</header>

<div class="dashboard">
    <div class="section">
        <h2>Clienti</h2>
        <ul>
            <li><a href="#">Visualizza tutti i clienti</a></li>
            <li><a href="#">Crea nuovo cliente</a></li>
            <li><a href="#">Gestisci conti cliente</a></li>
        </ul>
    </div>

    <div class="section">
        <h2>Conti Correnti</h2>
        <ul>
            <li><a href="#">Visualizza tutti i conti</a></li>
            <li><a href="#">Apri nuovo conto</a></li>
            <li><a href="#">Blocca conto</a></li>
        </ul>
    </div>

    <div class="section">
        <h2>Prestiti</h2>
        <ul>
            <li><a href="#">Visualizza richieste</a></li>
            <li><a href="#">Approva/nega prestito</a></li>
            <li><a href="#">Gestione ammortamenti</a></li>
        </ul>
    </div>

    <div class="section">
        <h2>Dipendenti</h2>
        <ul>
            <li><a href="#">Elenco dipendenti</a></li>
            <li><a href="#">Aggiungi nuovo</a></li>
            <li><a href="#">Ruoli e permessi</a></li>
        </ul>
    </div>

    <div class="section">
        <h2>Finanziamenti Interni</h2>
        <ul>
            <li><a href="#">Progetti attivi</a></li>
            <li><a href="#">Budget annuali</a></li>
            <li><a href="#">Revisioni finanziarie</a></li>
        </ul>
    </div>
</div>

</body>
</html>
