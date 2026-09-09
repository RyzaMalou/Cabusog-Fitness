<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/Function.php';
requireLogin();

$user = currentUser();
$error = '';
$success = flash('booking_success');

$programs = [
    'Build Muscle' => ['icon' => '💪', 'description' => 'Strength and hypertrophy training.'],
    'Fat Loss' => ['icon' => '🔥', 'description' => 'Strength and conditioning for fat loss.'],
    'Performance' => ['icon' => '⚡', 'description' => 'Speed, agility, endurance and strength.'],
    'Mobility & Wellness' => ['icon' => '🧘', 'description' => 'Mobility, recovery and core training.'],
];

$coaches = [
    1 => ['name'=>'Coach Alex', 'specialty'=>'Strength & Muscle Building', 'program'=>'Build Muscle', 'experience'=>'8 years', 'icon'=>'🏋️'],
    2 => ['name'=>'Coach Mia', 'specialty'=>'Fat Loss & Conditioning', 'program'=>'Fat Loss', 'experience'=>'6 years', 'icon'=>'🔥'],
    3 => ['name'=>'Coach Jordan', 'specialty'=>'Performance & Athletic Training', 'program'=>'Performance', 'experience'=>'9 years', 'icon'=>'⚡'],
    4 => ['name'=>'Coach Sam', 'specialty'=>'Mobility & Wellness', 'program'=>'Mobility & Wellness', 'experience'=>'7 years', 'icon'=>'🧘'],
];

