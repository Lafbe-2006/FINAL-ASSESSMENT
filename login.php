<?php
require_once __DIR__ . '/auth.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/dp.php';

$error = '';
$notice = isset($_GET['registered']) ? 'Account created. You can now log in.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } elseif (!ensure_auth_table($conn)) {
        $error = 'Account storage is unavailable. Please try again later.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        try {
            $statement = $conn->prepare(
                'SELECT id, username, password_hash FROM app_users WHERE username = ?'
            );
            $statement->bind_param('s', $username);
            $statement->execute();
            $user = $statement->get_result()->fetch_assoc();
            $statement->close();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $error = 'Invalid username or password.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['username'] = $user['username'];
                unset($_SESSION['csrf_token']);
                header('Location: index.php');
                exit;
            }
        } catch (mysqli_sql_exception $exception) {
            error_log('Login failed: ' . $exception->getMessage());
            $error = 'Unable to log in right now. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #3863c7, #96358d);
        }

        .login-card {
            width: min(100%, 400px);
            overflow: hidden;
            border-radius: 9px;
            background: #fff;
            box-shadow: 0 12px 35px rgb(0 0 0 / 15%);
        }

        h1 {
            margin: 0;
            padding: 18px 20px;
            border-bottom: 1px solid #dedede;
            color: #171717;
            font-family: Georgia, serif;
            font-size: 28px;
            text-align: center;
        }

        .login-form {
            padding: 24px 32px 28px;
        }

        .field {
            display: block;
            width: 100%;
            margin: 8px 0 22px;
            padding: 11px 4px;
            border: 0;
            border-bottom: 1px solid #999;
            border-radius: 0;
            outline: none;
            background: transparent;
            color: #222;
            font: inherit;
        }

        .field:focus {
            border-bottom: 2px solid #3863c7;
        }

        .field::placeholder {
            color: #999;
        }

        .forgot-link {
            display: inline-block;
            margin: 0 0 18px 4px;
            color: #666;
            font-size: 14px;
            text-decoration: none;
        }

        .forgot-link:hover,
        .signup-link:hover {
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            min-height: 44px;
            border: 1px solid #555;
            border-radius: 24px;
            background: #f1f1f1;
            color: #171717;
            cursor: pointer;
            font: inherit;
            font-weight: 700;
        }

        .login-button:hover {
            background: #e7e7e7;
        }

        .signup {
            margin: 24px 0 0;
            color: #666;
            font-family: Georgia, serif;
            font-size: 14px;
            text-align: center;
        }

        .signup-link {
            color: #397c9b;
            text-decoration: none;
        }

        .message {
            margin: 0 0 16px;
            color: #a12626;
            font-size: 14px;
            line-height: 1.4;
        }

        .notice {
            color: #287044;
        }

        @media (max-width: 420px) {
            .login-form {
                padding-right: 24px;
                padding-left: 24px;
            }
        }
    </style>
</head>
<body>
    <main class="login-card">
        <h1>Login</h1>
        <form class="login-form" method="post" action="">
            <?php if ($error !== ''): ?>
                <p class="message" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php elseif ($notice !== ''): ?>
                <p class="message notice" role="status"><?php echo htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
            <label>
                <span hidden>Username</span>
                <input class="field" type="text" name="username" placeholder="Username" autocomplete="username" maxlength="50" required>
            </label>

            <label>
                <span hidden>Password</span>
                <input class="field" type="password" name="password" placeholder="Password" autocomplete="current-password" maxlength="72" required>
            </label>

            <a class="forgot-link" href="#">Forgot Password?</a>
            <button class="login-button" type="submit">Login</button>

            <p class="signup">Not a Member? <a class="signup-link" href="signup.php">Signup</a></p>
        </form>
    </main>
</body>
</html>