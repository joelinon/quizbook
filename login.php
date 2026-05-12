<?php
session_start();
include 'config.php';

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        $message = "Fyll i både användarnamn och lösenord.";
        $toastClass = "error";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $dbUsername, $dbPassword);
            $stmt->fetch();

            if (password_verify($password, $dbPassword)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $dbUsername;
                header("Location: index.php");
                exit();
            } else {
                $message = "Fel lösenord.";
                $toastClass = "error";
            }
        } else {
            $message = "Användaren hittades inte.";
            $toastClass = "error";
        }

        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Climate+Crisis:YEAR@1979&family=Special+Gothic+Expanded+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
    <title>QuizBook – Logga in</title>
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
        .register-link {
            text-align: center;
            font-family: "Special Gothic Expanded One", sans-serif;
            font-size: clamp(11px, 1.5vw, 16px);
            color: rgba(255,255,255,0.6);
        }
        .register-link a {
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
    <span class="logo">Quiz<span>Book</span></span>
</nav>

<main>
    <div class="cards background">
        <form action="login.php" method="post" class="normal">
            <h1>Logga in</h1>

            <div>
                <label for="username">Användarnamn</label>
                <input type="text" name="username" id="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>
            <div>
                <label for="password">Lösenord</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit">Logga in</button>

            <p class="register-link">Inget konto? <a href="register.php">Registrera dig</a></p>
        </form>
    </div>
</main>

</body>
</html>