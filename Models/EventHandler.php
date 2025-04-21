<?php

namespace Models;
require_once 'Utils/Redirect.php';
use Utils\Redirect;
use \DateTime;
/**
 * Class EventHandler
 * Handles the creation, editing, and deletion of events.
 * 
 * @package Models
 */
class EventHandler
{
    private $postData;
    private $db;
    private $eventFile = 'events.txt';

    public function __construct($db, $postData)
    {
        $this->db = $db;
        $this->postData = $postData;
    }

	/**
	 * Handles the incoming request based on the request method.
	 */
	public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($this->postData['id'])) {
                $this->editEvent();
            } else {
                $this->addEvent();
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            if ($userId) {
                $this->deleteEvent($_GET['id'], $userId);
            } else {
                Redirect::to('index.php', [
                    'error' => 'You must be logged in to delete an event.',
                ]);
            }
        }
    }
	

	/**
	 * Adds a new event.
	 * Validates the input data and appends the event to the events file.
	 */
	private function addEvent()
    {
        $name = trim($this->postData['name']);
        $description = trim(isset($this->postData['description']) ? $this->postData['description'] : '');
        $start = trim($this->postData['start']);
        $end = trim($this->postData['end']);
        $authorId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        // if (empty($name) || empty($start) || empty($end) || !$authorId) {
        //     Redirect::to('new-event.php', [
        //         'error' => "Required fields are missing.",
        //         'name' => $name,
        //         'description' => $description,
        //         'start' => $start,
        //         'end' => $end,
        //     ]);
        // }

        $startDate = $this->validateDate($start);
        $endDate = $this->validateDate($end);

        if (!$startDate || !$endDate || $startDate >= $endDate) {
            Redirect::to('new-event.php', [
                'error' => "Start and end dates are invalid or end date is before start date.",
                'name' => $name,
                'description' => $description,
                'start' => $start,
                'end' => $end,
            ]);
        }

        $id = uniqid();
        $name = str_replace('|', '\|', $name);
        $description = str_replace('|', '\|', $description);

        // Append to file with authorId included
        $line = "$id|$name|$description|{$startDate->format('Y-m-d H:i:s')}|{$endDate->format('Y-m-d H:i:s')}|$authorId\n";
        file_put_contents($this->eventFile, $line, FILE_APPEND | LOCK_EX);

        Redirect::to('index.php', [
            'success' => "Event '$name' Successfully Created",
        ]);
    }

	/**
	 * Edits an existing event.
	 * Validates the input data and updates the event in the events file.
	 */
	private function editEvent()
	{
		$id = htmlspecialchars(trim($this->postData['id']));
		$name = htmlspecialchars(trim($this->postData['name']));
		$description = htmlspecialchars(trim(isset($this->postData['description']) ? $this->postData['description'] : ''));
		$start = htmlspecialchars(trim($this->postData['start']));
		$end = htmlspecialchars(trim($this->postData['end']));

		// Validate required fields and dates
		// if (empty($id) || empty($name) || empty($start) || empty($end)) {
		// 	Redirect::to('edit-event.php', [
		// 		'error' => "Required fields are missing.",
		// 		'id' => $id,
		// 		'name' => $name,
		// 		'description' => $description,
		// 		'start' => $start,
		// 		'end' => $end,
		// 	]);
		// }

		$startDate = $this->validateDate($start);
		$endDate = $this->validateDate($end);

		if (!$startDate || !$endDate || $startDate >= $endDate) {
			Redirect::to('edit-event.php', [
				'error' => "Start and end dates are invalid or end date is before start date.",
				'id' => $id,
				'name' => $name,
				'description' => $description,
				'start' => $start,
				'end' => $end,
			]);
		}

		$name = str_replace('|', '\|', $name);
		$description = str_replace('|', '\|', $description);

		// Read events from file and update the specific event
		$events = file($this->eventFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$updatedEvents = [];

		foreach ($events as $line) {
			list($lineID) = explode('|', $line);
			if ($lineID === $id) {
				$line = "$id|$name|$description|{$startDate->format('Y-m-d H:i:s')}|{$endDate->format('Y-m-d H:i:s')}";
			}
			$updatedEvents[] = $line;
		}

		// Write updated events back to file
		file_put_contents($this->eventFile, implode("\n", $updatedEvents) . "\n");

		// Redirect with success message
		Redirect::to('index.php', [
			'success' => "Event '$name' Successfully Edited",
		]);
	}

	/**
	 * Deletes an existing event.
	 * Validates the event ID and removes the event from the events file.
	 */
	public function deleteEvent($eventId, $userId)
	{
		if (!$eventId) {
			error_log("deleteEvent: Failed - Missing event ID");
			return false;
		}
	
		$events = file($this->eventFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if ($events === false) {
			error_log("deleteEvent: Failed - Could not read file: " . $this->eventFile);
			return false;
		}
		error_log("Event file contents: " . implode(", ", $events));
	
		$updatedEvents = [];
		$eventFound = false;
	
		foreach ($events as $line) {
			list($id, $name, $creatorId) = explode('|', $line, 3);
			error_log("Checking: $id vs $eventId");
			if ($id === (string)$eventId) {
				$eventFound = true;
				error_log("Found event: $line");
				continue;
			}
			$updatedEvents[] = $line;
		}
	
		if (!$eventFound) {
			error_log("deleteEvent: Failed - Event $eventId not found");
			return false;
		}
	
		if (file_put_contents($this->eventFile, implode("\n", $updatedEvents) . "\n") === false) {
			error_log("deleteEvent: Failed - Write failed");
			return false;
		}
	
		error_log("deleteEvent: Success - Event $eventId deleted");
		return true;
	}

	/**
	 * Validates a date string against multiple formats.
	 * @param string $date The date string to validate.
	 * @return DateTime|false The DateTime object if valid, false otherwise.
	 */
	private function validateDate($date)
	{
		$dateFormats = ['Y-m-d\TH:i', 'Y-m-d H:i:s', 'd-m-Y H:i:s', 'd/m/Y H:i:s'];
		foreach ($dateFormats as $format) {
			$dateTime = DateTime::createFromFormat($format, $date);
			if ($dateTime) {
				return $dateTime;
			}
		}
		return false;
	}
	// Method to check if the event was created by the user
	// public function isEventCreatedByUser($eventId, $userId)
	// {
	// 	// Query to check if the event's creator is the same as the logged-in user
	// 	// This assumes you have an "events" table with a "created_by" field, and that the user ID is stored there.

	// 	// Example: Assuming a database connection is available in this class
	// 	$sql = "SELECT created_by FROM events WHERE event_id = :event_id LIMIT 1";

	// 	$stmt = $this->db->prepare($sql);
	// 	$stmt->bindParam(':event_id', $eventId);
	// 	$stmt->execute();

	// 	$event = $stmt->fetch();

	// 	// Check if the event was created by the given user
	// 	return $event && $event['created_by'] === $userId;
	// }




}