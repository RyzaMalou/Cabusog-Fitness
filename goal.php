<?php
require_once __DIR__ . '/includes/Function.php';
requireLogin();
$user=currentUser(); $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!verifyCsrf($_POST['csrf']??null)) $error='Invalid form token.';
    else {
        $goal=$_POST['goal']??''; $experience=$_POST['experience']??'';
        if (!in_array($goal,['Build Muscle','Fat Loss','Performance','Mobility & Wellness'],true) || !in_array($experience,['Beginner','Intermediate','Advanced'],true)) $error='Please choose valid options.';
        else { $stmt=db()->prepare("UPDATE users SET goal=?, experience=? WHERE id=?"); $stmt->execute([$goal,$experience,$user['id']]); flash('success','Your goal has been updated.'); redirect('recommendation.php'); }
    }
}
$pageTitle='My Goal'; $active='goal'; include __DIR__ . '/includes/header.php';
?>
<div class="auth-page"><div class="auth-card"><span class="eyebrow">PERSONALIZE YOUR PLAN</span><h1>What's your goal?</h1><p class="muted">Your answer changes the recommended program on your dashboard.</p>
<?php if($error): ?><div class="form-error"><?=e($error)?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrfToken())?>">
<label>Primary goal<select name="goal"><?php foreach(['Build Muscle','Fat Loss','Performance','Mobility & Wellness'] as $g): ?><option <?=$user['goal']===$g?'selected':''?>><?=e($g)?></option><?php endforeach;?></select></label>
<label>Experience<select name="experience"><?php foreach(['Beginner','Intermediate','Advanced'] as $x): ?><option <?=$user['experience']===$x?'selected':''?>><?=e($x)?></option><?php endforeach;?></select></label>
<button class="btn btn-primary full">SAVE GOAL</button></form></div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
