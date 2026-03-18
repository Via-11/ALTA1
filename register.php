<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

include 'db.php';

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $full_name = trim(filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $company = trim(filter_input(INPUT_POST, 'company', FILTER_SANITIZE_STRING)) ?: null;
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING) ?: 'student';
    $contact = trim(filter_input(INPUT_POST, 'contact', FILTER_SANITIZE_STRING));
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms_agreed = isset($_POST['terms_agree']);

    // Validate all required fields
    if (empty($full_name)) {
        $message = "Full name is required.";
        $message_type = "error";
    } elseif (empty($email)) {
        $message = "Email address is required.";
        $message_type = "error";
    } elseif (empty($contact)) {
        $message = "Contact number is required.";
        $message_type = "error";
    } elseif (!$terms_agreed) {
        $message = "You must agree to the Terms of Service.";
        $message_type = "error";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
        $message_type = "error";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters.";
        $message_type = "error";
    } elseif (!in_array($role, ['student', 'admin', 'guest'])) {
        $message = "Invalid role selected.";
        $message_type = "error";
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT email FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $message = "Email already registered.";
                $message_type = "error";
            } else {
                // Insert new user into database
                // Columns: user_id (auto), name, email, password, role, company, contact, created_at, updated_at
                $stmt = $pdo->prepare("
                    INSERT INTO users (name, email, password, role, company, contact) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $full_name,
                    $email,
                    $password,  // Note: In production, use password_hash($password, PASSWORD_BCRYPT)
                    $role,
                    $company,
                    $contact
                ]);
                
                // Redirect to login on successful registration
                header("Location: login.php?registered=1");
                exit();
            }
        } catch (PDOException $e) {
            $message = "Registration error. Please try again later.";
            $message_type = "error";
            error_log("Registration error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | ALTA iHub</title>
    <link rel="stylesheet" href="styles/style1.css">
    <link rel="stylesheet" href="styles/register.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="register-main-container">
        <div class="register-card">
            <div class="register-text-center">
                <h1 class="register-title">Create Account</h1>
                <p class="register-subtitle">Join ALTA iHub Innovation Ecosystem</p>
            </div>

            <?php if ($message): ?>
                <div class="register-alert-message <?php echo $message_type === 'error' ? 'register-alert-error' : 'register-alert-success'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" class="register-form">
                <!-- Full Name -->
                <div class="register-form-group">
                    <label class="register-form-label">Full Name *</label>
                    <input type="text" name="full_name" required class="register-form-input" placeholder="John Doe" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                </div>

                <!-- Email -->
                <div class="register-form-group">
                    <label class="register-form-label">Email Address *</label>
                    <input type="email" name="email" required class="register-form-input" placeholder="you@example.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <!-- Role Selection -->
                <div class="register-form-group">
                    <label class="register-form-label">Select Your Role *</label>
                    <input type="hidden" name="role" id="register-selected-role" value="student">
                    <div class="register-role-selection-grid">
                        <button type="button" onclick="selectRegisterRole('student')" class="register-role-btn register-role-btn-active" id="register-btn-student">Student</button>
                        <button type="button" onclick="selectRegisterRole('admin')" class="register-role-btn" id="register-btn-admin">Admin</button>
                        <button type="button" onclick="selectRegisterRole('guest')" class="register-role-btn" id="register-btn-guest">Guest</button>
                    </div>
                </div>

                <!-- Contact -->
                <div class="register-form-group">
                    <label class="register-form-label">Contact Number *</label>
                    <input type="tel" name="contact" required class="register-form-input" placeholder="+63 900 000 0000" value="<?php echo isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : ''; ?>">
                </div>

                <!-- Company (Optional) -->
                <div class="register-form-group">
                    <label class="register-form-label">Company (Optional)</label>
                    <input type="text" name="company" class="register-form-input" placeholder="Company Name" value="<?php echo isset($_POST['company']) ? htmlspecialchars($_POST['company']) : ''; ?>">
                </div>

                <!-- Password -->
                <div class="register-form-group register-form-password">
                    <label class="register-form-label">Password *</label>
                    <input type="password" name="password" id="register-password" required class="register-form-input" placeholder="At least 8 characters">
                    <button type="button" onclick="toggleRegisterPassword()" class="register-toggle-password-btn">
                        <svg id="register-eye-icon" class="register-eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Confirm Password -->
                <div class="register-form-group register-form-password">
                    <label class="register-form-label">Confirm Password *</label>
                    <input type="password" id="register-confirm-password" name="confirm_password" required class="register-form-input" placeholder="Re-enter password">
                    <button type="button" onclick="toggleRegisterConfirmPassword()" class="register-toggle-password-btn">
                        <svg id="register-eye-icon-confirm" class="register-eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Terms -->
                <div class="register-terms-checkbox-wrapper">
                    <input type="checkbox" name="terms_agree" id="register-terms" required class="register-form-checkbox">
                    <label for="register-terms" class="register-terms-label">
                        I agree to the <a href="#" class="register-link">Terms of Service</a> and <a href="#" class="register-link">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" class="register-btn-submit">
                    CREATE ACCOUNT
                </button>
            </form>

            <div class="register-login-prompt">
                <p>Already have an account? <a href="login.php" class="register-link">Log in</a></p>
            </div>
        </div>
    </main>

    <script>
        function selectRegisterRole(role) {
            document.getElementById('register-selected-role').value = role;
            document.querySelectorAll('.register-role-btn').forEach(btn => {
                btn.classList.remove('register-role-btn-active');
            });
            document.getElementById('register-btn-' + role).classList.add('register-role-btn-active');
        }

        function toggleRegisterPassword() {
            const input = document.getElementById('register-password');
            const icon = document.getElementById('register-eye-icon');
            
            if (input.type === "password") {
                input.type = "text";
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>';
            } else {
                input.type = "password";
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }

        function toggleRegisterConfirmPassword() {
            const input = document.getElementById('register-confirm-password');
            const icon = document.getElementById('register-eye-icon-confirm');
            
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