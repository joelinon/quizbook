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
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Climate+Crisis:YEAR@1979&family=Honk:MORF@15&family=Special+Gothic+Expanded+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css" class="css">
</head>
<?php include 'head.php'; ?>
<body class='background normal'>

<main>
    <a href="#">
    <div class="cards">
            <div></div>
            <h3>Snabbstart</h3>
            <p class='normal'>Starta ett slumpmässigt quiz</p>
        
</div>
</a>
<a href="quizzes.php">
<div class="cards">
        
            <div></div>
            <h3>Alla Quiz</h3>
            <p>Bläddra bland alla tillgängliga quiz</p>
</div>
</a>
<a href="create_quiz.php">
    <div class="cards">
            <h3>Skapa Quiz</h3>
            <p>Skapa och ladda upp ditt egna quiz</p>
    </div>
    </a>
</main>
<footer>
    <p>&copy;Jolleski</p>
</footer>
</body>
</html>