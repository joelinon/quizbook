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

            <button type="submit" class="normal">Logga in</button>

            <p class="register-link">Inget konto? <a id='reg' href="register.php">Registrera dig</a></p>
        </form>
    </div>
</main>

</body>
</html>