<?php

// Include necessary files

require_once 'path/to/your/autoload.php';
use Models\Auth;
use Models\UserManager;
use Models\User;

session_start();

// Confirm the user is logged in and is an admin
$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUser()->role !== 'admin') {
    header('Location: index.php?error=You must be an admin to manage users');
    exit();
}

// Check if user ID is provided in the URL
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    header('Location: users.php?error=No user ID provided');
    exit();
}

// Get the user ID from the URL
$userId = $_GET['user_id'];

// Initialize UserManager and retrieve the user
$userManager = new UserManager();
$user = $userManager->getUser($userId); // Assuming getUserById() is replaced by getUser()
if (!$user) {
    header('Location: users.php?error=User not found');
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $fullName = filter_var(trim($_POST['fullName']), FILTER_SANITIZE_STRING);
    $role = filter_var(trim($_POST['role']), FILTER_SANITIZE_STRING);
    $password = trim($_POST['password']);
    
    // Check required fields
    if (empty($email) || empty($fullName) || empty($role)) {
        header("Location: edit-user.php?user_id=$userId&error=All fields are required");
        exit();
    }
    
    // If password is entered, sanitize and hash it
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
    } else {
        // If password is empty, retain the existing password
        $password = $user->getPassword();
    }

    // Update user in the UserManager class
    $updatedUser = new User($userId, $user->username, $email, $fullName, $role, $password);
    $updateSuccess = $userManager->editUser($userId, $email, $fullName, $role, $password); // Pass arguments properly
    
    if ($updateSuccess) {
        header('Location: users.php?success=User edited successfully');
        exit();
    } else {
        header("Location: edit-user.php?user_id=$userId&error=Failed to edit user");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
</head>
<body>
    <h1>Edit User</h1>

    <p><a href="users.php">Back to Users</a></p>
    
    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>
    
    <form method="post" action="edit-user.php?user_id=<?php echo $userId; ?>">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user->username); ?>" disabled>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user->email); ?>">

        <label for="fullName">Full Name:</label>
        <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($user->fullName); ?>">

        <label for="role">Role:</label>
        <select name="role" id="role">
            <option value="admin" <?php echo ($user->role === 'admin') ? 'selected' : ''; ?>>Admin</option>
            <option value="user" <?php echo ($user->role === 'user') ? 'selected' : ''; ?>>User</option>
        </select>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password">
        <p>Password will not be changed if left blank.</p>

        <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
        
        <button type="submit">Update User</button>
    </form>
</body>
</html>
