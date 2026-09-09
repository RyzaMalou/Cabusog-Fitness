<?php
require_once __DIR__ . '/includes/Function.php';
requireLogin(); $user=currentUser(); $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verifyCsrf($_POST['csrf']??null)) $error='Invalid form token.';
    else {
        $name=trim($_POST['name']??''); $duration=(int)($_POST['duration']??0); $calories=(int)($_POST['calories']??0);
        if($name===''||$duration<1) $error='Workout name and duration are required.';
        else { $stmt=db()->prepare("INSERT INTO workout_logs(user_id,workout_name,duration_minutes,calories,workout_date) VALUES(?,?,?,?,CURDATE())"); $stmt->execute([$user['id'],$name,$duration,max(0,$calories)]); flash('success','Workout logged! Your dashboard has been updated.'); redirect('dashboard.php'); }
    }
}
$pageTitle='Log Workout'; include __DIR__ . '/includes/header.php';
?>
<div class="auth-page"><div class="auth-card"><span class="eyebrow">TRACK YOUR PROGRESS</span><h1>Log a workout</h1>
<?php if($error): ?><div class="form-error"><?=e($error)?></div><?php endif;?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrfToken())?>">
<label>Workout name<input name="name" placeholder="e.g. Lower Body Strength" required></label>
<label>Duration (minutes)<input type="number" name="duration" min="1" max="600" required></label>
<label>Calories (optional)<input type="number" name="calories" min="0" max="10000" value="0"></label>
<button class="btn btn-primary full">SAVE WORKOUT</button></form></div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
