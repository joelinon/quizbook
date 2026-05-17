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
    <link rel="stylesheet" href="register.css">
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

            <p class="login-link">Har du redan ett konto? <a id="reg" href="login.php">Logga in</a></p>
        </form>
    </div>
</main>

</body>
</html>