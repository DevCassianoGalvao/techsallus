<?php
require_once dirname(__DIR__) . '/core/Auth.php';
Auth::logout();
header('Location: ' . Security::url('/admin/login.php'));
exit;
