<?php


 namespace Models;
 
 class User {
     public $id;
     public $username;
     public $email;
     public $fullName;
     public $role;
     private $password;
 
     // Constructor
     public function __construct($id, $username, $email, $fullName, $role, $password) {
         $this->id = $id;
         $this->username = $username;
         $this->email = $email;
         $this->fullName = $fullName;
         $this->role = $role;
         $this->password = $password; // Assume already hashed when instantiated
     }
 
     // Get the hashed password
     public function getPassword() {
         return $this->password;
     }
 
     // Set password (hash before storing)
     public function setPassword($newPassword) {
         $this->password = password_hash($newPassword, PASSWORD_DEFAULT);
     }
 
     // Verify password (used for login)
     public function verifyPassword($password) {
         return password_verify($password, $this->password);
     }
     public function getId() {
        return $this->id; // Ensure `$id` exists and is set in the constructor
    }
    public function getRole() {
        return $this->role;  // Returns the role of the user
    }
 }
 ?>
 