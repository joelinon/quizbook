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
    <title>QuizBook – Logga in</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #0d0d0f;
            --surface: #16161a;
            --border: #2a2a30;
            --accent: #f0c94a;
            --accent-dim: rgba(240,201,74,0.12);
            --text: #e8e8ec;
            --muted: #7a7a8a;
            --error: #ff6b6b;
            --success: #6bffb0;
            --radius: 12px;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Animated background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 20% 20%, rgba(240,201,74,0.07) 0%, transparent 60%),
                radial-gradient(ellipse 50% 60% at 80% 80%, rgba(100,80,200,0.07) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }

        .card {
            position: relative;
            z-index: 1;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 48px 44px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5);
            animation: fadeUp 0.5s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .logo {
            font-family: 'Syne', sans-serif;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        .logo span { color: var(--accent); }

        .subtitle {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 36px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        input {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 13px 16px;
            font-size: 15px;
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-dim);
        }

        button[type="submit"] {
            width: 100%;
            margin-top: 8px;
            padding: 14px;
            background: var(--accent);
            color: #0d0d0f;
            border: none;
            border-radius: var(--radius);
            font-family: 'Syne', sans-serif;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
            letter-spacing: 0.02em;
        }

        button[type="submit"]:hover { opacity: 0.9; transform: translateY(-1px); }
        button[type="submit"]:active { transform: translateY(0); }

        .footer-link {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: var(--muted);
        }

        .footer-link a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
        }

        .footer-link a:hover { text-decoration: underline; }

        /* Toast */
        .toast {
            padding: 12px 16px;
            border-radius: var(--radius);
            font-size: 14px;
            margin-bottom: 24px;
            animation: fadeUp 0.3s ease both;
        }

        .toast.error  { background: rgba(255,107,107,0.12); border: 1px solid rgba(255,107,107,0.3); color: var(--error); }
        .toast.success{ background: rgba(107,255,176,0.10); border: 1px solid rgba(107,255,176,0.3); color: var(--success); }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">Quiz<span>Book</span></div>
    <p class="subtitle">Logga in för att fortsätta</p>

    <?php if (!empty($message)): ?>
        <div class="toast <?= htmlspecialchars($toastClass) ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="post" novalidate>
        <div class="form-group">
            <label for="username">Användarnamn</label>
            <input type="text" name="username" id="username" autocomplete="username"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="password">Lösenord</label>
            <input type="password" name="password" id="password" autocomplete="current-password">
        </div>
        <button type="submit">Logga in</button>
    </form>

    <p class="footer-link">Inget konto? <a href="register.php">Registrera dig</a></p>
</div>
</body>
</html>