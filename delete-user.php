<?php
/**
 * Assignment 2 Instructions
 * 
 * - Confirm the current user is an admin. If they are not, redirect them to the index.php page with an error indicating they must be an admin to delete users.
 * - Confirm the user ID is provided in the URL. If it is not, redirect the user to the users.php page with an error indicating no user ID was provided.
 * - If the user ID is provided, attempt to delete the user using the UserManager class. Redirect the user back to users.php with a success indicating the user was deleted. If the user was not deleted (id did not exist in users.txt), redirect the user back to users.php with an error indicating the user was not found.
 */



 require_once 'Models/Auth.php';
 require_once 'Models/UserManager.php';
 use Models\Auth;
 use Models\UserManager;
 
 $auth = new Auth();
 
 // Check if the user is logged in and is an admin
 if (!$auth->isLoggedIn() || $auth->getUser()->getRole() !== 'admin') {
     header('Location: index.php?error=You must be an admin to delete users');
     exit;
 }
 
 // Check if user ID is provided in the GET request
 if (!isset($_GET['id'])) {
     header('Location: users.php?error=No user ID provided');
     exit;
 }
 
 $userId = $_GET['id'];
 
 // Instantiate UserManager class
 $userManager = new UserManager();
 
 // Get the current logged-in user
 $currentUser = $auth->getUser();
 
 // Attempt to delete the user
 $result = $userManager->deleteUser($userId, $currentUser);
 
 if ($result === 'User deleted successfully') {
     header('Location: users.php?success=User deleted successfully');
 } else {
     header("Location: users.php?error=$result");
 }
 exit;
 ?>