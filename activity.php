<?php
require_once __DIR__ . '/includes/Function.php';
requireLogin(); $user=currentUser();
$stmt=db()->prepare("SELECT workout_name,duration_minutes,calories,workout_date FROM workout_logs WHERE user_id=? ORDER BY workout_date DESC, id DESC");
$stmt->execute([$user['id']]); $logs=$stmt->fetchAll();
$pageTitle='Activity'; include __DIR__ . '/includes/header.php';
?>
<div class="page-head"><span class="eyebrow">MY ACTIVITY</span><h1>WORKOUT HISTORY</h1><p>Every workout counts. Keep building consistency.</p></div>
<div class="panel table-panel"><a class="btn btn-primary" href="log_workout.php">+ LOG WORKOUT</a><div class="table-wrap"><table><thead><tr><th>Date</th><th>Workout</th><th>Duration</th><th>Calories</th></tr></thead><tbody>
<?php if(!$logs): ?><tr><td colspan="4" class="muted center">No workouts logged yet.</td></tr><?php else: foreach($logs as $log): ?><tr><td><?=e($log['workout_date'])?></td><td><?=e($log['workout_name'])?></td><td><?=e((string)$log['duration_minutes'])?> min</td><td><?=e((string)$log['calories'])?> kcal</td></tr><?php endforeach; endif;?>
</tbody></table></div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
