<?php
require_once __DIR__ . '/auth.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/dp.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!preg_match('/\A[a-zA-Z0-9_.-]{3,50}\z/', $username)) {
            $error = 'Username must be 3-50 characters and use only letters, numbers, dots, dashes, or underscores.';
        } elseif (strlen($password) < 8 || strlen($password) > 72) {
            $error = 'Password must be between 8 and 72 bytes.';
        } elseif (!ensure_auth_table($conn)) {
            $error = 'Account storage is unavailable. Please try again later.';
        } else {
            try {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $statement = $conn->prepare(
                    'INSERT INTO app_users (username, password_hash) VALUES (?, ?)'
                );
                $statement->bind_param('ss', $username, $passwordHash);
                $statement->execute();
                $statement->close();

                header('Location: login.php?registered=1');
                exit;
            } catch (mysqli_sql_exception $exception) {
                if ($exception->getCode() === 1062) {
                    $error = 'That username is already taken.';
                } else {
                    error_log('Signup failed: ' . $exception->getMessage());
                    $error = 'Unable to create an account right now. Please try again later.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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

        .signup-card {
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

        .signup-form {
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

        .submit-button {
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

        .message {
            margin: 0 0 16px;
            color: #a12626;
            font-size: 14px;
            line-height: 1.4;
        }

        .login-link {
            margin: 24px 0 0;
            color: #666;
            font-family: Georgia, serif;
            font-size: 14px;
            text-align: center;
        }

        .login-link a {
            color: #397c9b;
            text-decoration: none;
        }

        @media (max-width: 420px) {
            .signup-form {
                padding-right: 24px;
                padding-left: 24px;
            }
        }
    </style>
</head>
<body>
    <main class="signup-card">
        <h1>Create Account</h1>
        <form class="signup-form" method="post" action="">
            <?php if ($error !== ''): ?>
                <p class="message" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
            <label>
                <span hidden>Username</span>
                <input class="field" type="text" name="username" placeholder="Username" autocomplete="username" minlength="3" maxlength="50" required>
            </label>
            <label>
                <span hidden>Password</span>
                <input class="field" type="password" name="password" placeholder="Password (at least 8 characters)" autocomplete="new-password" minlength="8" maxlength="72" required>
            </label>
            <button class="submit-button" type="submit">Create Account</button>
            <p class="login-link">Already a member? <a href="login.php">Login</a></p>
        </form>
    </main>
</body>
</html>
