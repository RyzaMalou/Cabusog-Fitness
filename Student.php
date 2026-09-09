<?php
// Student.php is kept as a simple reusable data-access class for school/project requirements.
// It can be used anywhere the application needs student/member records.
declare(strict_types=1);
require_once __DIR__ . '/Function.php';

class Student {
    public static function all(): array {
        return db()->query("SELECT id, first_name, last_name, email, goal, experience, created_at FROM users ORDER BY id DESC")->fetchAll();
    }
    public static function find(int $id): ?array {
        $stmt=db()->prepare("SELECT id, first_name, last_name, email, goal, experience, created_at FROM users WHERE id=?");
        $stmt->execute([$id]); return $stmt->fetch() ?: null;
    }
}
?>