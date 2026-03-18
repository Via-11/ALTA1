<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirect users based on role or login status
 *
 * @param string $required_role 'admin', 'user', or '' (any logged-in user)
 */
function check_session($required_role = '') {
    if (!isset($_SESSION['user_id'])) {
        // Not logged in → send to login
        header("Location: /login.php");
        exit();
    }

    if ($required_role !== '' && $_SESSION['user_role'] !== $required_role) {
        // Logged in but wrong role → redirect appropriately
        if ($_SESSION['user_role'] === 'admin') {
            header("Location: /admin/admin_index.php");
        } else {
            header("Location: /dashboard.php");
        }
        exit();
    }
}