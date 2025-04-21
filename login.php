<?php
/**
 * Assignment 2 Instructions
 * 
 * - Create a login form to gather the user's username and password.
 * - If the user is already logged in and attempts to visit login.php, redirect them to index.php.
 * - When the form is submitted, validate and sanitize all data and ensure required values are submitted, then login the user using the Auth class and redirect them to index.php
 * - If the username or password does not validate, display an error message at the top of the login form. Note: see the success and error get attribute blocks in the index.php file for an example of how to display messages.
 */

// Include the necessary classes
require_once 'Models/Auth.php';
require_once 'Models/User.php';

use Models\Auth;
use Models\User;

// Initialize the Auth class
$auth = new Auth();

// Check if the user is already logged in. If yes, redirect to index.php.
if ($auth->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = ''; // Variable to hold any error message

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate the input data
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    if (empty($username) || empty($password)) {
        $error = 'Username and password are required.';
    } else {
        // Attempt to log in the user
        if ($auth->login($username, $password)) {
            // Redirect to the index page on successful login
            header('Location: index.php');
            exit();
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="container mx-auto p-6">

    <h1 class="text-2xl font-bold mb-4">Login</h1>

    <!-- Display error message if there was an issue -->
    <?php if (!empty($error)) { ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php } ?>

    <!-- Login form -->
    <form action="login.php" method="POST" class="max-w-sm mx-auto bg-white p-6 rounded-lg shadow-lg">
        <div class="mb-4">
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" id="username" name="username" class="w-full p-2 border border-gray-300 rounded mt-1" required>
        </div>
        
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" name="password" class="w-full p-2 border border-gray-300 rounded mt-1" required>
        </div>

        <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Login</button>
    </form>

</body>

</html>
