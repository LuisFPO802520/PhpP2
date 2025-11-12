<?php
require_once __DIR__ . '/../../../includes/auth.php';

logout();
header('Location: /public/pages/account/login.php');
exit;
