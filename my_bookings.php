<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/Function.php';
requireLogin();

$user = currentUser();
$success = flash('booking_success');
$error = '';

try {
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

    $bookings = db()->prepare("SELECT b.*, c.name AS coach_name, c.specialty, c.icon
        FROM bookings b
        LEFT JOIN coaches c ON c.id=b.coach_id
        WHERE b.user_id=?
        ORDER BY b.booking_date DESC, b.booking_time DESC, b.id DESC");
    $bookings->execute([$user['id']]);
    $bookings = $bookings->fetchAll();
} catch (PDOException $ex) {
    $bookings = [];
    $error = 'Unable to load bookings. Make sure MySQL is running and the database exists.';
}

$pageTitle = 'My Bookings';
$active = 'booking';
include __DIR__ . '/includes/header.php';
?>
<div class="page-head">
    <span class="eyebrow">YOUR SESSIONS</span>
    <h1>MY BOOKINGS</h1>
    <p>Track your coach requests and manage your upcoming training sessions.</p>
</div>

<div class="table-panel">
    <?php if ($success): ?><div class="flash success booking-inline"><?=e($success)?></div><?php endif; ?>
    <?php if ($error): ?><div class="form-error"><?=e($error)?></div><?php endif; ?>

    <a class="btn btn-primary" href="booking.php">+ BOOK A COACH</a>

    <?php if (!$bookings): ?>
        <div class="panel center empty-bookings">
            <div class="success-icon">📅</div>
            <h2>No bookings yet</h2>
            <p class="muted">Choose a program and coach to schedule your first personal training session.</p>
            <a class="btn btn-primary" href="booking.php">BOOK YOUR FIRST SESSION</a>
        </div>
    <?php else: ?>
        <div class="booking-cards">
            <?php foreach ($bookings as $booking): ?>
                <article class="booking-card">
                    <div class="booking-coach-icon"><?=e($booking['icon'] ?? '🏋️')?></div>
                    <div class="booking-card-main">
                        <span class="eyebrow"><?=e($booking['program'])?></span>
                        <h2><?=e($booking['coach_name'] ?? 'Coach')?></h2>
                        <p><?=e($booking['specialty'] ?? '')?></p>
                        <div class="booking-meta">
                            <span>📅 <?=e(date('M d, Y', strtotime($booking['booking_date'])))?></span>
                            <span>🕒 <?=e(date('g:i A', strtotime($booking['booking_time'])))?></span>
                        </div>
                        <?php if (!empty($booking['notes'])): ?>
                            <small class="booking-notes">Note: <?=e($booking['notes'])?></small>
                        <?php endif; ?>
                    </div>
                    <span class="status status-<?=strtolower($booking['status'])?>"><?=e($booking['status'])?></span>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