$selectedProgram = trim($_GET['program'] ?? $_POST['program'] ?? $user['goal'] ?? '');
if (!array_key_exists($selectedProgram, $programs)) {
    $selectedProgram = $user['goal'];
}
$selectedCoach = (int)($_POST['coach_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? null)) {
        $error = 'Invalid form token. Please refresh and try again.';
    } else {
        $selectedProgram = trim($_POST['program'] ?? '');
        $selectedCoach = (int)($_POST['coach_id'] ?? 0);
        $date = trim($_POST['booking_date'] ?? '');
        $time = trim($_POST['booking_time'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if (!array_key_exists($selectedProgram, $programs)) {
            $error = 'Please choose a valid program.';
        } elseif (!isset($coaches[$selectedCoach]) || $coaches[$selectedCoach]['program'] !== $selectedProgram) {
            $error = 'Please choose a coach for the selected program.';
        } elseif (!$date || !$time) {
            $error = 'Please choose a date and time.';
        } elseif ($date < date('Y-m-d')) {
            $error = 'Please choose today or a future date.';
        } else {
            try {
                // Create the booking tables automatically for XAMPP/MySQL projects.
                db()->exec("CREATE TABLE IF NOT EXISTS coaches (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(120) NOT NULL,
                    specialty VARCHAR(160) NOT NULL,
                    program VARCHAR(100) NOT NULL,
                    experience VARCHAR(50) NOT NULL,
                    icon VARCHAR(20) NOT NULL DEFAULT '🏋️',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

                db()->exec("CREATE TABLE IF NOT EXISTS bookings (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id INT UNSIGNED NOT NULL,
                    coach_id INT UNSIGNED NOT NULL,
                    program VARCHAR(100) NOT NULL,
                    booking_date DATE NOT NULL,
                    booking_time TIME NOT NULL,
                    notes VARCHAR(500) DEFAULT NULL,
                    status ENUM('Pending','Confirmed','Cancelled') NOT NULL DEFAULT 'Pending',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_user (user_id),
                    INDEX idx_coach_schedule (coach_id, booking_date, booking_time)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

                // Seed the coach records if they are not already present.
                $coachCount = (int)db()->query("SELECT COUNT(*) FROM coaches")->fetchColumn();
                if ($coachCount === 0) {
                    $seed = db()->prepare("INSERT INTO coaches (name,specialty,program,experience,icon) VALUES (?,?,?,?,?)");
                    foreach ($coaches as $c) {
                        $seed->execute([$c['name'], $c['specialty'], $c['program'], $c['experience'], $c['icon']]);
                    }
                }

                // Do not allow two bookings for the same coach at the same time.
                $check = db()->prepare("SELECT id FROM bookings
                    WHERE coach_id=? AND booking_date=? AND booking_time=? AND status <> 'Cancelled' LIMIT 1");
                $check->execute([$selectedCoach, $date, $time]);

                if ($check->fetch()) {
                    $error = 'That coach is already booked at that time. Please choose another time.';
                } else {
                    $insert = db()->prepare("INSERT INTO bookings
                        (user_id,coach_id,program,booking_date,booking_time,notes,status)
                        VALUES (?,?,?,?,?,?, 'Pending')");
                    $insert->execute([
                        $user['id'], $selectedCoach, $selectedProgram,
                        $date, $time, $notes !== '' ? $notes : null
                    ]);
                    flash('booking_success', 'Booking request submitted successfully!');
                    redirect('my_bookings.php');
                }
            } catch (PDOException $ex) {
                $error = 'Booking setup failed. Make sure MySQL is running and the cabusog_fitness database exists.';
            }
        }
    }
}

$pageTitle = 'Book a Coach';
$active = 'booking';
include __DIR__ . '/includes/header.php';
?>
<div class="page-head">
    <span class="eyebrow">PERSONAL COACHING</span>
    <h1>BOOK YOUR COACH</h1>
    <p>Choose your fitness program, select a coach, and reserve a training session that fits your schedule.</p>
</div>

<div class="booking-page">
    <?php if ($error): ?><div class="form-error"><?=e($error)?></div><?php endif; ?>

    <section class="booking-layout">
        <div class="panel booking-form-panel">
            <div class="panel-title"><span>1. CHOOSE YOUR PROGRAM</span></div>

            <div class="program-choice-grid">
                <?php foreach ($programs as $name => $p): ?>
                    <a class="choice-card <?= $selectedProgram === $name ? 'selected' : '' ?>"
                       href="booking.php?program=<?=urlencode($name)?>">
                        <span class="choice-icon"><?=$p['icon']?></span>
                        <strong><?=e($name)?></strong>
                        <small><?=e($p['description'])?></small>
                    </a>
                <?php endforeach; ?>
            </div>

            <form method="post" id="bookingForm">
                <input type="hidden" name="csrf" value="<?=e(csrfToken())?>">
                <input type="hidden" name="program" id="programInput" value="<?=e($selectedProgram)?>">

                <div class="panel-title booking-step-title"><span>2. SELECT YOUR COACH</span></div>
                <div class="coach-grid">
                    <?php foreach ($coaches as $id => $coach): ?>
                        <?php if ($coach['program'] !== $selectedProgram) continue; ?>
                        <label class="coach-card">
                            <input type="radio" name="coach_id" value="<?=$id?>" <?=$selectedCoach === $id ? 'checked' : ''?> required>
                            <span class="coach-avatar"><?=$coach['icon']?></span>
                            <span class="coach-info">
                                <strong><?=e($coach['name'])?></strong>
                                <small><?=e($coach['specialty'])?></small>
                                <small><?=e($coach['experience'])?> coaching experience</small>
                            </span>
                            <span class="coach-check">✓</span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="panel-title booking-step-title"><span>3. CHOOSE DATE & TIME</span></div>
                <div class="form-grid">
                    <label>Training date
                        <input type="date" name="booking_date" min="<?=date('Y-m-d')?>" value="<?=e($_POST['booking_date'] ?? '')?>" required>
                    </label>
                    <label>Training time
                        <select name="booking_time" required>
                            <option value="">Choose a time</option>
                            <?php foreach (['08:00','09:00','10:00','13:00','14:00','15:00','16:00','17:00','18:00'] as $slot): ?>
                                <option value="<?=$slot?>" <?=($_POST['booking_time'] ?? '') === $slot ? 'selected' : ''?>><?=date('g:i A', strtotime($slot))?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="full">Notes for your coach
                        <input type="text" name="notes" maxlength="500" placeholder="Example: I want to focus on beginner strength exercises." value="<?=e($_POST['notes'] ?? '')?>">
                    </label>
                </div>

                <button class="btn btn-primary full" type="submit">CONFIRM BOOKING REQUEST</button>
            </form>
        </div>

        <aside class="panel booking-summary">
            <div class="panel-title"><span>BOOKING SUMMARY</span></div>
            <div class="summary-row"><span>Program</span><strong id="summaryProgram"><?=e($selectedProgram)?></strong></div>
            <div class="summary-row"><span>Coach</span><strong id="summaryCoach">Choose a coach</strong></div>
            <div class="summary-row"><span>Date</span><strong id="summaryDate">Not selected</strong></div>
            <div class="summary-row"><span>Time</span><strong id="summaryTime">Not selected</strong></div>
            <div class="summary-note">Your request will be saved to your account. A coach can confirm or cancel the session later.</div>
            <a class="btn btn-ghost full" href="my_bookings.php">VIEW MY BOOKINGS</a>
        </aside>
    </section>
</div>

<script>
(function () {
    const programInput = document.getElementById('programInput');
    const summaryProgram = document.getElementById('summaryProgram');
    const summaryCoach = document.getElementById('summaryCoach');
    const summaryDate = document.getElementById('summaryDate');
    const summaryTime = document.getElementById('summaryTime');
    const dateInput = document.querySelector('input[name="booking_date"]');
    const timeInput = document.querySelector('select[name="booking_time"]');

    function updateSummary() {
        const coach = document.querySelector('input[name="coach_id"]:checked');
        summaryProgram.textContent = programInput.value || 'Not selected';
        summaryCoach.textContent = coach ? coach.closest('.coach-card').querySelector('.coach-info strong').textContent : 'Choose a coach';
        summaryDate.textContent = dateInput && dateInput.value ? dateInput.value : 'Not selected';
        summaryTime.textContent = timeInput && timeInput.value ? new Date('1970-01-01T' + timeInput.value).toLocaleTimeString([], {hour:'numeric', minute:'2-digit'}) : 'Not selected';
    }

    document.querySelectorAll('input[name="coach_id"]').forEach(el => el.addEventListener('change', updateSummary));
    if (dateInput) dateInput.addEventListener('change', updateSummary);
    if (timeInput) timeInput.addEventListener('change', updateSummary);
    updateSummary();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
