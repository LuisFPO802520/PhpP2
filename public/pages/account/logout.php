<?php
require_once __DIR__ . '/../../../includes/auth.php';


logout();
header('Location: /pages/account/login.php');
include __DIR__ . '/../../../includes/messages.php';
exit;
