<?php
/**
 * Assignment 2 Instructions
 * 
 * - Check if the user is logged in and is an admin, if not redirect to login.php with a message indicating that the user must be an admin to manage users.
 * - Get all users from the database using the UserManager.
 * - Display all users in a table with the following columns: ID, Username, Email, Full Name, Role, and Actions.
 * - The Actions column should have two links: Edit and Delete.
 * - The Edit link should go to edit-user.php with the user ID as a query parameter.
 * - The Delete link should go to delete-user.php with the user ID as a query parameter and should have a JavaScript confirmation prompt before deleting the user.
 */

require_once 'Models/UserManager.php';
require_once 'Models/Auth.php';
use Models\UserManager;
use Models\Auth;

// Check if user is logged in and is an admin
$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUser()->getRole() !== 'admin') {
    header('Location: login.php?error=You must be an admin to manage users');
    exit;
}

// Get all users using UserManager
$userManager = new UserManager();
$users = $userManager->getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Manage Users</h1>

    <!-- Navigation Buttons -->
    <div class="mb-4 space-x-2">
        <a href="index.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Index
        </a>
    </div>

    <!-- Display success message if present -->
    <?php if (isset($_GET['success'])) { ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($_GET['success']); ?></span>
        </div>
    <?php } ?>

    <!-- Display error message if present -->
    <?php if (isset($_GET['error'])) { ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($_GET['error']); ?></span>
        </div>
    <?php } ?>

    <!-- Table to display all users -->
    <table class="min-w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-4 py-2">ID</th>
                <th class="border px-4 py-2">Username</th>
                <th class="border px-4 py-2">Email</th>
                <th class="border px-4 py-2">Full Name</th>
                <th class="border px-4 py-2">Role</th>
                <th class="border px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) { ?>
                <tr class="hover:bg-gray-50">
                    <td class="border px-4 py-2"><?php echo htmlspecialchars($user->id); ?></td>
                    <td class="border px-4 py-2"><?php echo htmlspecialchars($user->username); ?></td>
                    <td class="border px-4 py-2"><?php echo htmlspecialchars($user->email); ?></td>
                    <td class="border px-4 py-2"><?php echo htmlspecialchars($user->fullName); ?></td>
                    <td class="border px-4 py-2"><?php echo htmlspecialchars($user->role); ?></td>
                    <td class="border px-4 py-2">
                        <a href="edit-user.php?user_id=<?php echo $user->id; ?>"
                            class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Edit</a>
                        <a href="delete-user.php?id=<?php echo $user->id; ?>"
                            class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
                            onclick="return confirm('Are you sure you want to delete this user?');">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>

</html>