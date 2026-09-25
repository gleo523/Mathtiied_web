<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/auth.php';

try {
    require_role('admin');
    $conn = db();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = request_json();
        $studentId = (int)($data['student_id'] ?? 0);
        $teacherId = (int)($data['teacher_id'] ?? 0);
        if ($studentId < 1) {
            throw new InvalidArgumentException('A student is required.');
        }
        if ($teacherId > 0) {
            $check = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'teacher'");
            $check->bind_param('i', $teacherId);
            $check->execute();
            if (!$check->get_result()->fetch_assoc()) {
                throw new InvalidArgumentException('Selected teacher does not exist.');
            }
        }
        $stmt = $conn->prepare("UPDATE users SET teacher_id = NULLIF(?, 0) WHERE id = ? AND role = 'student'");
        $stmt->bind_param('ii', $teacherId, $studentId);
        $stmt->execute();
        if ($stmt->affected_rows < 0) {
            throw new RuntimeException('Student assignment failed.');
        }
    }
    $teachers = $conn->query("SELECT id, username, first_name, last_name FROM users WHERE role = 'teacher' ORDER BY last_name, first_name, username")->fetch_all(MYSQLI_ASSOC);
    respond(['teachers' => $teachers]);
} catch (InvalidArgumentException $error) {
    respond(['error' => $error->getMessage()], 400);
} catch (Throwable $error) {
    respond(['error' => 'Student assignment request failed.'], 500);
}
