<?php
session_start();
include 'config.php';

$quizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
if ($quizId === 0) { header("Location: quizzes.php"); exit(); }

$stmtQuiz = $conn->prepare("SELECT quiz_name FROM quiz WHERE quiz_id = ?");
$stmtQuiz->bind_param("i", $quizId);
$stmtQuiz->execute();
$quiz = $stmtQuiz->get_result()->fetch_assoc();
$stmtQuiz->close();

if (!$quiz) { header("Location: quizzes.php"); exit(); }

$stmtQ = $conn->prepare("SELECT quest_name, alternative1, alternative2, alternative3, alternative4, correct_answer FROM question WHERE quiz_id = ?");
$stmtQ->bind_param("i", $quizId);
$stmtQ->execute();
$result = $stmtQ->get_result();
$questions = [];
while ($row = $result->fetch_assoc()) $questions[] = $row;
$stmtQ->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizBook - <?= htmlspecialchars($quiz['quiz_name']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Climate+Crisis:YEAR@1979&family=Special+Gothic+Expanded+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<?php include 'head.php'; ?>
<body class="background normal">

<h1><?= htmlspecialchars($quiz['quiz_name']) ?></h1>

<main>
    <div id="game">
        <p id="counter"></p>
        <div class="cards" id="card">
            <p id="question"></p>
            <div id="alts"></div>
            <p id="feedback"></p>
            <button id="btn-next" onclick="next()">N&auml;sta</button>
        </div>
    </div>

    <div id="result" style="display:none">
        <div class="cards">
            <h2 id="result-title"></h2>
            <p id="result-score"></p>
            <p id="result-sub"></p>
            <button onclick="restart()">Spela igen</button>
            <a href="quizzes.php">Alla quiz</a>
        </div>
    </div>
</main>

<script>
const Q = <?= json_encode($questions, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>;
let i = 0, score = 0, answered = false;

function load() {
    answered = false;
    const q = Q[i];
    document.getElementById('counter').textContent = `Fr\u00e5ga ${i+1} av ${Q.length}`;
    document.getElementById('question').textContent = q.quest_name;
    document.getElementById('feedback').textContent = '';
    document.getElementById('btn-next').style.display = 'none';

    const alts = document.getElementById('alts');
    alts.innerHTML = '';
    ['alternative1','alternative2','alternative3','alternative4'].forEach(key => {
        const btn = document.createElement('button');
        btn.textContent = q[key];
        btn.dataset.key = key;
        btn.onclick = () => pick(key);
        alts.appendChild(btn);
    });
}

function pick(chosen) {
    if (answered) return;
    answered = true;
    const correct = Q[i].correct_answer;
    if (chosen === correct) score++;

    document.querySelectorAll('#alts button').forEach(btn => btn.disabled = true);

    document.getElementById('feedback').textContent =
        chosen === correct ? 'R\u00e4tt!' : 'Fel! R\u00e4tt svar: ' + Q[i][correct];

    const nextBtn = document.getElementById('btn-next');
    nextBtn.style.display = 'block';
    nextBtn.textContent = i < Q.length - 1 ? 'N\u00e4sta' : 'Se resultat';
}

function next() {
    i++;
    if (i < Q.length) {
        load();
    } else {
        document.getElementById('game').style.display = 'none';
        document.getElementById('result').style.display = 'block';
        const pct = score / Q.length;
        document.getElementById('result-score').textContent = score + ' / ' + Q.length;
        document.getElementById('result-title').textContent =
            pct === 1 ? 'Perfekt!' : pct >= 0.8 ? 'Bra jobbat!' : pct >= 0.5 ? 'Helt okej!' : 'F\u00f6rs\u00f6k igen!';
        document.getElementById('result-sub').textContent = Math.round(pct * 100) + '% r\u00e4tt';
    }
}

function restart() {
    i = 0; score = 0;
    document.getElementById('result').style.display = 'none';
    document.getElementById('game').style.display = 'block';
    load();
}

if (Q.length > 0) load();
else document.getElementById('card').textContent = 'Inga fr\u00e5gor i detta quiz.';
</script>
</body>
</html>