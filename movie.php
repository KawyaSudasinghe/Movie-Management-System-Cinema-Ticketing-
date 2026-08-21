<?php

// Load the database connection and common helper functions.
require_once 'includes/database.php';
require_once 'includes/functions.php';

// Read the selected movie ID from the URL.
$movieId = positiveInt($_GET['movie_id'] ?? 0);

// Retrieve the selected currently showing movie.
$stmt = $conn->prepare(
    'SELECT *
     FROM movies
     WHERE id = :id
     AND status = "Showing"
     LIMIT 1'
);

$stmt->execute(['id' => $movieId]);

$movie = $stmt->fetch();

// Show a friendly message when the movie does not exist.
if (!$movie) {

    http_response_code(404);

    $pageTitle = 'Movie Not Found';

    require_once 'includes/header.php';

    ?>

    <section class="section">

        <div class="container">

            <div class="empty-state">

                <h1>Movie not found</h1>

                <p>
                    The selected movie is not currently showing.
                </p>

                <a
                    class="button primary"
                    href="index.php"
                >
                    Back to Movies
                </a>

            </div>

        </div>

    </section>

    <?php

    require_once 'includes/footer.php';

    exit;

}

$pageTitle = $movie['title'] . ' | CineVault';

require_once 'includes/header.php';

?>

<section class="section">

    <div class="container">

        <a
            class="back-link"
            href="index.php"
        >
            &larr; Back to Movies
        </a>

        <div class="movie-detail">

            <div class="detail-poster">

                <img
                    src="<?= e($movie['poster_url']) ?>"
                    alt="<?= e($movie['title']) ?> poster"
                    onerror="this.classList.add('poster-error');"
                >

                <span class="poster-fallback">
                    <?= e($movie['title']) ?>
                </span>

            </div>

            <div class="detail-content">

                <span class="eyebrow">
                    <?= e($movie['genre']) ?>
                </span>

                <h1><?= e($movie['title']) ?></h1>

                <p class="lead">
                    <?= e($movie['description']) ?>
                </p>

                <div class="detail-meta">

                    <div>
                        <span>Runtime</span>
                        <strong><?= e($movie['runtime']) ?> minutes</strong>
                    </div>

                    <div>
                        <span>Ticket Price</span>
                        <strong><?= money($movie['ticket_price']) ?></strong>
                    </div>

                    <div>
                        <span>Status</span>
                        <strong><?= e($movie['status']) ?></strong>
                    </div>

                </div>

                <form
                    class="booking-form"
                    action="checkout.php"
                    method="GET"
                    id="booking-form"
                >

                    <input
                        type="hidden"
                        name="movie_id"
                        value="<?= (int)$movie['id'] ?>"
                    >

                    <label>

                        <span>Your Name</span>

                        <input
                            type="text"
                            name="customer_name"
                            required
                            maxlength="100"
                            placeholder="Enter your name"
                        >

                    </label>

                    <label>

                        <span>Email Address</span>

                        <input
                            type="email"
                            name="customer_email"
                            required
                            maxlength="150"
                            placeholder="Enter your email"
                        >

                    </label>

                    <label>

                        <span>Number of Tickets</span>

                        <input
                            type="number"
                            name="quantity"
                            id="ticket-quantity"
                            min="1"
                            max="10"
                            value="1"
                            required
                        >

                    </label>

                    <div class="booking-total">

                        <span>Total</span>

                        <strong id="live-total">
                            <?= money($movie['ticket_price']) ?>
                        </strong>

                    </div>

                    <button
                        class="button primary full"
                        type="submit"
                    >
                        Continue to Payment
                    </button>

                </form>

            </div>

        </div>

    </div>

</section>

<script>

// Store the movie ticket price for the live JavaScript calculation.
window.movieTicketPrice = <?= json_encode((float)$movie['ticket_price']) ?>;

</script>

<?php require_once 'includes/footer.php'; ?>
