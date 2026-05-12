<?php
session_start();
include 'config.php';

// Hämta quiz_id från URL:en
$quizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;

// Om inget quiz_id skickades, gå tillbaka till listan
if ($quizId === 0) {
    header("Location: quizzes.php");
    exit();
}

// Hämta quiz-namnet
$stmtQuiz = $conn->prepare("SELECT quiz_name FROM quiz WHERE quiz_id = ?");
$stmtQuiz->bind_param("i", $quizId);
$stmtQuiz->execute();
$quizResult = $stmtQuiz->get_result();
$quiz = $quizResult->fetch_assoc();
$stmtQuiz->close();

// Hämta alla frågor som tillhör detta quiz
$stmtQ = $conn->prepare("SELECT quest_name, alternative1, alternative2, alternative3, alternative4, correct_answer FROM question WHERE quiz_id = ?");
$stmtQ->bind_param("i", $quizId);
$stmtQ->execute();
$questions = $stmtQ->get_result();
$stmtQ->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>QuizBook - <?= htmlspecialchars($quiz['quiz_name']) ?></title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Climate+Crisis:YEAR@1979&family=Honk:MORF@15&family=Special+Gothic+Expanded+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="create.css">
</head>
<?php include 'head.php'; ?>
<body>
    <a href="quizzes.php">Tillbaka</a>
    <h1><?= htmlspecialchars($quiz['quiz_name']) ?></h1>

    <?php if ($questions->num_rows === 0): ?>
        <p>Det finns inga frågor i detta quiz.</p>
    <?php else: ?>
        <?php $i = 1; while ($q = $questions->fetch_assoc()): ?>
            <div>
                <h3>Fråga <?= $i ?>: <?= htmlspecialchars($q['quest_name']) ?></h3>
                <ul>
                    <li>1. <?= htmlspecialchars($q['alternative1']) ?></li>
                    <li>2. <?= htmlspecialchars($q['alternative2']) ?></li>
                    <li>3. <?= htmlspecialchars($q['alternative3']) ?></li>
                    <li>4. <?= htmlspecialchars($q['alternative4']) ?></li>
                </ul>
            </div>
        <?php $i++; endwhile; ?>
    <?php endif; ?>
</body>
</html>