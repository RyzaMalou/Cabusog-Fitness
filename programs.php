<?php
require_once __DIR__ . '/includes/Function.php';
$pageTitle='Programs'; $active='programs'; include __DIR__ . '/includes/header.php';
$programs=[
['Build Muscle','💪','Increase strength and build lean muscle.','4 days/week','Strength + hypertrophy'],
['Fat Loss','🔥','Burn fat while maintaining strength and energy.','4–5 days/week','Strength + conditioning'],
['Performance','⚡','Improve speed, agility and endurance.','4 days/week','Athletic performance'],
['Mobility & Wellness','🧘','Improve mobility, recovery and overall movement.','3–4 days/week','Mobility + core'],
];
?>
<div class="page-head"><span class="eyebrow">OUR PROGRAMS</span><h1>TRAIN FOR YOUR GOAL</h1><p>Choose a program or use our recommendation feature to get a starting point based on your goal.</p></div>
<div class="program-grid large"><?php foreach($programs as $p): ?><article class="program-card"><span><?=$p[1]?></span><h2><?=e($p[0])?></h2><p><?=e($p[2])?></p><div class="program-meta"><b><?=e($p[3])?></b><small><?=e($p[4])?></small></div><?php if(isLoggedIn()): ?><a class="btn btn-primary" href="recommendation.php">VIEW RECOMMENDATION</a><?php else: ?><a class="btn btn-ghost" href="register.php">START THIS PROGRAM</a><?php endif; ?></article><?php endforeach; ?></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
