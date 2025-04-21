<?php
/**
 * Assignment 2 Instructions:
 * 
 * - Add a login button that links to the login.php page if the user is not logged in.
 * - Add a logout button that links to the logout.php page if the user is logged in.
 * - Add a "Manage User" button that links to the users.php page if the user is logged in and is an admin.
 * - Add a welcome message that displays the user's full name and role if the user is logged in.
 * - Add an Author column listing the full name of the event author.
 * - Add an Actions column with Edit and Delete links for each event. Display the edit link if the user is an admin or editor, or if the user is the author of the event. Display the delete button if the user is an admin or author of the event.
 * 
 */

// Testing login using:
// Username: admin
// Password: password
// Import and use classes
require_once 'Models/User.php';
require_once 'Models/Auth.php';
require_once 'Models/Event.php';
require_once 'Models/UserManager.php';
session_start();

use Models\Auth;
use Models\Event;
use Models\UserManager;

// Check if user is logged in
$user = Auth::getAuthenticatedUser();
$isLoggedIn = $user !== null;

// Read events from file (unchanged)
$events = file('events.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$pastEvents = $thisWeekEvents = $futureEvents = [];
$today = new DateTime();
$nextWeek = (clone $today)->modify('+7 days');

foreach ($events as $line) {
    $event = Event::fromString($line);
    if ($event->end < $today) {
        $pastEvents[] = $event;
    } elseif ($event->end <= $nextWeek) {
        $thisWeekEvents[] = $event;
    } else {
        $futureEvents[] = $event;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Event List</title>
</head>
<body class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Event List</h1>

    <!-- Authentication and User Info -->
    <div class="mb-4 flex justify-between items-center">
        <?php if ($isLoggedIn): ?>
            <p class="text-gray-700">Welcome, <strong><?php echo htmlspecialchars($user->fullName); ?></strong>
                (<?php echo htmlspecialchars($user->getRole()); ?>)</p>
            <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a>
        <?php else: ?>
            <a href="login.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Login</a>
        <?php endif; ?>
    </div>

    <!-- User Management Buttons -->
    <?php if ($isLoggedIn): ?>
        <div class="mb-4 space-x-2">
            <a href="users.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Manage Users</a>
            <?php if ($user->getRole() === 'admin'): ?>
                <a href="new-user.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Create New User</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Success & Error Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <span class="block sm:inline"><?php echo htmlspecialchars($_GET['success']); ?></span>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <span class="block sm:inline"><?php echo htmlspecialchars($_GET['error']); ?></span>
        </div>
    <?php endif; ?>

    <!-- Link to create new event -->
    <a href="new-event.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mb-4">New Event</a>

    <!-- Event Tables (unchanged except role checks) -->
    <?php foreach (["Past Events" => $pastEvents, "This Week" => $thisWeekEvents, "Future Events" => $futureEvents] as $title => $group): ?>
        <h2 class="text-xl font-semibold mt-6 mb-2"><?php echo $title; ?></h2>
        <table class="min-w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">Name</th>
                    <th class="border px-4 py-2">Description</th>
                    <th class="border px-4 py-2">Start Date/Time</th>
                    <th class="border px-4 py-2">End Date/Time</th>
                    <th class="border px-4 py-2">Author</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($group as $event):
                    $userManager = new UserManager();
                    $author = $userManager->getUserById($event->authorId);
                    $authorName = $author ? htmlspecialchars($author->fullName) : 'Unknown';
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($event->name); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($event->description); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($event->start->format('F j, Y, g:i a')); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($event->end->format('F j, Y, g:i a')); ?></td>
                        <td class="border px-4 py-2"><?php echo $authorName; ?></td>
                        <td class="border px-4 py-2">
                            <?php if ($isLoggedIn && ($user->getRole() === 'admin' || $user->getRole() === 'editor' || $user->id === $event->authorId)): ?>
                                <a href="edit-event.php?id=<?php echo $event->id; ?>"
                                   class="bg-yellow-500 text-white px-4 py-2 rounded-full hover:bg-yellow-600 w-full text-center block mb-2">Edit</a>
                            <?php endif; ?>
                            <?php if ($isLoggedIn && ($user->getRole() === 'admin' || $user->id === $event->authorId)): ?>
                                <a href="delete-event.php?id=<?php echo $event->id; ?>"
                                   class="bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-600 w-full text-center block"
                                   onclick="return confirm('Are you sure you want to delete this event?');">
                                   Delete
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</body>
</html>