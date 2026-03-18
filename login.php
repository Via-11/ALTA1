<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header("Location: admin/admin_index.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

include 'db.php';

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($email) || empty($password)) {
        $message = "Email and password are required.";
        $message_type = "error";
    } else {
        try {
            // Query users table from schema
            // Columns: user_id, name, email, password, role, company, contact, created_at, updated_at
            $stmt = $pdo->prepare("
                SELECT user_id, name, email, password, role 
                FROM users 
                WHERE email = ?
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify credentials (Note: In production use password_verify with password_hash)
            if ($user && $password === $user['password']) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin/admin_index.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                $message = "Invalid email or password.";
                $message_type = "error";
            }
        } catch (PDOException $e) {
            $message = "Database error. Please try again later.";
            $message_type = "error";
            error_log("Login error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | ALTA iHub</title>
    <link rel="stylesheet" href="styles/style1.css">
    <link rel="stylesheet" href="styles/login.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="login-main-container">
        <div class="login-card">
            <div class="login-text-center">
                <h1 class="login-title">Sign In to Your Account</h1>
                <p class="login-subtitle">
                    Or <a href="register.php" class="login-link">create a new account</a>
                </p>
            </div>

            <?php if ($message): ?>
                <div class="login-alert-message <?php echo $message_type === 'error' ? 'login-alert-error' : 'login-alert-success'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['registered']) && $_GET['registered'] === '1'): ?>
                <div class="login-alert-message login-alert-success">
                    Registration successful! Please log in with your credentials.
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="login-form" autocomplete="off">
                <!-- Email Address -->
                <div class="login-form-group">
                    <label for="login-email" class="login-form-label">Email Address</label>
                    <input 
                        type="email" 
                        id="login-email" 
                        name="email" 
                        required 
                        class="login-form-input" 
                        placeholder="you@example.com"
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    >
                </div>

                <!-- Password -->
                <div class="login-form-group login-form-password">
                    <label for="login-password" class="login-form-label">Password</label>
                    <input 
                        type="password" 
                        id="login-password" 
                        name="password" 
                        required 
                        class="login-form-input" 
                        placeholder="Enter your password"
                    >
                    <button 
                        type="button" 
                        onclick="toggleLoginPassword()" 
                        class="login-toggle-password-btn"
                        aria-label="Toggle password visibility"
                    >
                        <svg id="login-eye-icon" class="login-eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="login-form-options">
                    <div class="login-checkbox-wrapper">
                        <input 
                            id="login-remember-me" 
                            name="remember-me" 
                            type="checkbox" 
                            class="login-form-checkbox"
                        >
                        <label for="login-remember-me" class="login-checkbox-label">
                            Remember me
                        </label>
                    </div>
                    <a href="#" class="login-forgot-password-link">
                        Forgot password?
                    </a>
                </div>

                <button type="submit" class="login-btn-submit">
                    SIGN IN
                </button>
            </form>

            <div class="login-signup-prompt">
                <p>Don't have an account? <a href="register.php" class="login-link">Sign up here</a></p>
            </div>
        </div>
    </main>

    <script>
        function toggleLoginPassword() {
            const input = document.getElementById('login-password');
            const icon = document.getElementById('login-eye-icon');
            
            if (input.type === "password") {
                input.type = "text";
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>';
            } else {
                input.type = "password";
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }
    </script>
</body>
</html>