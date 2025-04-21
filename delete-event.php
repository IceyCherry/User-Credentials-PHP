<?php
/**
 * Assignment 2 Instructions
 * 
 * - Check the user is allowed to delete this event. If they are not redirect to index.php with an error message. Admins can delete any event, editors and members can only delete events they created.
 * - Check the event id is set in the GET request. If not, redirect to index.php with an error message indicating to try again.
 * - If the user is allowed to delete the event, attempt to delete it.
 * - If successful, redirect to index.php with a success message. If not (user's id did not exist, etc.), redirect to index.php with an error message.
 */

// Import and use classes
// var_dump($_GET);
// die();

// Include necessary files
// Start session


require_once 'Models/Auth.php';
require_once 'Models/EventHandler.php';

use Models\Auth;
use Models\EventHandler;

session_start();

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: index.php?error=You must be logged in to delete events');
    exit;
}

$user = $auth->getUser();

if (!isset($_GET['id'])) {
    header('Location: index.php?error=Event ID is missing');
    exit;
}
$eventId = $_GET['id'];

$eventHandler = new EventHandler(null, []);

if ($user->getRole() === 'admin' || $eventHandler->isEventCreatedByUser($eventId, $user->getId())) {
    if ($eventHandler->deleteEvent($eventId, $user->getId())) {
        header('Location: index.php?success=Event deleted successfully');
        exit;
    } else {
        header('Location: index.php?error=Failed to delete event');
        exit;
    }
} else {
    header('Location: index.php?error=You are not authorized to delete this event');
    exit;
}
?>