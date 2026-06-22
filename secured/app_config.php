<?php

if (!function_exists('future_child_support_detect_app_url')) {
    function future_child_support_detect_app_url()
    {
        $https = $_SERVER['HTTPS'] ?? '';
        $forwardedProto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '';
        $isHttps = (!empty($https) && strtolower((string) $https) !== 'off')
            || strtolower((string) $forwardedProto) === 'https'
            || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

        $scheme = $isHttps ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';

        if ($host === '') {
            return '';
        }

        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = '/';

        if ($scriptName !== '') {
            $basePath = str_replace('\\', '/', dirname(dirname($scriptName)));
            if ($basePath === '.' || $basePath === '\\' || $basePath === '') {
                $basePath = '/';
            }
        }

        return rtrim($scheme . '://' . $host . $basePath, '/');
    }
}

$config = [
    'app_url' => '',
    'site_name' => 'Future Child Support',
    'site_domain' => 'futurechildsupport.com',
    'support_email' => 'support@futurechildsupport.com',
    'db_host' => '127.0.0.1',
    'db_port' => 3306,
    'db_name' => 'future_child_support',
    'db_user' => 'root',
    'db_pass' => '',
    'admin_name' => 'Future Child Support Admin',
    'admin_username' => 'admin',
    'admin_password' => 'ChangeMe123!',
];

$configFile = __DIR__ . '/config.php';
if (is_file($configFile)) {
    $overrides = require $configFile;
    if (is_array($overrides)) {
        $config = array_replace($config, $overrides);
    }
}

$config['app_url'] = rtrim((string) $config['app_url'], '/');
if ($config['app_url'] === '') {
    $config['app_url'] = future_child_support_detect_app_url();
}

$config['db_port'] = (int) $config['db_port'];

return $config;
