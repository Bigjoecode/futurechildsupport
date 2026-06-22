<?php
ob_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['sendmail'])) {
    header('Location: ../index.html');
    exit();
}

$fullName = trim($_POST['uname'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['sub'] ?? '');
$message = trim($_POST['mesg'] ?? '');

if ($phone === '') {
    $phone = 'Not Provided';
}

if ($fullName === '' || $email === '' || $subject === '' || $message === '') {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'false',
        'message' => 'Please fill in all required contact fields.'
    ]);
    exit();
}

$sql = "INSERT INTO contact_messages (full_name, email, phone, subject, message, status, created_at)
        VALUES (?, ?, ?, ?, ?, 'new', ?)";
$stmt = mysqli_prepare($dbconnec, $sql);

if (!$stmt) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'false',
        'message' => 'Unable to process your message right now.'
    ]);
    exit();
}

$createdAt = date('Y-m-d H:i:s');
mysqli_stmt_bind_param($stmt, 'ssssss', $fullName, $email, $phone, $subject, $message, $createdAt);
$saved = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header('Content-Type: application/json');

if (!$saved) {
    echo json_encode([
        'status' => 'false',
        'message' => 'Unable to save your message right now. Please try again.'
    ]);
    exit();
}

echo json_encode([
    'status' => 'true',
    'message' => 'Your message has been saved for the admin team.'
]);
