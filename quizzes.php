<?php
session_start();
include 'config.php';

$result = $conn->query("SELECT quiz_id, quiz_name FROM quiz");
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>QuizBook - Alla quiz</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Climate+Crisis:YEAR@1979&family=Honk:MORF@15&family=Special+Gothic+Expanded+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="quizzes.css">
</head>
<?php include 'head.php'; ?>
<body class="background normal">
    <a href="index.php">Tillbaka</a>
    <h1>Alla quiz</h1>
    <?php if ($result->num_rows === 0): ?>
        <p>Inga quiz hittades.</p>
    <?php else: ?>
        <ul>
            <?php while ($quiz = $result->fetch_assoc()): ?>
                <li>
                    <a href="questions.php?quiz_id=<?= $quiz['quiz_id'] ?>">
                        <?= htmlspecialchars($quiz['quiz_name']) ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>

    <?php $conn->close(); ?>
</body>
</html>