<?php
require_once __DIR__ . '/includes/Function.php';
require_once __DIR__ . '/includes/Validation.php';
if (isLoggedIn()) redirect('dashboard.php');

$errors = [];
$data = ['first_name'=>'','last_name'=>'','email'=>'','goal'=>'','experience'=>''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    if (!verifyCsrf($_POST['csrf'] ?? null)) $errors['general'] = 'Invalid form token. Please try again.';
    else {
        $errors = validateRegistration($_POST);
        if (!$errors) {
            $stmt = db()->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([trim($_POST['email'])]);
            if ($stmt->fetch()) $errors['email'] = 'This email is already registered.';
            else {
                $stmt = db()->prepare("INSERT INTO users (first_name,last_name,email,password,goal,experience) VALUES (?,?,?,?,?,?)");
                $stmt->execute([
                    trim($_POST['first_name']), trim($_POST['last_name']), trim($_POST['email']),
                    password_hash($_POST['password'], PASSWORD_DEFAULT), $_POST['goal'], $_POST['experience']
                ]);
                flash('success','Account created successfully. You can now log in.');
                redirect('login.php');
            }
        }
    }
}
$pageTitle='Register';
include __DIR__ . '/includes/header.php';
?>
<div class="auth-page">
<div class="auth-card">
    <span class="eyebrow">START YOUR JOURNEY</span><h1>Create your account</h1><p class="muted">Tell us your goal so Cabusog Fitness can recommend a starting program.</p>
    <?php if (!empty($errors['general'])): ?><div class="form-error"><?=e($errors['general'])?></div><?php endif; ?>
    <form method="post" class="form-grid">
        <input type="hidden" name="csrf" value="<?=e(csrfToken())?>">
        <label>First name<input name="first_name" value="<?=e($data['first_name'])?>" required></label>
        <label>Last name<input name="last_name" value="<?=e($data['last_name'])?>" required></label>
        <label class="full">Email<input type="email" name="email" value="<?=e($data['email'])?>" required></label>
        <label>Password<input type="password" name="password" required></label>
        <label>Confirm password<input type="password" name="confirm_password" required></label>
        <label>What's your goal?
            <select name="goal" required><option value="">Choose a goal</option><?php foreach(['Build Muscle','Fat Loss','Performance','Mobility & Wellness'] as $g): ?><option <?=($data['goal']??'')===$g?'selected':''?>><?=e($g)?></option><?php endforeach; ?></select>
        </label>
        <label>Experience level
            <select name="experience" required><option value="">Choose level</option><?php foreach(['Beginner','Intermediate','Advanced'] as $x): ?><option <?=($data['experience']??'')===$x?'selected':''?>><?=e($x)?></option><?php endforeach; ?></select>
        </label>
        <button class="btn btn-primary full" type="submit">CREATE ACCOUNT</button>
    </form>
    <p class="auth-bottom">Already have an account? <a href="login.php">Log in</a></p>
</div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
