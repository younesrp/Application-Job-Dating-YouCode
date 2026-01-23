<?php
session_start();
echo "<pre>";
echo "Session data:\n";
print_r($_SESSION);
echo "\nUser role: " . ($_SESSION['user_role'] ?? 'NOT SET');
echo "\nRedirect URL: " . (($_SESSION['user_role'] === 'admin') ? '/admin/dashboard' : '/dashboard');
