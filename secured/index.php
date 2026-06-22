<?php
ob_start();
include 'db.php';

function future_child_support_redirect_home($queryKey, $message)
{
    header('Location: ../index.html?' . $queryKey . '=' . urlencode($message));
    exit();
}

function future_child_support_uploaded_file_name($originalName)
{
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    return 'proof-' . date('YmdHis') . '-' . substr(md5(uniqid('', true)), 0, 8) . '.' . $extension;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['payment_type'])) {
    future_child_support_redirect_home('error', 'Please select a payment method.');
}

$paymentType = trim($_POST['payment_type']);
$allowedPaymentTypes = ['btc_payment', 'bank_payment'];

if (!in_array($paymentType, $allowedPaymentTypes, true)) {
    future_child_support_redirect_home('error', 'Please select a valid payment method.');
}

$itemName = trim($_POST['item_name'] ?? '');
$currencyCode = trim($_POST['currency_code'] ?? '');
$fullName = trim($_POST['fullname'] ?? '');
$company = trim($_POST['company'] ?? '');
$country = trim($_POST['country'] ?? '');
$state = trim($_POST['state'] ?? '');
$address = trim($_POST['address'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$note = trim($_POST['note'] ?? '');
$amount = trim($_POST['amount'] ?? ($_POST['amount1'] ?? ''));

if ($company === '') {
    $company = ' ';
}

if ($note === '') {
    $note = ' ';
}

if (
    $itemName === '' ||
    $currencyCode === '' ||
    $fullName === '' ||
    $country === '' ||
    $state === '' ||
    $address === '' ||
    $phone === '' ||
    $email === '' ||
    $amount === ''
) {
    future_child_support_redirect_home('error', 'Please complete all required donation fields.');
}

$proofFile = 'N/A';

if ($paymentType === 'btc_payment') {
    if (empty($_FILES['file_upload']['name'])) {
        future_child_support_redirect_home('error', 'Please upload your proof of payment.');
    }

    $fileType = strtolower(pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION));
    $fileSize = (int) ($_FILES['file_upload']['size'] ?? 0);
    $proofFile = future_child_support_uploaded_file_name($_FILES['file_upload']['name']);
    $targetRelativePath = 'uploads/' . $proofFile;
    $validationResult = validateImageUpload($targetRelativePath, $fileType, $fileSize);

    if ($validationResult !== $targetRelativePath) {
        future_child_support_redirect_home('error', $validationResult);
    }

    $uploadDirectory = __DIR__ . '/uploads';
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0775, true);
    }

    if (!move_uploaded_file($_FILES['file_upload']['tmp_name'], __DIR__ . '/' . $targetRelativePath)) {
        future_child_support_redirect_home('error', 'Unable to save the uploaded proof of payment.');
    }
}

$transactionId = 'Txid' . substr(uniqid('', true), 0, 3) . substr(uniqid('', true), -3);
$createdAt = date('Y-m-d H:i:s');
$status = 'Processing';

$sql = "INSERT INTO donations (
            item_name,
            fullname,
            company_name,
            country,
            state,
            street,
            phone,
            email,
            amount,
            payment_type,
            proof,
            note,
            transac_id,
            status,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($dbconnec, $sql);

if (!$stmt) {
    future_child_support_redirect_home('error', 'Unable to process your donation right now.');
}

mysqli_stmt_bind_param(
    $stmt,
    'sssssssssssssss',
    $itemName,
    $fullName,
    $company,
    $country,
    $state,
    $address,
    $phone,
    $email,
    $amount,
    $paymentType,
    $proofFile,
    $note,
    $transactionId,
    $status,
    $createdAt
);

$saved = mysqli_stmt_execute($stmt);
$databaseError = mysqli_stmt_error($stmt);
mysqli_stmt_close($stmt);

if (!$saved) {
    if ($proofFile !== 'N/A') {
        $proofPath = __DIR__ . '/uploads/' . $proofFile;
        if (is_file($proofPath)) {
            unlink($proofPath);
        }
    }
    future_child_support_redirect_home('error', 'Donation could not be saved: ' . $databaseError);
}

future_child_support_redirect_home('suc', 'Your donation has been saved and is now visible in the admin dashboard.');

function validateImageUpload($file, $fileExe, $fileSize)
{
    $allowedExtensions = ['jpg', 'png', 'jpeg', 'pdf'];

    if (!in_array($fileExe, $allowedExtensions, true)) {
        return 'File format not allowed. Please upload a JPG, PNG, or PDF file.';
    }

    if ($fileSize > 2097152) {
        return 'File is too large. Maximum size allowed is 2MB.';
    }

    if ($fileSize === 0) {
        return 'The uploaded file is empty.';
    }

    if (function_exists('finfo_open') && !empty($_FILES['file_upload']['tmp_name'])) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['file_upload']['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($mime, $allowedMimes, true)) {
            return 'Invalid file content. The file type does not match its extension.';
        }
    }

    return $file;
}
