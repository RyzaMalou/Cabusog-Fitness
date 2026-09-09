<?php
declare(strict_types=1);

function validateRegistration(array $data): array {
    $errors = [];

    if (trim($data['first_name'] ?? '') === '') $errors['first_name'] = 'First name is required.';
    if (trim($data['last_name'] ?? '') === '') $errors['last_name'] = 'Last name is required.';
    if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Enter a valid email address.';
    if (strlen($data['password'] ?? '') < 6) $errors['password'] = 'Password must be at least 6 characters.';
    if (($data['password'] ?? '') !== ($data['confirm_password'] ?? '')) $errors['confirm_password'] = 'Passwords do not match.';
    if (!in_array($data['goal'] ?? '', ['Build Muscle','Fat Loss','Performance','Mobility & Wellness'], true)) $errors['goal'] = 'Choose a valid goal.';
    if (!in_array($data['experience'] ?? '', ['Beginner','Intermediate','Advanced'], true)) $errors['experience'] = 'Choose your experience level.';

    return $errors;
}
?>