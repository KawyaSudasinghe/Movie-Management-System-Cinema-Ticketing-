<?php

// Load the database connection and common helper functions.
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Redirect an already authenticated administrator to the dashboard.
if (!empty($_SESSION['admin_id'])) {
    redirect('dashboard.php');
}

$error = '';

// Process the administrator login form.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!verify_csrf($_POST['csrf_token'] ?? '')) {

        $error = 'Invalid request. Please try again.';

    } else {

        $stmt = $conn->prepare(
            'SELECT *
             FROM admins
             WHERE username = :username
             LIMIT 1'
        );

        $stmt->execute(['username' => $username]);

        $admin = $stmt->fetch();

        // Verify the stored password hash instead of storing plain passwords.
        if ($admin && password_verify($password, $admin['password_hash'])) {

            session_regenerate_id(true);

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];

            redirect('dashboard.php');

        } else {

            $error = 'Incorrect username or password.';

        }

    }

}

$pageTitle = 'Admin Login | CineVault';

$rootPath = '../';

require_once '../includes/header.php';

?>

<section class="section">

    <div class="container narrow-container">

        <div class="auth-card">

            <span class="eyebrow">ADMINISTRATION</span>

            <h1>Manager Login</h1>

            <p class="muted">
                Sign in to manage movies and customer bookings.
            </p>

            <?php if ($error): ?>

                <div class="alert error">
                    <?= e($error) ?>
                </div>

            <?php endif; ?>

            <form
                method="POST"
                class="stack-form"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(csrf_token()) ?>"
                >

                <label>

                    <span>Username</span>

                    <input
                        type="text"
                        name="username"
                        required
                        autocomplete="username"
                    >

                </label>

                <label>

                    <span>Password</span>

                    <input
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                    >

                </label>

                <button
                    class="button primary full"
                    type="submit"
                >
                    Login
                </button>

            </form>

            <p class="login-help">
                Demo login: <strong>admin</strong> /
                <strong>admin123</strong>
            </p>

        </div>

    </div>

</section>

<?php require_once '../includes/footer.php'; ?>
