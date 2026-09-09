<?php
require_once __DIR__ . '/includes/Function.php';
$message = flash('success') ?? 'Your action was completed successfully.';
$pageTitle='Success'; include __DIR__ . '/includes/header.php';
?>
<div class="auth-page"><div class="auth-card compact success-page"><div class="success-icon">✓</div><span class="eyebrow">SUCCESS</span><h1>All done!</h1><p><?=e($message)?></p><a class="btn btn-primary full" href="<?=isLoggedIn()?'dashboard.php':'index.php'?>">CONTINUE</a></div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
