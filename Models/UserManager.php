<?php
/**
 * Assignment 2 Instructions
 * 
 * - Create a new class called UserManager in the Models namespace.
 * - The UserManager class should have the following properties:
 * --- A private property called file that stores the path to the file where user data is stored.
 * - The UserManager class should have the following methods: 
 * --- A constructor that checks if the file exists and creates it if it doesn't. 
 * --- A getUsers method that reads the user data from the file and returns an array of User objects. 
 * --- A getUser method that accepts a user ID and returns the corresponding User object. If the user ID is not found, it should return null.
 * --- A createUser method that accepts a username, email, full name, role, and password, creates a new User object, and adds it to the user data file. If the username is already in use, it should return an error message. Note: Use the password_hash function to hash the password.
 * --- An editUser method that accepts a user ID, email, full name, role, and password, updates the corresponding User object, and saves the changes to the user data file. If the password is empty, it should not be updated. 
 * --- A deleteUser method that accepts a user ID and deletes the corresponding User object from the user data file. Only users with the role 'admin' should be able to delete users.
 * --- An isUsernameAvailable method that accepts a username and checks if it is available in the user data file. It should return true if the username is available, false otherwise.
 * --- A private method called userToString that accepts a User object and converts it to a string format for storage in the file.
 * --- A private method called stringToUser that accepts a string and converts it to a User object.
 */

namespace Models;
 
use Models\User;
 
class UserManager
{
    // Path to the user data file
    private $file;

    // Constructor
    public function __construct($file = 'users.txt')
    {
        $this->file = $file;

        // Check if the file exists, if not, create it
        if (!file_exists($this->file)) {
            file_put_contents($this->file, ""); // Create an empty file if it doesn't exist
        }
    }

    // Get all users from the file and return as User objects
    public function getUsers()
    {
        $users = [];
        $lines = file($this->file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $users[] = $this->stringToUser($line);
        }

        return $users;
    }

    // Get all users (alias for getUsers())
    public function getAllUsers()
    {
        return $this->getUsers();
    }

    // Get a user by ID
    public function getUser($id)
    {
        $users = $this->getUsers();
        foreach ($users as $user) {
            if ($user->id === $id) {
                return $user;
            }
        }
        return null; // User not found
    }

    // Get a user by ID (Alias for getUser)
    public function getUserById($id)
    {
        return $this->getUser($id);
    }

    // Create a new user
    public function createUser($username, $email, $fullName, $role, $password)
    {
        // Check if the username is already taken
        if (!$this->isUsernameAvailable($username)) {
            return 'Username is already taken';
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Create a new User object
        $id = uniqid(); // Generate a unique ID
        $user = new User($id, $username, $email, $fullName, $role, $hashedPassword);

        // Save the new user to the file
        file_put_contents($this->file, $this->userToString($user) . PHP_EOL, FILE_APPEND);

        return 'User created successfully';
    }

    // Edit an existing user
    public function editUser($id, $email, $fullName, $role, $password)
    {
        $users = $this->getUsers();
        $updated = false;

        foreach ($users as $user) {
            if ($user->id === $id) {
                // Update user details
                $user->email = $email;
                $user->fullName = $fullName;
                $user->role = $role;

                // If a new password is provided, hash it and update it
                if (!empty($password)) {
                    $user->setPassword($password);
                }

                // Save the changes back to the file
                file_put_contents($this->file, implode(PHP_EOL, array_map([$this, 'userToString'], $users)) . PHP_EOL);
                $updated = true;
                break;
            }
        }

        return $updated ? 'User edited successfully' : 'User not found';
    }

    // Delete a user by ID (only admins can delete)
    public function deleteUser($id, $currentUser)
    {
        // Only admins can delete users
        var_dump($currentUser->role); // This will output the role
        if ($currentUser->role !== 'admin') {
            return 'You must be an admin to delete users';
        }
        
    
        // Get the list of users
        $users = $this->getUsers();
    
        // Filter out the user with the matching ID to be deleted
        $usersToKeep = array_filter($users, function ($user) use ($id) {
            return $user->id !== $id; // Remove user with matching ID
        });
    
        // If the user to delete is found (i.e., the user count decreases), update the file
        if (count($users) !== count($usersToKeep)) {
            // Update the user file by writing the remaining users back
            file_put_contents($this->file, implode(PHP_EOL, array_map([$this, 'userToString'], $usersToKeep)) . PHP_EOL);
            return 'User deleted successfully';
        }
    
        return 'User not found';
    }
    

    // Check if the username is available
    public function isUsernameAvailable($username)
    {
        $users = $this->getUsers();
        foreach ($users as $user) {
            if ($user->username === $username) {
                return false; // Username is taken
            }
        }
        return true; // Username is available
    }

    // Convert a User object to a string for file storage
    private function userToString(User $user)
    {
        return $user->id . '|' . $user->username . '|' . $user->email . '|' . $user->fullName . '|' . $user->role . '|' . $user->getPassword();
    }

    // Convert a string to a User object
    private function stringToUser($string)
    {
        list($id, $username, $email, $fullName, $role, $password) = explode('|', $string);
        return new User($id, $username, $email, $fullName, $role, $password);
    }
}
?>
