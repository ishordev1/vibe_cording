<?php
/**
 * Logout Action
 * Handles user logout
 */

require_once __DIR__ . '/../config/config.php';

// Destroy all session data
session_unset();
session_destroy();

// Redirect to home page
setFlashMessage('success', 'You have been logged out successfully.');
redirect('/index.php');
