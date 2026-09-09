<?php
require_once __DIR__ . '/includes/Function.php';
requireLogin();
$user=currentUser(); $program=recommendedProgram($user['goal']);
$pageTitle='Recommendation'; $active='recommendation'; include __DIR__ . '/includes/header.php';
?>
<div class="recommend-page">
<div class="page-head"><span class="eyebrow">SMART RECOMMENDATION</span><h1>YOUR PROGRAM MATCH</h1><p>Based on your current goal: <strong><?=e($user['goal'])?></strong></p></div>
<div class="recommend-card"><div class="recommend-icon"><?=$program['icon']?></div><div><span class="pill">RECOMMENDED</span><h2><?=e($program['name'])?></h2><p><?=e($program['description'])?></p><div class="recommend-details"><div><b>Schedule</b><span><?=e($program['days'])?></span></div><div><b>Level</b><span><?=e($user['experience'])?></span></div><div><b>Focus</b><span><?=e($user['goal'])?></span></div></div><a class="btn btn-primary" href="log_workout.php">START / LOG WORKOUT</a></div></div>
<div class="tips"><h2>Getting started</h2><ol><li>Start with manageable weights and controlled movement.</li><li>Keep your weekly schedule consistent.</li><li>Record your workouts so your dashboard can track progress.</li><li>Adjust your plan as your goals and experience change.</li></ol></div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
