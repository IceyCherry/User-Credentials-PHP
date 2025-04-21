<?php
/**
 * Assignment 2 Instructions
 * 
 * - Log out the current user using the Auth class.
 * - Redirect the user to the index/php page.
 */

require_once 'Models/Auth.php';

use Models\Auth;

// Create an Auth object
$auth = new Auth();

// Log out the user
$auth->logout();

// Redirect to the index.php page
header('Location: index.php');
exit;
?>
