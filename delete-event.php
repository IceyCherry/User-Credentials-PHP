<?php

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