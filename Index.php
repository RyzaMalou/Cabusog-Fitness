<?php
require_once __DIR__ . '/includes/Function.php';
$pageTitle = 'Home';
include __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="hero-copy">
        <span class="eyebrow">STRONGER EVERY DAY.</span>
        <h1>STRONG TODAY.<br><em>UNSTOPPABLE</em><br>TOMORROW.</h1>
        <p>Science-backed training programs, custom nutrition plan, and expert coaching to help you become the best version of yourself.</p>
        <div class="hero-actions">
            <a class="btn btn-primary" href="register.php">JOIN THE MOVEMENT</a>
            <a class="btn btn-ghost" href="#programs">EXPLORE PROGRAMS</a>
        </div>
        <div class="stats">
            <div><b>12+</b><span>YEARS EXPERIENCE</span></div>
            <div><b>150+</b><span>TRANSFORMATIONS</span></div>
            <div><b>50K+</b><span>HAPPY CLIENTS</span></div>
            <div><b>98%</b><span>SUCCESS RATE</span></div>
        </div>
    </div>
    <div class="hero-art">
        <div class="phone-mock">
            <div class="phone-top"></div><div class="screen-card"><span>YOUR JOURNEY</span><strong>Move More.<br>Live Better.</strong><div class="circle-row"><i>💪</i><i>🔥</i><i>⚡</i></div><div class="progress-line"><span></span></div><small>Today's progress</small></div>
        </div>
    </div>
</section>

<section class="section" id="programs">
    <div class="section-head"><span class="eyebrow">OUR PROGRAMS</span><h2>TRAIN FOR YOUR GOAL</h2><p>Whether you want to build muscle, lose fat, improve performance, or move better, we have a program for you.</p></div>
    <div class="program-grid">
        <a class="program-card" href="programs.php"><span>💪</span><h3>BUILD MUSCLE</h3><p>Increase strength and build lean muscle.</p><b>LEARN MORE →</b></a>
        <a class="program-card" href="programs.php"><span>🔥</span><h3>FAT LOSS</h3><p>Burn fat and reveal a stronger you.</p><b>LEARN MORE →</b></a>
        <a class="program-card" href="programs.php"><span>⚡</span><h3>PERFORMANCE</h3><p>Improve speed, agility and endurance.</p><b>LEARN MORE →</b></a>
        <a class="program-card" href="programs.php"><span>🧘</span><h3>MOBILITY & WELLNESS</h3><p>Improve mobility and reduce stress.</p><b>LEARN MORE →</b></a>
    </div>
</section>
<section class="section split">

    <div class="split-image">
        <img src="images/split.png
        " alt="Fitness Training">
    </div>

    <div class="split-content">
        <p class="eyebrow">WHY CABUSOG FITNESS</p>
        <h2>BUILD A STRONGER YOU</h2>
        <p>
            Our fitness programs help you build strength,
            improve endurance and achieve your fitness goals.
        </p>

    </div>
</section>

<section class="section split" id="about">
    <div><span class="eyebrow">WHY CHOOSE US</span><h2>MORE THAN A GYM.<br>WE'RE YOUR PARTNER.</h2><p>We combine proven training methods with real support and accountability to help you break limits and achieve lasting results.</p></div>
    <div class="feature-list">
        <div><b>EXPERT COACHES</b><span>Certified professionals with real-world experience.</span></div>
        <div><b>CUSTOM PLANS</b><span>Personal workouts and nutrition for you.</span></div>
        <div><b>ACCOUNTABILITY</b><span>We track, support and push you every step.</span></div>
        <div><b>FLEXIBLE ACCESS</b><span>Train anytime, anywhere with our app.</span></div>
    </div>
</section>

<section class="cta"><span class="eyebrow">READY TO TRANSFORM?</span><h2>JOIN CABUSOG FITNESS TODAY!</h2><a class="btn btn-primary" href="register.php">START YOUR JOURNEY</a></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
<link rel="stylesheet" href="assets/css/style.css">