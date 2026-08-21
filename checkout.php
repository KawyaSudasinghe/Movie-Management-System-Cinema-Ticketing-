<?php

// Load the database connection and common helper functions.
require_once 'includes/database.php';
require_once 'includes/functions.php';

// Read booking details from the query string.
$movieId = positiveInt($_GET['movie_id'] ?? 0);
$quantity = positiveInt($_GET['quantity'] ?? 0);
$customerName = trim($_GET['customer_name'] ?? '');
$customerEmail = trim($_GET['customer_email'] ?? '');

// Validate the booking values on the server as well as in JavaScript.
if (
    $movieId < 1
    || $quantity < 1
    || $quantity > 10
    || $customerName === ''
    || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)
) {

    redirect('index.php');
}

// Retrieve the movie from the database.
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
    redirect('index.php');
}

// Calculate the total on the server.
$total = (float)$movie['ticket_price'] * $quantity;

$pageTitle = 'Payment | CineVault';

require_once 'includes/header.php';

?>

<section class="section">

    <div class="container checkout-container">

        <div class="section-heading">

            <div>

                <span class="eyebrow">CHECKOUT</span>

                <h1>Complete Your Purchase</h1>

            </div>

        </div>

        <div class="checkout-grid">

            <div class="checkout-card">

                <h2>Booking Details</h2>

                <div class="summary-row">

                    <span>Movie</span>

                    <strong><?= e($movie['title']) ?></strong>

                </div>

                <div class="summary-row">

                    <span>Customer</span>

                    <strong><?= e($customerName) ?></strong>

                </div>

                <div class="summary-row">

                    <span>Email</span>

                    <strong><?= e($customerEmail) ?></strong>

                </div>

                <div class="summary-row">

                    <span>Tickets</span>

                    <strong><?= $quantity ?></strong>

                </div>

                <div class="summary-row total-row">

                    <span>Total</span>

                    <strong><?= money($total) ?></strong>

                </div>

            </div>

            <div class="checkout-card">

                <h2>Payment</h2>

                <p class="muted">
                    This coursework demo uses a simulated payment
                    process. No external payment gateway or API is used.
                </p>

                <form
                    action="payment_success.php"
                    method="POST"
                    id="payment-form">

                    <input
                        type="hidden"
                        name="movie_id"
                        value="<?= (int)$movie['id'] ?>">

                    <input
                        type="hidden"
                        name="customer_name"
                        value="<?= e($customerName) ?>">

                    <input
                        type="hidden"
                        name="customer_email"
                        value="<?= e($customerEmail) ?>">

                    <input
                        type="hidden"
                        name="quantity"
                        value="<?= $quantity ?>">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e(csrf_token()) ?>">

                    <label>

                        <span>Cardholder Name</span>

                        <input
                            type="text"
                            name="card_name"
                            maxlength="100"
                            required
                            value="<?= e($customerName) ?>">

                    </label>

                    <label>

                        <span>Card Number</span>

                        <input
                            type="text"
                            name="card_number"
                            id="card-number"
                            inputmode="numeric"
                            maxlength="19"
                            placeholder="1111 2222 3333 4444"
                            required>

                    </label>

                    <div class="two-column">

                        <label>

                            <span>Expiry</span>

                            <input
                                type="text"
                                name="expiry"
                                id="expiry"
                                maxlength="5"
                                placeholder="MM/YY"
                                required>

                        </label>

                        <label>

                            <span>CVV</span>

                            <input
                                type="password"
                                name="cvv"
                                id="cvv"
                                inputmode="numeric"
                                maxlength="4"
                                required>

                        </label>

                    </div>

                    <button
                        class="button primary full"
                        type="submit">
                        Pay <?= money($total) ?>
                    </button>

                </form>

            </div>

        </div>

    </div>

</section>

<?php require_once 'includes/footer.php'; ?>