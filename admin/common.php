<?php
require_once __DIR__ . '/../secured/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function future_child_support_admin_session_key()
{
    return 'future_child_support_admin';
}

function future_child_support_admin_is_authenticated()
{
    return !empty($_SESSION[future_child_support_admin_session_key()]);
}

function future_child_support_admin_login($username)
{
    global $appConfig;

    session_regenerate_id(true);
    $_SESSION[future_child_support_admin_session_key()] = [
        'username' => $username,
        'name' => $appConfig['admin_name'],
        'logged_in_at' => time(),
    ];
}

function future_child_support_admin_logout()
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}

function future_child_support_admin_credentials_match($username, $password)
{
    global $appConfig;

    $normalizedUsername = trim((string) $username);
    $validUsernames = [
        (string) $appConfig['admin_username'],
        (string) $appConfig['support_email'],
    ];

    $isValidUsername = in_array($normalizedUsername, $validUsernames, true);
    $isValidPassword = hash_equals((string) $appConfig['admin_password'], (string) $password);

    return $isValidUsername && $isValidPassword;
}

function future_child_support_require_admin()
{
    if (!future_child_support_admin_is_authenticated()) {
        header('Location: login.php');
        exit();
    }
}

function future_child_support_fetch_scalar(mysqli $connection, $query)
{
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return 0;
    }

    $row = mysqli_fetch_row($result);
    mysqli_free_result($result);

    return (int) ($row[0] ?? 0);
}

function future_child_support_fetch_rows(mysqli $connection, $query)
{
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return [];
    }

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    mysqli_free_result($result);

    return $rows;
}

function future_child_support_format_value($value, $fallback = 'N/A')
{
    $value = trim((string) $value);
    return $value === '' ? $fallback : htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function future_child_support_format_datetime($value)
{
    if (empty($value)) {
        return 'N/A';
    }

    $timestamp = strtotime((string) $value);
    if ($timestamp === false) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    return date('M j, Y g:i A', $timestamp);
}

function future_child_support_compose_location($country, $state)
{
    $parts = array_filter([
        trim((string) $country),
        trim((string) $state),
    ]);

    if (empty($parts)) {
        return 'N/A';
    }

    return htmlspecialchars(implode(', ', $parts), ENT_QUOTES, 'UTF-8');
}
