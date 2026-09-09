<?php
require_once __DIR__ . '/includes/Function.php';
requireLogin();
$user=currentUser(); $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!verifyCsrf($_POST['csrf']??null)) $error='Invalid form token.';
    else {
        $first=trim($_POST['first_name']??''); $last=trim($_POST['last_name']??'');
        if($first===''||$last==='') $error='First and last name are required.';
        else { $stmt=db()->prepare("UPDATE users SET first_name=?, last_name=? WHERE id=?"); $stmt->execute([$first,$last,$user['id']]); flash('success','Profile updated.'); redirect('profile.php'); }
    }
}
$pageTitle='Profile'; $active='profile'; include __DIR__ . '/includes/header.php';
?>
<div class="auth-page"><div class="auth-card"><span class="eyebrow">YOUR PROFILE</span><h1>Account settings</h1>
<?php if($error): ?><div class="form-error"><?=e($error)?></div><?php endif;?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrfToken())?>">
<label>First name<input name="first_name" value="<?=e($user['first_name'])?>" required></label>
<label>Last name<input name="last_name" value="<?=e($user['last_name'])?>" required></label>
<label>Email<input value="<?=e($user['email'])?>" disabled></label>
<label>Goal<input value="<?=e($user['goal'])?>" disabled></label>
<button class="btn btn-primary full">SAVE CHANGES</button></form></div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
