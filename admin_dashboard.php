<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/Function.php';
requireAdmin();

$error = '';
$success = flash('admin_success');

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

    // Seed coaches when this is the first time the admin dashboard is opened.
    if ((int)db()->query("SELECT COUNT(*) FROM coaches")->fetchColumn() === 0) {
        $seed = db()->prepare("INSERT INTO coaches (name,specialty,program,experience,icon) VALUES (?,?,?,?,?)");
        foreach ([
            ['Coach Alex','Strength & Muscle Building','Build Muscle','8 years','🏋️'],
            ['Coach Mia','Fat Loss & Conditioning','Fat Loss','6 years','🔥'],
            ['Coach Jordan','Performance & Athletic Training','Performance','9 years','⚡'],
            ['Coach Sam','Mobility & Wellness','Mobility & Wellness','7 years','🧘'],
        ] as $c) $seed->execute($c);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verifyCsrf($_POST['csrf'] ?? null)) {
            $error = 'Invalid form token. Please refresh the page.';
        } else {
            $action = $_POST['action'] ?? '';
            $bookingId = (int)($_POST['booking_id'] ?? 0);
            $date = trim($_POST['booking_date'] ?? '');
            $time = trim($_POST['booking_time'] ?? '');

            if ($bookingId < 1) {
                $error = 'Invalid booking.';
            } elseif ($action === 'confirm' || $action === 'cancel') {
                $status = $action === 'confirm' ? 'Confirmed' : 'Cancelled';
                $stmt = db()->prepare("UPDATE bookings SET status=? WHERE id=?");
                $stmt->execute([$status, $bookingId]);
                flash('admin_success', "Booking #{$bookingId} marked {$status}.");
                redirect('admin_dashboard.php');
            } elseif ($action === 'reschedule') {
                if (!$date || !$time || $date < date('Y-m-d')) {
                    $error = 'Choose a valid future date and time.';
                } else {
                    $find = db()->prepare("SELECT coach_id FROM bookings WHERE id=?");
                    $find->execute([$bookingId]);
                    $booking = $find->fetch();
                    if (!$booking) {
                        $error = 'Booking not found.';
                    } else {
                        $check = db()->prepare("SELECT id FROM bookings WHERE coach_id=? AND booking_date=? AND booking_time=? AND id<>? AND status<>'Cancelled' LIMIT 1");
                        $check->execute([(int)$booking['coach_id'], $date, $time, $bookingId]);
                        if ($check->fetch()) {
                            $error = 'That coach is already booked at the new date/time.';
                        } else {
                            $stmt = db()->prepare("UPDATE bookings SET booking_date=?, booking_time=?, status='Pending' WHERE id=?");
                            $stmt->execute([$date, $time, $bookingId]);
                            flash('admin_success', "Booking #{$bookingId} rescheduled and returned to Pending.");
                            redirect('admin_dashboard.php');
                        }
                    }
                }
            }
        }
    }

    $counts = ['all'=>0,'pending'=>0,'confirmed'=>0,'cancelled'=>0];
    $counts['all'] = (int)db()->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
    $counts['pending'] = (int)db()->query("SELECT COUNT(*) FROM bookings WHERE status='Pending'")->fetchColumn();
    $counts['confirmed'] = (int)db()->query("SELECT COUNT(*) FROM bookings WHERE status='Confirmed'")->fetchColumn();
    $counts['cancelled'] = (int)db()->query("SELECT COUNT(*) FROM bookings WHERE status='Cancelled'")->fetchColumn();

    $filter = $_GET['status'] ?? 'All';
    $allowed = ['All','Pending','Confirmed','Cancelled'];
    if (!in_array($filter, $allowed, true)) $filter = 'All';

    $sql = "SELECT b.*, u.first_name, u.last_name, u.email, c.name AS coach_name, c.specialty, c.icon
            FROM bookings b
            LEFT JOIN users u ON u.id=b.user_id
            LEFT JOIN coaches c ON c.id=b.coach_id";
    $params = [];
    if ($filter !== 'All') { $sql .= " WHERE b.status=?"; $params[] = $filter; }
    $sql .= " ORDER BY b.booking_date ASC, b.booking_time ASC, b.id DESC";
    $stmt = db()->prepare($sql); $stmt->execute($params); $bookings = $stmt->fetchAll();
} catch (PDOException $ex) {
    $bookings = [];
    $counts = ['all'=>0,'pending'=>0,'confirmed'=>0,'cancelled'=>0];
    $error = 'Could not load the admin dashboard. Check that MySQL is running and your database is available.';
}

