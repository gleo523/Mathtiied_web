<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';

function teacher_profile_response(array $user, array $profile): array
{
    $profile['first_name'] = $user['first_name'] ?? '';
    $profile['last_name'] = $user['last_name'] ?? '';
    $profile['email'] = $user['email'] ?? '';
    $profile['photo_url'] = !empty($profile['profile_photo'])
        ? '../' . ltrim((string)$profile['profile_photo'], '/')
        : '';
    return $profile;
}

try {
    $user = require_role('teacher');
    $conn = db();
    $userId = (int)$user['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $firstName = trim((string)($_POST['first_name'] ?? ''));
        $lastName = trim((string)($_POST['last_name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $phone = trim((string)($_POST['phone'] ?? ''));
        $school = trim((string)($_POST['school'] ?? ''));
        $department = trim((string)($_POST['department'] ?? ''));
        $gradeLevel = trim((string)($_POST['grade_level'] ?? ''));
        $sections = trim((string)($_POST['sections'] ?? ''));
        $emailNotifications = isset($_POST['email_notifications']) ? (int)$_POST['email_notifications'] : 0;
        $progressSummary = isset($_POST['progress_summary']) ? (int)$_POST['progress_summary'] : 0;

        if ($firstName === '' || $lastName === '') {
            throw new InvalidArgumentException('First name and last name are required.');
        }
        if (mb_strlen($firstName) > 80 || mb_strlen($lastName) > 80) {
            throw new InvalidArgumentException('Names must be 80 characters or fewer.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) {
            throw new InvalidArgumentException('Enter a valid email address.');
        }
        foreach ([$phone, $school, $department, $gradeLevel, $sections] as $value) {
            if (mb_strlen($value) > 500) {
                throw new InvalidArgumentException('Profile fields are too long.');
            }
        }

        $photoPath = null;
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['profile_photo'];
            if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 2 * 1024 * 1024) {
                throw new InvalidArgumentException('Profile photo must be an image no larger than 2 MB.');
            }
            $imageInfo = @getimagesize($file['tmp_name']);
            $mime = $imageInfo['mime'] ?? '';
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            if (!$imageInfo || !isset($allowed[$mime])) {
                throw new InvalidArgumentException('Profile photo must be a JPG, PNG, or WebP image.');
            }
            $directory = __DIR__ . '/../uploads/profile';
            if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
                throw new RuntimeException('Profile photo directory could not be created.');
            }
            $photoPath = 'uploads/profile/' . bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
            if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../' . $photoPath)) {
                throw new RuntimeException('Profile photo could not be saved.');
            }
        }

        $conn->begin_transaction();
        $stmt = $conn->prepare('UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?');
        $stmt->bind_param('sssi', $firstName, $lastName, $email, $userId);
        $stmt->execute();

        if ($photoPath !== null) {
            $stmt = $conn->prepare(
                'INSERT INTO teacher_profiles (user_id, phone, school, department, grade_level, sections, email_notifications, progress_summary, profile_photo)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE phone=VALUES(phone), school=VALUES(school), department=VALUES(department), grade_level=VALUES(grade_level), sections=VALUES(sections), email_notifications=VALUES(email_notifications), progress_summary=VALUES(progress_summary), profile_photo=VALUES(profile_photo)'
            );
            $stmt->bind_param('isssssiis', $userId, $phone, $school, $department, $gradeLevel, $sections, $emailNotifications, $progressSummary, $photoPath);
        } else {
            $stmt = $conn->prepare(
                'INSERT INTO teacher_profiles (user_id, phone, school, department, grade_level, sections, email_notifications, progress_summary)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE phone=VALUES(phone), school=VALUES(school), department=VALUES(department), grade_level=VALUES(grade_level), sections=VALUES(sections), email_notifications=VALUES(email_notifications), progress_summary=VALUES(progress_summary)'
            );
            $stmt->bind_param('isssssii', $userId, $phone, $school, $department, $gradeLevel, $sections, $emailNotifications, $progressSummary);
        }
        $stmt->execute();
        $conn->commit();
    }

    $stmt = $conn->prepare('SELECT u.id, u.first_name, u.last_name, u.email, p.phone, p.school, p.department, p.grade_level, p.sections, COALESCE(p.email_notifications, 1) AS email_notifications, COALESCE(p.progress_summary, 1) AS progress_summary, p.profile_photo FROM users u LEFT JOIN teacher_profiles p ON p.user_id = u.id WHERE u.id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $profile = $stmt->get_result()->fetch_assoc() ?: [];
    respond(['profile' => teacher_profile_response($user, $profile)]);
} catch (InvalidArgumentException $error) {
    respond(['error' => $error->getMessage()], 400);
} catch (Throwable $error) {
    if (isset($conn) && $conn->errno) {
        $conn->rollback();
    }
    respond(['error' => 'Teacher profile could not be saved.'], 500);
}
