<?php
require_once __DIR__ . '/Function.php';
$pageTitle = $pageTitle ?? 'Cabusog Fitness';
$active = $active ?? '';
$user = isLoggedIn() ? currentUser() : null;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | Cabusog Fitness</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="<?= isLoggedIn() ? 'dashboard.php' : 'index.php' ?>">
        <span class="brand-mark">C</span><span>CABUSOG <small>FITNESS</small></span>
    </a>
    <nav class="main-nav">
        <?php if (isLoggedIn()): ?>
            <a class="<?= $active==='dashboard'?'active':'' ?>" href="dashboard.php">Dashboard</a>
            <a class="<?= $active==='programs'?'active':'' ?>" href="programs.php">Programs</a>
            <a class="<?= $active==='recommendation'?'active':'' ?>" href="recommendation.php">Recommendation</a>
            <a class="<?= $active==='goal'?'active':'' ?>" href="goal.php">My Goal</a>
            <a class="<?= $active==='profile'?'active':'' ?>" href="profile.php">Profile</a>
            <a href="logout.php" class="nav-login">Logout</a>
        <?php else: ?>
            <a href="index.php#about">About Us</a>
            <a href="index.php#programs">Programs</a>
            <a href="index.php#coaches">Coaches</a>
            <a href="index.php#contact">Contact</a>
            <a href="login.php" class="nav-login">Register / Login</a>
        <?php endif; ?>
    </nav>
    <button class="menu-toggle" onclick="document.body.classList.toggle('menu-open')">☰</button>
</header>
<main>
<?php if ($msg = flash('success')): ?><div class="flash success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div class="flash error"><?= e($msg) ?></div><?php endif; ?>
