<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config/Database.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) $pdo = (new Database())->connect();
    return $pdo;
}

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never {
    header("Location: $url");
    exit;
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) redirect('login.php');
}

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    $stmt = db()->prepare("SELECT id, first_name, last_name, email, goal, experience, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function flash(string $key, ?string $message = null): ?string {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function recommendedProgram(?string $goal): array {
    $goal = strtolower(trim((string)$goal));
    return match ($goal) {
        'build muscle' => ['name' => 'Muscle Builder', 'icon' => '💪', 'description' => 'Strength-focused workouts designed to build lean muscle and improve power.', 'days' => '4 days/week'],
        'fat loss' => ['name' => 'Fat Loss', 'icon' => '🔥', 'description' => 'A balanced mix of strength and conditioning to support healthy fat loss.', 'days' => '4–5 days/week'],
        'performance' => ['name' => 'Performance', 'icon' => '⚡', 'description' => 'Speed, agility, endurance and strength training for better performance.', 'days' => '4 days/week'],
        'mobility & wellness' => ['name' => 'Mobility & Wellness', 'icon' => '🧘', 'description' => 'Mobility, core and recovery sessions to help you move and feel better.', 'days' => '3–4 days/week'],
        default => ['name' => 'Foundation Fitness', 'icon' => '🌱', 'description' => 'A beginner-friendly program that builds consistency, movement quality and strength.', 'days' => '3 days/week'],
    };
}

function csrfToken(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function verifyCsrf(?string $token): bool {
    return is_string($token) && hash_equals($_SESSION['csrf'] ?? '', $token);
}
?>