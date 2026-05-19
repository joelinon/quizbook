<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Climate+Crisis:YEAR@1979&family=Honk:MORF@15&family=Special+Gothic+Expanded+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="succes.css">
    <title>Quiz Skapades</title>
</head>
<body class="background">
    <?php 
    session_start();
    include 'config.php';
    $username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '';
    include 'head.php'; 
    ?>
    <h1 class='normal'>Quiz Skapades!</h1>
    <h3><a href="index.php"  id='back' class='normal'>Tillbaka till startsidan</a><h3>
</body>
</html>