<?php

// Load the database connection and common helper functions.
require_once 'includes/database.php';
require_once 'includes/functions.php';

// Accept the checkout information submitted by the payment form.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

// Check the CSRF token before creating a booking.
if (!verify_csrf($_POST['csrf_token'] ?? '')) {
    die('Invalid request. Please return to the movie page and try again.');
}

$movieId = positiveInt($_POST['movie_id'] ?? 0);
$quantity = positiveInt($_POST['quantity'] ?? 0);
$customerName = trim($_POST['customer_name'] ?? '');
$customerEmail = trim($_POST['customer_email'] ?? '');

// Validate the submitted booking data.
if (
    $movieId < 1
    || $quantity < 1
    || $quantity > 10
    || $customerName === ''
    || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)
) {

    die('Invalid booking information.');
}

// Retrieve the movie and its current ticket price.
$stmt = $conn->prepare(
    'SELECT *
     FROM movies
     WHERE id = :id
     AND status = "Showing"
     LIMIT 1'
);

$stmt->execute(['id' => $movieId]);

$movie = $stmt->fetch();

if (!$movie) {
    die('The selected movie is no longer available.');
}

// Calculate the final amount using the database price.
$total = (float)$movie['ticket_price'] * $quantity;

// Generate a simple human-readable booking reference.
$bookingReference = 'CV-' . date('YmdHis') . '-' . random_int(100, 999);

// Save the booking with a completed payment status.
$insert = $conn->prepare(
    'INSERT INTO bookings
        (booking_reference, movie_id, customer_name, customer_email,
         quantity, total_amount, payment_status)
     VALUES
        (:booking_reference, :movie_id, :customer_name, :customer_email,
         :quantity, :total_amount, "Completed")'
);

$insert->execute([
    'booking_reference' => $bookingReference,
    'movie_id' => $movieId,
    'customer_name' => $customerName,
    'customer_email' => $customerEmail,
    'quantity' => $quantity,
    'total_amount' => $total
]);

$pageTitle = 'Payment Successful | CineVault';

require_once 'includes/header.php';

?>

<section class="section success-section">

    <div class="container">

        <div class="success-card">

            <div class="success-icon">
                ✓
            </div>

            <span class="eyebrow">PAYMENT SUCCESSFUL</span>

            <h1>Booking Confirmed!</h1>

            <p>
                Your cinema ticket purchase has been recorded successfully.
            </p>

            <div class="ticket-receipt">

                <div class="summary-row">

                    <span>Booking Reference</span>

                    <strong><?= e($bookingReference) ?></strong>

                </div>

                <div class="summary-row">

                    <span>Movie</span>

                    <strong><?= e($movie['title']) ?></strong>

                </div>

                <div class="summary-row">

                    <span>Customer</span>

                    <strong><?= e($customerName) ?></strong>

                </div>

                <div class="summary-row">

                    <span>Tickets</span>

                    <strong><?= $quantity ?></strong>

                </div>

                <div class="summary-row total-row">

                    <span>Paid</span>

                    <strong><?= money($total) ?></strong>

                </div>

            </div>

            <a
                class="button primary"
                href="index.php">
                Back to Movie Catalog
            </a>

        </div>

    </div>

</section>

<?php require_once 'includes/footer.php'; ?>