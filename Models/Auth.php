<?php
/**
 * Assignment 2 Instructions 
 * 
 * - Create a new class called Auth in the Models namespace.
 * - The Auth class should have the following properties:
 * --- A public method called login that accepts a username and password, gets all users, then authenticates the username+password against the set of users. If a user with matching username and password found, return true; otherwise return false. Note: Use the password_verify function to verify the password. 
 * --- A public method called logout that destroys the session. 
 * --- A public method called isLoggedIn that returns true if a user is logged in, false otherwise. 
 * --- A public method called getUser that returns the currently logged-in user as a User object from the Session. If no user is logged in, return false.
 */

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
 