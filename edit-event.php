<?php

// Import and use classes
require_once 'Models/Event.php';
require_once 'Models/EventHandler.php';
require_once 'Utils/Redirect.php';
use Models\Event;
use Models\EventHandler;
use Utils\Redirect;

// Handle POST submission
if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
	// Create an instance of EventHandler with POST data and handle the request
	$eventHandler = new EventHandler( $db,$_POST );
	$eventHandler->handleRequest();
}

// Handle form rendering if no post
if ( ! isset( $_GET[ 'id' ] ) ) {
	// Redirect to index.php with an error message if 'id' is not set in the GET request
	Redirect::to( "index.php", [ "message" => "Something went wrong. Please try again" ] );
	exit();
}

// Get the event ID from the GET request
$id = $_GET[ 'id' ];

// Read events from the file and store them in an array
$events = file( 'events.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

// Initialize event variable
$event = null;

// Loop through each event line to find the event with the matching ID
foreach ( $events as $line ) {
	$eventObj = Event::fromString( $line );
	if ( $eventObj->id === $id ) {
		$event = $eventObj;
		break;
	}
}

// Redirect to index.php with an error message if the event is not found
if ( ! $event ) {
	Redirect::to( "index.php", [ "message" => "Something went wrong. Please try again" ] );
	exit();
}
?>

<!-- Edit Form -->
<!DOCTYPE html>
<html>

	<head>
		<!-- Include Tailwind CSS for styling -->
		<script src="https://cdn.tailwindcss.com"></script>
		<title>Edit Event</title>
	</head>

	<body class="container mx-auto p-4">
		<h1 class="text-2xl font-bold mb-4">Edit Event</h1>
		<!-- Link to go back to all events -->
		<div class="mb-4">
			<a href="index.php" class="text-blue-500 hover:text-blue-700 inline-flex items-center">
				<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
				</svg>
				Back to All Events
			</a>
		</div>
		<!-- Display success message if present in the GET request -->
		<?php if ( isset( $_GET[ 'success' ] ) ) { ?>
			<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
				<span class="block sm:inline"><?php echo htmlspecialchars( $_GET[ 'success' ] ); ?></span>
			</div>
		<?php } ?>
		<!-- Display error message if present in the GET request -->
		<?php if ( isset( $_GET[ 'error' ] ) ) { ?>
			<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
				<span class="block sm:inline"><?php echo htmlspecialchars( $_GET[ 'error' ] ); ?></span>
			</div>
		<?php } ?>
		<!-- Form to edit the event, submits to itself -->
		<form action="edit-event.php" method="POST" class="space-y-4">
			<!-- Hidden input to store event ID -->
			<input type="hidden" name="id" value="<?php echo htmlspecialchars( $event->id ); ?>">
			<div>
				<label for="name" class="block text-sm font-medium">Event Name</label>
				<!-- Input for event name, pre-filled with existing or GET data -->
				<input type="text" name="name" id="name" value="<?php echo htmlspecialchars( isset( $_GET[ 'name' ] ) ? $_GET[ 'name' ] : $event->name ); ?>" class="w-full border rounded px-3 py-2" required>
			</div>
			<div>
				<label for="description" class="block text-sm font-medium">Description</label>
				<!-- Textarea for event description, pre-filled with existing or GET data -->
				<textarea name="description" id="description" class="w-full border rounded px-3 py-2"><?php echo htmlspecialchars( isset( $_GET[ 'description' ] ) ? $_GET[ 'description' ] : $event->description ); ?></textarea>
			</div>
			<div>
				<label for="start" class="block text-sm font-medium">Start Date/Time</label>
				<!-- Input for start date/time, pre-filled with existing or GET data -->
				<input type="datetime-local" name="start" id="start" value="<?php echo htmlspecialchars( isset( $_GET[ 'start' ] ) ? $_GET[ 'start' ] : $event->start->format( 'Y-m-d\TH:i' ) ); ?>" class="w-full border rounded px-3 py-2" required>
			</div>
			<div>
				<label for="end" class="block text-sm font-medium">End Date/Time</label>
				<!-- Input for end date/time, pre-filled with existing or GET data -->
				<input type="datetime-local" name="end" id="end" value="<?php echo htmlspecialchars( isset( $_GET[ 'end' ] ) ? $_GET[ 'end' ] : $event->end->format( 'Y-m-d\TH:i' ) ); ?>" class="w-full border rounded px-3 py-2" required>
			</div>

			<!-- Submit button to save changes -->
			<button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Save Changes</button>
		</form>
	</body>

</html>