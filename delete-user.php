<?php

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