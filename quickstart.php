<?php
session_start();
include 'config.php';

$result = $conn->query("SELECT quiz_id FROM quiz ORDER BY RAND() LIMIT 1");
$quiz = $result->fetch_assoc();
$conn->close();

if ($quiz) {
    header("Location: questions.php?quiz_id=" . $quiz['quiz_id']);
}

else {
    header('Location: index.php');
}
?>