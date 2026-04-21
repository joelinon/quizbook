<?php
include 'config.php';

$message = "";
$toastClass ="";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $message = "All fields are required.";
        $toastClass = "error";
    } else if (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $toastClass = "error";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $toastClass = "error";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $checkEmailStmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
        $checkEmailStmt->bind_param("s", $email);
        $checkEmailStmt->execute();
        $checkEmailStmt->store_result();

        if ($checkEmailStmt->num_rows > 0) {
            $message = "Email already exists. Please use a different email.";
            $toastClass = "error";
        } else {
            $stmt = $conn->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $message = "Registration successful! You can now log in.";
                $toastClass = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $toastClass = "error";
            }

            $stmt->close();
        }
        $checkEmailStmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GroggBook</title>
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <form method="post" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>