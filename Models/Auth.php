<?php

 namespace Models;

 require_once 'Models/User.php';
 
 use Models\User;
 
 class Auth {
 
     // Login method
     public function login($username, $password) {
         // Start the session at the beginning of the login process
         session_start();
 
         // Read users from users.txt
         $users = file('users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
         
         foreach ($users as $line) {
             // Assuming that each line contains 'userID|username|email|fullName|role|hashedPassword'
             list($storedId, $storedUsername, $storedEmail, $storedFullName, $storedRole, $storedHashedPassword) = explode('|', $line);
         
             // Check if username matches and password is correct
             if ($username === $storedUsername && password_verify($password, $storedHashedPassword)) {
                 // Store user in session with all required properties (id, username, email, fullName, role, hashedPassword)
                 $_SESSION['user'] = new User($storedId, $storedUsername, $storedEmail, $storedFullName, $storedRole, $storedHashedPassword);
         
                 return true; // User authenticated successfully
             }
         }
         
         return false; // No matching user found
     }
 
     // Logout method
     public function logout() {
         session_start();
         session_destroy();
     }
 
     // Check if user is logged in
     public function isLoggedIn() {
         session_start();
         return isset($_SESSION['user']);
     }
 
     // Get the current logged-in user
     public function getUser() {
         // session_start(); // No need to start the session again here as it has already been done in the login method
         return isset($_SESSION['user']) ? $_SESSION['user'] : false;
     }
 
     // Method to retrieve the authenticated user from the session
     public static function getAuthenticatedUser() {
         // Return the user from session if logged in
         return isset($_SESSION['user']) ? $_SESSION['user'] : null;
     }
 }
 