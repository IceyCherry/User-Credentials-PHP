<?php

// Check if the user is logged in and if they are an admin

require_once 'Models/Auth.php'; // Include Auth class
require_once 'Models/UserManager.php';
use Models\UserManager;
use Models\Auth;

$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUser()->getRole() !== 'admin') {
    header('Location: index.php?error=You must be an admin to create users');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $fullName = trim($_POST['full_name']);
    $role = trim($_POST['role']);
    $password = trim($_POST['password']);
    
    // Validate required fields
    if (empty($username) || empty($email) || empty($fullName) || empty($role) || empty($password)) {
        header('Location: new-user.php?error=All fields are required');
        exit;
    }

    // Sanitize the data (basic sanitization example)
    $username = filter_var($username, FILTER_SANITIZE_STRING);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $fullName = filter_var($fullName, FILTER_SANITIZE_STRING);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: new-user.php?error=Invalid email format');
        exit;
    }
    
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    // Instantiate UserManager
    $userManager = new UserManager(); // Fix: instantiate UserManager

    // Create the user
    if ($userManager->createUser($username, $email, $fullName, $role, $hashedPassword)) {
        header('Location: users.php?success=User created successfully');
        exit;
    } else {
        header('Location: new-user.php?error=Failed to create user');
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Create New User</h1>
    <div class="mb-4">
        <a href="users.php" class="text-blue-500 hover:text-blue-700 inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Users List
        </a>
    </div>
    <?php if (isset($_GET['error'])) { ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($_GET['error']); ?></span>
        </div>
    <?php } ?>
    <?php if (isset($_GET['success'])) { ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($_GET['success']); ?></span>
        </div>
    <?php } ?>
    <form action="new-user.php" method="POST" class="space-y-4">
        <div>
            <label for="username" class="block text-sm font-medium">Username</label>
            <input type="text" name="username" id="username" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label for="email" class="block text-sm font-medium">Email</label>
            <input type="email" name="email" id="email" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label for="full_name" class="block text-sm font-medium">Full Name</label>
            <input type="text" name="full_name" id="full_name" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label for="role" class="block text-sm font-medium">Role</label>
            <select name="role" id="role" class="w-full border rounded px-3 py-2" required>
                <option value="admin">Admin</option>
                <option value="editor">Editor</option>
                <option value="member">Member</option>
            </select>
        </div>
        <div>
            <label for="password" class="block text-sm font-medium">Password</label>
            <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2" required>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Create User</button>
    </form>
</body>
</html>
