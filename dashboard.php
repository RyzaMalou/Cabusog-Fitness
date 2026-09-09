<?php
require_once __DIR__ . '/includes/Function.php';
requireLogin();
$user = currentUser();
$program = recommendedProgram($user['goal']);
$stats = ['Workouts' => 0, 'Minutes' => 0, 'Calories' => 0];
$stmt = db()->prepare("SELECT COUNT(*) workouts, COALESCE(SUM(duration_minutes),0) minutes, COALESCE(SUM(calories),0) calories FROM workout_logs WHERE user_id=?");
$stmt->execute([$user['id']]); $row=$stmt->fetch();
$stats['Workouts']=(int)$row['workouts']; $stats['Minutes']=(int)$row['minutes']; $stats['Calories']=(int)$row['calories'];
$pageTitle='Dashboard'; $active='dashboard'; include __DIR__ . '/includes/header.php';
?>
<div class="dashboard">
    <section class="welcome-banner"><div><span class="eyebrow">WELCOME BACK</span><h1>Hey, <?=e($user['first_name'])?> 👋</h1><p>Ready to become stronger today?</p></div><a class="btn btn-primary" href="log_workout.php">LOG WORKOUT</a></section>
    <div class="dashboard-grid">
        <section class="panel recommendation-panel"><div class="panel-title"><span>RECOMMENDED FOR YOU</span><a href="recommendation.php">View details →</a></div><div class="rec-body"><div class="big-icon"><?=$program['icon']?></div><div><h2><?=e($program['name'])?></h2><p><?=e($program['description'])?></p><span class="pill"><?=$program['days']?></span></div></div></section>
        <section class="panel goal-panel"><div class="panel-title"><span>YOUR GOAL</span><a href="goal.php">Edit →</a></div><h2><?=e($user['goal'])?></h2><p>Experience: <strong><?=e($user['experience'])?></strong></p><div class="goal-progress"><span style="width:35%"></span></div><small>35% weekly target completed</small></section>
    </div>
    <section class="panel"><div class="panel-title"><span>YOUR ACTIVITY</span><a href="activity.php">View activity →</a></div><div class="metric-grid"><?php foreach($stats as $label=>$value): ?><div class="metric"><b><?=$value?></b><span><?=e($label)?></span></div><?php endforeach; ?><div class="metric"><b><?=date('d')?></b><span>Day of month</span></div></div></section>
    <section class="dashboard-grid">
        <div class="panel"><div class="panel-title"><span>THIS WEEK</span></div><div class="week"><div class="week-bars"><i style="height:35%"></i><i style="height:55%"></i><i style="height:75%"></i><i style="height:45%"></i><i style="height:85%"></i><i style="height:20%"></i><i style="height:60%"></i></div><div class="week-labels"><span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span><span>S</span></div></div></div>
        <div class="panel quick"><div class="panel-title"><span>QUICK ACTIONS</span></div><a href="programs.php">🏋️ Explore programs</a><a href="recommendation.php">✨ Get recommendation</a><a href="log_workout.php">➕ Log a workout</a><a href="profile.php">👤 Update profile</a></div>
    </section>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
