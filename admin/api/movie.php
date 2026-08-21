<?php

// Load the database connection and common helper functions.
require_once '../../includes/database.php';
require_once '../../includes/functions.php';

// Return JSON for all AJAX requests from the admin dashboard.
header('Content-Type: application/json; charset=utf-8');

// Only authenticated administrators may use this endpoint.
if (empty($_SESSION['admin_id'])) {

    http_response_code(401);

    echo json_encode([
        'success' => false,
        'message' => 'Authentication required.'
    ]);

    exit;

}

// Read JSON data sent by JavaScript.
$input = json_decode(file_get_contents('php://input'), true) ?? [];

if (!verify_csrf($input['csrf_token'] ?? '')) {

    http_response_code(403);

    echo json_encode([
        'success' => false,
        'message' => 'Invalid security token.'
    ]);

    exit;

}

$action = $input['action'] ?? '';

// Handle create and update operations.
if ($action === 'save') {

    $id = positiveInt($input['id'] ?? 0);
    $title = trim($input['title'] ?? '');
    $genre = trim($input['genre'] ?? '');
    $runtime = positiveInt($input['runtime'] ?? 0);
    $ticketPrice = (float)($input['ticket_price'] ?? 0);
    $posterUrl = trim($input['poster_url'] ?? '');
    $description = trim($input['description'] ?? '');
    $status = $input['status'] ?? 'Showing';

    // Validate the submitted movie data.
    if (
        $title === ''
        || $genre === ''
        || $runtime < 1
        || $ticketPrice < 0
        || $posterUrl === ''
        || $description === ''
        || !in_array($status, ['Showing', 'Ended'], true)
    ) {

        http_response_code(422);

        echo json_encode([
            'success' => false,
            'message' => 'Please enter valid movie information.'
        ]);

        exit;

    }

    if ($id > 0) {

        // Update an existing movie.
        $stmt = $conn->prepare(
            'UPDATE movies
             SET title = :title,
                 genre = :genre,
                 runtime = :runtime,
                 ticket_price = :ticket_price,
                 poster_url = :poster_url,
                 description = :description,
                 status = :status
             WHERE id = :id'
        );

        $stmt->execute([
            'title' => $title,
            'genre' => $genre,
            'runtime' => $runtime,
            'ticket_price' => $ticketPrice,
            'poster_url' => $posterUrl,
            'description' => $description,
            'status' => $status,
            'id' => $id
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Movie updated successfully.'
        ]);

    } else {

        // Insert a new movie.
        $stmt = $conn->prepare(
            'INSERT INTO movies
                (title, genre, runtime, ticket_price, poster_url,
                 description, status)
             VALUES
                (:title, :genre, :runtime, :ticket_price, :poster_url,
                 :description, :status)'
        );

        $stmt->execute([
            'title' => $title,
            'genre' => $genre,
            'runtime' => $runtime,
            'ticket_price' => $ticketPrice,
            'poster_url' => $posterUrl,
            'description' => $description,
            'status' => $status
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Movie added successfully.'
        ]);

    }

    exit;

}

// Handle movie retrieval for the edit form.
if ($action === 'get') {

    $id = positiveInt($input['id'] ?? 0);

    $stmt = $conn->prepare(
        'SELECT *
         FROM movies
         WHERE id = :id
         LIMIT 1'
    );

    $stmt->execute(['id' => $id]);

    $movie = $stmt->fetch();

    if (!$movie) {

        http_response_code(404);

        echo json_encode([
            'success' => false,
            'message' => 'Movie not found.'
        ]);

        exit;

    }

    echo json_encode([
        'success' => true,
        'movie' => $movie
    ]);

    exit;

}

// Handle deletion of a movie.
if ($action === 'delete') {

    $id = positiveInt($input['id'] ?? 0);

    if ($id < 1) {

        http_response_code(422);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid movie ID.'
        ]);

        exit;

    }

    // Check whether the movie has bookings before deleting it.
    $check = $conn->prepare(
        'SELECT COUNT(*)
         FROM bookings
         WHERE movie_id = :id'
    );

    $check->execute(['id' => $id]);

    if ((int)$check->fetchColumn() > 0) {

        // Preserve booking history by marking the movie as ended.
        $stmt = $conn->prepare(
            'UPDATE movies
             SET status = "Ended"
             WHERE id = :id'
        );

        $stmt->execute(['id' => $id]);

        echo json_encode([
            'success' => true,
            'message' => 'Movie has bookings, so it was marked as Ended.'
        ]);

        exit;

    }

    // Delete movies that do not have booking records.
    $stmt = $conn->prepare(
        'DELETE FROM movies
         WHERE id = :id'
    );

    $stmt->execute(['id' => $id]);

    echo json_encode([
        'success' => true,
        'message' => 'Movie deleted successfully.'
    ]);

    exit;

}

http_response_code(400);

echo json_encode([
    'success' => false,
    'message' => 'Unknown action.'
]);

?>