$pageTitle='Admin / Coach Dashboard'; $active='admin'; include __DIR__ . '/includes/header.php';
?>
<div class="admin-page">
    <section class="admin-hero">
        <div><span class="eyebrow">MANAGEMENT CENTER</span><h1>ADMIN / COACH DASHBOARD</h1><p>Manage member bookings, confirm sessions, cancel requests, or reschedule a coach appointment.</p></div>
        <a class="btn btn-ghost" href="dashboard.php">VIEW MEMBER DASHBOARD</a>
    </section>

    <?php if ($error): ?><div class="form-error admin-message"><?=e($error)?></div><?php endif; ?>
    <?php if ($success): ?><div class="flash success admin-inline"><?=e($success)?></div><?php endif; ?>

    <section class="admin-stats">
        <a href="admin_dashboard.php" class="admin-stat"><b><?=$counts['all']?></b><span>ALL BOOKINGS</span></a>
        <a href="admin_dashboard.php?status=Pending" class="admin-stat pending"><b><?=$counts['pending']?></b><span>PENDING</span></a>
        <a href="admin_dashboard.php?status=Confirmed" class="admin-stat confirmed"><b><?=$counts['confirmed']?></b><span>CONFIRMED</span></a>
        <a href="admin_dashboard.php?status=Cancelled" class="admin-stat cancelled"><b><?=$counts['cancelled']?></b><span>CANCELLED</span></a>
    </section>

    <section class="panel admin-panel">
        <div class="panel-title"><span>BOOKING MANAGEMENT</span><div class="admin-filters"><?php foreach ($allowed as $x): ?><a class="<?= $filter===$x?'selected':'' ?>" href="admin_dashboard.php<?= $x==='All'?'':'?status='.urlencode($x) ?>"><?=e($x)?></a><?php endforeach; ?></div></div>

        <?php if (!$bookings): ?>
            <div class="admin-empty"><div>📅</div><h2>No bookings found</h2><p>New member booking requests will appear here.</p></div>
        <?php else: ?>
            <div class="admin-bookings">
            <?php foreach ($bookings as $b): ?>
                <article class="admin-booking">
                    <div class="admin-booking-top">
                        <div class="booking-coach-icon"><?=e($b['icon'] ?? '🏋️')?></div>
                        <div class="admin-booking-title"><span class="eyebrow">BOOKING #<?=e((string)$b['id'])?></span><h2><?=e(($b['first_name'] ?? '').' '.($b['last_name'] ?? ''))?></h2><p><?=e($b['email'] ?? '')?></p></div>
                        <span class="status status-<?=strtolower($b['status'])?>"><?=e($b['status'])?></span>
                    </div>
                    <div class="admin-booking-details">
                        <div><small>PROGRAM</small><strong><?=e($b['program'])?></strong></div>
                        <div><small>COACH</small><strong><?=e($b['coach_name'] ?? 'Unassigned')?></strong></div>
                        <div><small>DATE</small><strong><?=e(date('M d, Y', strtotime($b['booking_date'])))?></strong></div>
                        <div><small>TIME</small><strong><?=e(date('g:i A', strtotime($b['booking_time'])))?></strong></div>
                    </div>
                    <?php if (!empty($b['notes'])): ?><div class="admin-notes"><b>Member note:</b> <?=e($b['notes'])?></div><?php endif; ?>
                    <div class="admin-actions">
                        <form method="post"><input type="hidden" name="csrf" value="<?=e(csrfToken())?>"><input type="hidden" name="booking_id" value="<?=e((string)$b['id'])?>"><input type="hidden" name="action" value="confirm"><button class="btn btn-confirm" type="submit" <?= $b['status']==='Cancelled'?'disabled':'' ?>>✓ CONFIRM</button></form>
                        <form method="post"><input type="hidden" name="csrf" value="<?=e(csrfToken())?>"><input type="hidden" name="booking_id" value="<?=e((string)$b['id'])?>"><input type="hidden" name="action" value="cancel"><button class="btn btn-cancel" type="submit" <?= $b['status']==='Cancelled'?'disabled':'' ?>>✕ CANCEL</button></form>
                        <form method="post" class="reschedule-form"><input type="hidden" name="csrf" value="<?=e(csrfToken())?>"><input type="hidden" name="booking_id" value="<?=e((string)$b['id'])?>"><input type="hidden" name="action" value="reschedule"><input type="date" name="booking_date" min="<?=date('Y-m-d')?>" value="<?=e($b['booking_date'])?>" required><select name="booking_time" required><?php foreach(['08:00','09:00','10:00','13:00','14:00','15:00','16:00','17:00','18:00'] as $slot): ?><option value="<?=$slot?>" <?=$b['booking_time']===($slot.':00')||substr($b['booking_time'],0,5)===$slot?'selected':''?>><?=date('g:i A',strtotime($slot))?></option><?php endforeach; ?></select><button class="btn btn-reschedule" type="submit">↻ RESCHEDULE</button></form>
                    </div>
                </article>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
