<?php
// Starta sessionen för att komma åt inloggad användares data
session_start();

// Inkludera databasanslutningen
include 'config.php';

// om användaren inte är inloggad skickas de till login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Hämta inloggad användares ID från sessionen
$userId = $_SESSION['user_id'];

// Variabler för feedback-meddelanden till användaren
$message = "";
$toastClass = "";


//Definierar request method som post
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Hämta och rensa quiz namnet från formuläret
    $quizName = isset($_POST['quiz_name']) ? trim($_POST['quiz_name']) : '';

    // Hämta frågorna som en array (från de dynamiska formulärfälten)
    $questions = isset($_POST['questions']) ? $_POST['questions'] : [];

    // quiz-namn får inte vara tomt
    if (empty($quizName)) {
        $message = "Du måste ange ett namn på quizet.";
        $toastClass = "error";

    //minst en fråga måste finnas
    } elseif (empty($questions)) {
        $message = "Du måste lägga till minst en fråga.";
        $toastClass = "error";

    } else {

       
        //sparar quiz_name och kopplar det till inloggad user_id
        $stmtQuiz = $conn->prepare("INSERT INTO quiz (quiz_name, user_id) VALUES (?, ?)");
        $stmtQuiz->bind_param("si", $quizName, $userId);

        if ($stmtQuiz->execute()) {
            //Hämtar det genererade quiz_id för det nya quizet
            //koppla frågorna till rätt quiz
            $quizId = $conn->insert_id;
            $stmtQuiz->close();

                 $stmtQ = $conn->prepare(
                "INSERT INTO question (quiz_id, quest_name, alternative1, alternative2, alternative3, alternative4, correct_answer)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $hasError = false;

            foreach ($questions as $index => $q) {
                // Rensa och hämta varje fälts värde
                $questName  = trim($q['quest_name']   ?? '');
                $alt1       = trim($q['alternative1']  ?? '');
                $alt2       = trim($q['alternative2']  ?? '');
                $alt3       = trim($q['alternative3']  ?? '');
                $alt4       = trim($q['alternative4']  ?? '');
                $correct    = trim($q['correct_answer'] ?? '');

                // Hoppa över tomma fråger
                if (empty($questName)) continue;

                // kolla att alla alternativ är ifyllda
                if (empty($alt1) || empty($alt2) || empty($alt3) || empty($alt4)) {
                    $message = "Fråga " . ($index + 1) . ": Alla fyra alternativ måste fyllas i.";
                    $toastClass = "error";
                    $hasError = true;
                    break;
                }

                //kolla att ett rätt svar är valt
                if (empty($correct)) {
                    $message = "Fråga " . ($index + 1) . ": Du måste välja ett rätt svar.";
                    $toastClass = "error";
                    $hasError = true;
                    break;
                }

                // Koppla parametrarna till SQL och kör den
                $stmtQ->bind_param("issssss", $quizId, $questName, $alt1, $alt2, $alt3, $alt4, $correct);

                if (!$stmtQ->execute()) {
                    $message = "Fel vid sparande av fråga " . ($index + 1) . ": " . $stmtQ->error;
                    $toastClass = "error";
                    $hasError = true;
                    break;
                }
            }

            $stmtQ->close();

            //visa  lyckat meddelande
            if (!$hasError) {
                $message = "Quizet \"" . htmlspecialchars($quizName) . "\" skapades!";
                $toastClass = "success";
                header('Location: success.php');
            }

        } else {
            $message = "Kunde inte skapa quizet: " . $stmtQuiz->error;
            $toastClass = "error";
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizBook - Skapa Quiz</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Climate+Crisis:YEAR@1979&family=Honk:MORF@15&family=Special+Gothic+Expanded+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="create.css">
</head>
<body class="background">

<?php include 'head.php'; ?>
    <h1 class="normal">Skapa quiz</h1>
<main>
    <form action="create_quiz.php" method="post" id="quizForm">
        <div class="">
            <h3 class ='normal'>Quiz-namn</h3>
            <input
                type="text"
                name="quiz_name"
                placeholder="Namnge ditt quiz"
                value="<?= htmlspecialchars($_POST['quiz_name'] ?? '') ?>"
            >
        </div>

        <div class="cards background questions-list" id="questionsList">
        </div>

        <div class="buttons-container">
            <button type="button" class="btn-add-question normal" onclick="addQuestion()">
            + Lägg till fråga
        </button>

        <button type="submit" class="btn-submit normal">Spara quiz</button>
        </div>

        
    </form>
</main>

<script>
    /**
      Skapar ett nytt fråge-kort och lägger till det i listan. Varje fält namnges med questions[index][fältnamn] så att PHP. tar emot dem som en array.
     */
    function addQuestion() {
        const index = document.querySelectorAll('.question-card').length; // Basera indexet på antalet frågor

        const list = document.getElementById('questionsList');

        // Skapa ett nytt div-element för frågekortet
        const card = document.createElement('div');
        card.className = 'question-card normal';
        card.id = `question-${index}`;

        // Bygg HTML-strukturen för kortet
        // Notera att radioknapparna delar samma name inom frågan,
        // och value sätts till alternativets namn (alt1/alt2/alt3/alt4)
        card.innerHTML = `
            <div class="q-header">
                <span class="q-number">Fråga ${index + 1}</span>
                <button type="button" class="btn-remove" onclick="removeQuestion(${index})" title="Ta bort fråga">×</button>
            </div>

    
            <div class="q-name-input">
                <input
                    type="text"
                    name="questions[${index}][quest_name]"
                    placeholder="Skriv din fråga här…"
                >
            </div>


            <div class="alternatives-grid">
                ${['alternative1','alternative2','alternative3','alternative4'].map((alt, i) => `
                    <div class="alt-row">

                        <input
                            type="radio"
                            name="questions[${index}][correct_answer]"
                            id="correct_${index}_${i}"
                            value="${alt}"
                        >
                        <label class="radio-dot" for="correct_${index}_${i}"></label>
                        <input
                            type="text"
                            name="questions[${index}][${alt}]"
                            placeholder="Alternativ ${i + 1}"
                        >
                    </div>
                `).join('')}
            </div>

        `;
        list.appendChild(card);

    }

    /**
    Uppdaterar nummreringen på alla frågorna
     */
    function updateQuestionNumbers() {
        const cards = document.querySelectorAll('.question-card');
        cards.forEach((card, index) => {
            const numberSpan = card.querySelector('.q-number');
            if (numberSpan) {
                numberSpan.textContent = `Fråga ${index + 1}`;
            }
        });
    }

    /**
    Ta bort fråga
     */
    function removeQuestion(index) {
        const card = document.getElementById(`question-${index}`);
        if (card) {
            // Animera bort kortet innan det tas bort ur DOM
            card.style.transition = 'opacity 0.2s, transform 0.2s';
            card.style.opacity = '0';
            card.style.transform = 'translateY(-8px)';
            setTimeout(() => {
                card.remove();
                updateQuestionNumbers(); // Uppdatera numreringen
            }, 200);
        }
    }

    // Lägg till en fråg
    addQuestion();
</script>
</body>
</html>