# 🛂 User Credentials and Event Management System

A PHP-based web application for managing users and events with role-based access control. This project was developed as part of a web development assignment and includes login/logout functionality, user sessions, and role-specific permissions.

---

## 📌 Features

- **User Authentication**
  - Secure login and logout
  - Session-based access
  - Password Hashing

- **Role-Based Access**
  - **Admin**: Full control—can view, edit, delete events and manage users
  - **Editor**: Can edit any event
  - **Author**: Can view, edit, and delete *their own* events

- **Event Management**
  - Each event lists the author's full name
  - Conditional display of action buttons based on user role
  - Welcome message on login displaying user's full name and role

---

## 🧪 Default Credentials

Use these test credentials to log in:

| Username | Password | Role   |
|----------|----------|--------|
| admin    | password | Admin  |
| editor   | password | Editor |
| author   | password | Author |

---

---

## ⚙️ Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/IceyCherry/User-Credentials-PHP.git
   cd User-Credentials-PHP


- **Run it on a local server:

  - Use XAMPP or Laragon or LocalWP

  - Place the folder inside htdocs or equivalent

  - Start Apache

  - Visit http://localhost/User-Credentials-PHP


- **🛠️ Tech Stack
  - PHP (vanilla)
  - HTML5 & CSS3
  - JSON for data storage (no database used)
  - Basic session management


