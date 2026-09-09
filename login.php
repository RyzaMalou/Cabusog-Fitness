<?php
require_once __DIR__ . '/includes/Function.php';
if (isLoggedIn()) redirect('dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? null)) $error = 'Invalid form token.';
    else {
        $stmt = db()->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([trim($_POST['email'] ?? '')]);
        $user = $stmt->fetch();
        if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            redirect('dashboard.php');
        }
        $error = 'Incorrect email or password.';
    }
}
$pageTitle='Login'; include __DIR__ . '/includes/header.php';
?>
<div class="auth-page"><div class="auth-card compact">
    <span class="eyebrow">WELCOME BACK</span><h1>Log in</h1><p class="muted">Continue your fitness journey.</p>
    <?php if ($error): ?><div class="form-error"><?=e($error)?></div><?php endif; ?>
    <form method="post">
        <input type="hidden" name="csrf" value="<?=e(csrfToken())?>">
        <label>Email<input type="email" name="email" required></label>
        <label>Password<input type="password" name="password" required></label>
        <button class="btn btn-primary full" type="submit">LOGIN</button>
    </form>
    <p class="auth-bottom">New here? <a href="register.php">Create an account</a></p>
</div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
