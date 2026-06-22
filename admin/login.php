<?php
require_once __DIR__ . '/common.php';

if (future_child_support_admin_is_authenticated()) {
    header('Location: index.php');
    exit();
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if (future_child_support_admin_credentials_match($username, $password)) {
        future_child_support_admin_login($username);
        header('Location: index.php');
        exit();
    }

    $errorMessage = 'Invalid login details.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | Future Child Support</title>
    <style>
        :root {
            --bg: #f4efe4;
            --panel: #fffaf2;
            --panel-strong: #ffffff;
            --ink: #24190d;
            --muted: #736250;
            --accent: #c98b1f;
            --accent-dark: #8f5c00;
            --border: rgba(36, 25, 13, 0.12);
            --shadow: 0 24px 60px rgba(56, 37, 10, 0.16);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: "Trebuchet MS", "Gill Sans", sans-serif;
            background:
                radial-gradient(circle at top right, rgba(201, 139, 31, 0.24), transparent 30%),
                linear-gradient(160deg, #f7f0de 0%, #f2e9d6 46%, #fbf6ea 100%);
            color: var(--ink);
        }

        .login-shell {
            width: min(100%, 460px);
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 28px;
            box-shadow: var(--shadow);
            padding: 32px 26px;
        }

        .eyebrow {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(201, 139, 31, 0.12);
            color: var(--accent-dark);
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1 {
            margin: 18px 0 10px;
            font-size: clamp(2rem, 5vw, 2.6rem);
            line-height: 1;
        }

        p {
            margin: 0 0 24px;
            color: var(--muted);
            line-height: 1.6;
        }

        form {
            display: grid;
            gap: 16px;
        }

        label {
            display: grid;
            gap: 8px;
            font-size: 14px;
            font-weight: 700;
        }

        input {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 14px 16px;
            font: inherit;
            background: var(--panel-strong);
            color: var(--ink);
        }

        input:focus {
            outline: 2px solid rgba(201, 139, 31, 0.24);
            border-color: rgba(201, 139, 31, 0.5);
        }

        button {
            border: 0;
            border-radius: 16px;
            padding: 14px 18px;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            color: white;
            background: linear-gradient(135deg, #d69724, #8e5b00);
            box-shadow: 0 16px 30px rgba(143, 92, 0, 0.22);
        }

        .error {
            margin-bottom: 18px;
            border-radius: 16px;
            padding: 12px 14px;
            background: rgba(186, 44, 44, 0.1);
            color: #8c1d1d;
            font-size: 14px;
        }

        .hint {
            margin-top: 18px;
            font-size: 13px;
            color: var(--muted);
        }
    </style>
</head>
<body>
    <main class="login-shell">
        <span class="eyebrow">Admin Area</span>
        <h1>Dashboard Login</h1>
        <p>Sign in to view donations and contact submissions saved from the website.</p>

        <?php if ($errorMessage !== ''): ?>
            <div class="error"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="post">
            <label>
                Username or email
                <input type="text" name="username" autocomplete="username" required>
            </label>
            <label>
                Password
                <input type="password" name="password" autocomplete="current-password" required>
            </label>
            <button type="submit">Open Dashboard</button>
        </form>

        <div class="hint">Default username: <strong><?php echo htmlspecialchars($appConfig['admin_username'], ENT_QUOTES, 'UTF-8'); ?></strong></div>
    </main>
</body>
</html>
