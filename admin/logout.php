<?php

declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$_SESSION = [];
session_destroy();
header('Location: ' . BASE_URL . '/admin/login.php');
exit;
