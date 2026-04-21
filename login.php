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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
<main>
    <form action="login.php" method="post">
        <h1>Login</h1>
        <div>
            <label for="username">Username:</label>
            <input type="text" name="username" id="username">
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password">
        </div>
        <section>
            <button type="submit">Login</button>
            <a href="register.php">Register</a>
        </section>
    </form>
</main>
</body>
</html>