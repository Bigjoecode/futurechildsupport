<?php
require_once __DIR__ . '/common.php';

future_child_support_admin_logout();
header('Location: login.php');
exit();
