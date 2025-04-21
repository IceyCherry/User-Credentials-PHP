<?php

// Import and use classes
require_once 'Models/EventHandler.php'; // Include the EventHandler class file
use Models\EventHandler; // Use the EventHandler class from the Models namespace

// Handle Form Submission
if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) { // Check if the form was submitted via POST method
	$eventHandler = new EventHandler( $db, $_POST ); // Create a new instance of EventHandler with form data
	$eventHandler->handleRequest(); // Call the handleRequest method to process the form data
}

?>

<!-- New Event Form -->
<!DOCTYPE html>
<html>

	<head>
		<script src="https://cdn.tailwindcss.com"></script> <!-- Include Tailwind CSS for styling -->
		<title>New Event</title> <!-- Page title -->
	</head>

	<body class="container mx-auto p-4"> <!-- Main container with padding -->
		<h1 class="text-2xl font-bold mb-4">Create New Event</h1> <!-- Page heading -->
		<div class="mb-4">
			<a href="index.php" class="text-blue-500 hover:text-blue-700 inline-flex items-center"> <!-- Link to go back to all events -->
				<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> <!-- Back arrow icon -->
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
				</svg>
				Back to All Events
			</a>
		</div>
		<?php if ( isset( $_GET[ 'success' ] ) ) { ?> <!-- Check if there is a success message in the URL -->
			<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert"> <!-- Success message alert box -->
				<span class="block sm:inline"><?php echo htmlspecialchars( $_GET[ 'success' ] ); ?></span> <!-- Display the success message -->
			</div>
		<?php } ?>
		<?php if ( isset( $_GET[ 'error' ] ) ) { ?> <!-- Check if there is an error message in the URL -->
			<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert"> <!-- Error message alert box -->
				<span class="block sm:inline"><?php echo htmlspecialchars( $_GET[ 'error' ] ); ?></span> <!-- Display the error message -->
			</div>
		<?php } ?>
		<form action="new-event.php" method="POST" class="space-y-4"> <!-- Form Submits to Itself -->
			<div>
				<label for="name" class="block text-sm font-medium">Event Name</label> <!-- Label for event name -->
				<input type="text" name="name" id="name" class="w-full border rounded px-3 py-2" value="<?php echo isset( $_GET[ 'name' ] ) ? htmlspecialchars( $_GET[ 'name' ] ) : ''; ?>" required> <!-- Input for event name with pre-filled value if available -->
			</div>
			<div>
				<label for="description" class="block text-sm font-medium">Description</label> <!-- Label for event description -->
				<textarea name="description" id="description" class="w-full border rounded px-3 py-2"><?php echo isset( $_GET[ 'description' ] ) ? htmlspecialchars( $_GET[ 'description' ] ) : ''; ?></textarea> <!-- Textarea for event description with pre-filled value if available -->
			</div>
			<div>
				<label for="start" class="block text-sm font-medium">Start Date/Time</label> <!-- Label for event start date/time -->
				<input type="datetime-local" name="start" id="start" class="w-full border rounded px-3 py-2" value="<?php echo isset( $_GET[ 'start' ] ) ? htmlspecialchars( $_GET[ 'start' ] ) : ''; ?>" required> <!-- Input for event start date/time with pre-filled value if available -->
			</div>
			<div>
				<label for="end" class="block text-sm font-medium">End Date/Time</label> <!-- Label for event end date/time -->
				<input type="datetime-local" name="end" id="end" class="w-full border rounded px-3 py-2" value="<?php echo isset( $_GET[ 'end' ] ) ? htmlspecialchars( $_GET[ 'end' ] ) : ''; ?>" required> <!-- Input for event end date/time with pre-filled value if available -->
			</div>
			<button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Create Event</button> <!-- Submit button -->
		</form>
	</body>

</html>