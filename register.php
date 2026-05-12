<?php
session_start();
include 'config.php';

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email    = isset($_POST['email'])    ? trim($_POST['email'])    : '';
    $password = isset($_POST['password']) ? $_POST['password']       : '';

    if (empty($username) || empty($email) || empty($password)) {
        $message   = "Alla fält måste fyllas i.";
        $toastClass = "error";
    } elseif (strlen($password) < 6) {
        $message   = "Lösenordet måste vara minst 6 tecken.";
        $toastClass = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message   = "Ogiltig e-postadress.";
        $toastClass = "error";
    } else {
        // Kolla om e-posten redan finns
        $checkStmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $message   = "E-postadressen används redan.";
            $toastClass = "error";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $message   = "Kontot skapades! Du kan nu logga in.";
                $toastClass = "success";
            } else {
                $message   = "Fel: " . $stmt->error;
                $toastClass = "error";
            }

            $stmt->close();
        }
        $checkStmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizBook – Registrera</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Climate+Crisis:YEAR@1979&family=Special+Gothic+Expanded+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
    <style>
        main {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 90dvh;
        }
        .cards {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: auto;
            padding: 2rem;
            gap: 1rem;
            width: clamp(280px, 40vw, 480px);
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            width: 100%;
        }
        form h1 {
            text-align: center;
            font-size: clamp(22px, 4vw, 40px);
            color: white;
            font-family: "Special Gothic Expanded One", sans-serif;
        }
        form div {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }
        label {
            font-family: "Special Gothic Expanded One", sans-serif;
            color: rgba(255,255,255,0.7);
            font-size: clamp(11px, 1.5vw, 16px);
        }
        input {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(128,128,128,0.4);
            border-radius: 6px;
            color: white;
            padding: 0.5rem 0.75rem;
            font-family: "Special Gothic Expanded One", sans-serif;
            font-size: clamp(12px, 1.8vw, 18px);
            outline: none;
            transition: border 0.2s;
        }
        input:focus {
            border-color: #008cff;
        }
        button[type="submit"] {
            background: #008cff;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 0.6rem 1rem;
            font-family: "Special Gothic Expanded One", sans-serif;
            font-size: clamp(13px, 2vw, 20px);
            cursor: pointer;
            margin-top: 0.5rem;
            transition: background 0.2s;
        }
        button[type="submit"]:hover {
            background: #0070cc;
        }
        .login-link {
            text-align: center;
            font-family: "Special Gothic Expanded One", sans-serif;
            font-size: clamp(11px, 1.5vw, 16px);
            color: rgba(255,255,255,0.6);
        }
        .login-link a {
            color: #008cff;
        }

        /* Toast */
        .toast {
            position: fixed;
            top: 1.5rem;
            left: 50%;
            transform: translateX(-50%);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-family: "Special Gothic Expanded One", sans-serif;
            font-size: clamp(12px, 1.8vw, 18px);
            color: white;
            z-index: 999;
            animation: fadeInOut 4s forwards;
        }
        .toast.success { background: rgba(0, 180, 80, 0.85); }
        .toast.error   { background: rgba(200, 30, 30, 0.85); }
        @keyframes fadeInOut {
            0%   { opacity: 0; transform: translateX(-50%) translateY(-10px); }
            10%  { opacity: 1; transform: translateX(-50%) translateY(0); }
            80%  { opacity: 1; }
            100% { opacity: 0; }
        }
    </style>
</head>
<body class="background">

<?php if (!empty($message)): ?>
    <div class="toast <?= $toastClass ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<nav>
    <a href="index.php" class="logo">Quiz<span>Book</span></a>
</nav>

<main>
    <div class="cards background">
        <form action="register.php" method="post" class="normal">
            <h1>Registrera</h1>

            <div>
                <label for="username">Användarnamn</label>
                <input type="text" id="username" name="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>
            <div>
                <label for="email">E-post</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div>
                <label for="password">Lösenord</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Skapa konto</button>

            <p class="login-link">Har du redan ett konto? <a href="login.php">Logga in</a></p>
        </form>
    </div>
</main>

</body>
</html>