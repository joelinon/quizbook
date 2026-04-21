<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizBook – Start</title>
</head>
<body>

<nav>
    <div class="logo">Quiz<span>Book</span></div>
    <div class="nav-right">
        <span class="nav-user">Inloggad som <strong><?= $username ?></strong></span>
        <a href="logout.php" class="btn-logout">Logga ut</a>
    </div>
</nav>

<main>
    <p class="hero-label">Välkommen tillbaka</p>
    <h1>Hej, <em><?= $username ?></em>!</h1>
    <p class="hero-sub">Vad vill du göra idag? Starta ett quiz, utforska frågor eller se dina resultat.</p>

    <div class="cards">
        <a href="#" class="card">
            <div class="card-icon">⚡</div>
            <h3>Starta quiz</h3>
            <p>Testa dina kunskaper med ett slumpmässigt quiz.</p>
            <span class="card-badge">Spela nu</span>
        </a>
        <a href="#" class="card">
            <div class="card-icon">📚</div>
            <h3>Alla frågor</h3>
            <p>Bläddra bland alla tillgängliga frågor i databasen.</p>
            <span class="card-badge">Utforska</span>
        </a>
        <a href="#" class="card">
            <div class="card-icon">🏆</div>
            <h3>Resultat</h3>
            <p>Se dina tidigare resultat och hur du placerar dig.</p>
            <span class="card-badge">Statistik</span>
        </a>
    </div>

    <p class="section-title">Snabbstart</p>
    <div class="cards">
        <a href="#" class="card">
            <div class="card-icon">➕</div>
            <h3>Skapa fråga</h3>
            <p>Lägg till egna frågor till quizet.</p>
        </a>
        <a href="#" class="card">
            <div class="card-icon">⚙️</div>
            <h3>Inställningar</h3>
            <p>Hantera din profil och dina preferenser.</p>
        </a>
    </div>
</main>

</body>
</html>