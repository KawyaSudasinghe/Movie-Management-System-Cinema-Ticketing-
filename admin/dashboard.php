<?php

// Load the database connection and common helper functions.
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Only authenticated managers can access this page.
requireLogin();

// Retrieve the movie catalog for the CRUD table.
$movieStmt = $conn->query(
    'SELECT *
     FROM movies
     ORDER BY id DESC'
);

$movies = $movieStmt->fetchAll();

// Retrieve all bookings for the booking-management view.
$bookingStmt = $conn->query(
    'SELECT
        b.*,
        m.title
     FROM bookings b
     INNER JOIN movies m ON m.id = b.movie_id
     ORDER BY b.created_at DESC'
);

$bookings = $bookingStmt->fetchAll();

// Calculate dashboard statistics.
$totalMovies = count($movies);
$totalBookings = count($bookings);
$totalTickets = array_sum(array_column($bookings, 'quantity'));
$totalRevenue = array_sum(array_column($bookings, 'total_amount'));

$pageTitle = 'Admin Dashboard | CineVault';

$rootPath = '../';

require_once '../includes/header.php';

?>

<section class="section admin-section">

    <div class="container">

        <div class="admin-topbar">

            <div>

                <span class="eyebrow">MANAGER DASHBOARD</span>

                <h1>Welcome, <?= e($_SESSION['admin_username']) ?></h1>

            </div>

            <a
                class="button outline"
                href="logout.php"
            >
                Logout
            </a>

        </div>

        <div class="stats-grid">

            <div class="stat-card">

                <span>Movies</span>

                <strong><?= $totalMovies ?></strong>

            </div>

            <div class="stat-card">

                <span>Bookings</span>

                <strong><?= $totalBookings ?></strong>

            </div>

            <div class="stat-card">

                <span>Tickets Sold</span>

                <strong><?= $totalTickets ?></strong>

            </div>

            <div class="stat-card">

                <span>Revenue</span>

                <strong><?= money($totalRevenue) ?></strong>

            </div>

        </div>

        <section class="admin-panel">

            <div class="panel-heading">

                <div>

                    <span class="eyebrow">CRUD</span>

                    <h2>Movie Management</h2>

                </div>

                <button
                    class="button primary"
                    type="button"
                    id="open-add-movie"
                >
                    + Add Movie
                </button>

            </div>

            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>

                            <th>Movie</th>
                            <th>Genre</th>
                            <th>Runtime</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody id="movie-admin-table">

                        <?php foreach ($movies as $movie): ?>

                            <tr data-movie-id="<?= (int)$movie['id'] ?>">

                                <td>

                                    <strong>
                                        <?= e($movie['title']) ?>
                                    </strong>

                                </td>

                                <td><?= e($movie['genre']) ?></td>

                                <td><?= e($movie['runtime']) ?> min</td>

                                <td><?= money($movie['ticket_price']) ?></td>

                                <td>
                                    <span class="table-status">
                                        <?= e($movie['status']) ?>
                                    </span>
                                </td>

                                <td class="actions">

                                    <button
                                        class="small-button edit-movie"
                                        type="button"
                                        data-id="<?= (int)$movie['id'] ?>"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        class="small-button danger delete-movie"
                                        type="button"
                                        data-id="<?= (int)$movie['id'] ?>"
                                    >
                                        Delete
                                    </button>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </section>

        <section class="admin-panel">

            <div class="panel-heading">

                <div>

                    <span class="eyebrow">BOOKING MANAGEMENT</span>

                    <h2>Customer Ticket Purchases</h2>

                </div>

            </div>

            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>

                            <th>Reference</th>
                            <th>Customer</th>
                            <th>Movie</th>
                            <th>Tickets</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Date</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (!$bookings): ?>

                            <tr>

                                <td colspan="7">
                                    No bookings yet.
                                </td>

                            </tr>

                        <?php endif; ?>

                        <?php foreach ($bookings as $booking): ?>

                            <tr>

                                <td>
                                    <?= e($booking['booking_reference']) ?>
                                </td>

                                <td>

                                    <strong>
                                        <?= e($booking['customer_name']) ?>
                                    </strong>

                                    <small>
                                        <?= e($booking['customer_email']) ?>
                                    </small>

                                </td>

                                <td><?= e($booking['title']) ?></td>

                                <td><?= (int)$booking['quantity'] ?></td>

                                <td><?= money($booking['total_amount']) ?></td>

                                <td>
                                    <span class="payment-status">
                                        <?= e($booking['payment_status']) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= e($booking['created_at']) ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </div>

</section>

<div
    class="modal hidden"
    id="movie-modal"
>

    <div class="modal-card">

        <button
            class="modal-close"
            type="button"
            id="close-movie-modal"
        >
            &times;
        </button>

        <span class="eyebrow">MOVIE FORM</span>

        <h2 id="movie-modal-title">Add Movie</h2>

        <form id="movie-form">

            <input
                type="hidden"
                id="movie-id"
                name="id"
                value=""
            >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e(csrf_token()) ?>"
            >

            <label>

                <span>Title</span>

                <input
                    type="text"
                    id="movie-title"
                    name="title"
                    required
                    maxlength="150"
                >

            </label>

            <div class="two-column">

                <label>

                    <span>Genre</span>

                    <input
                        type="text"
                        id="movie-genre"
                        name="genre"
                        required
                        maxlength="80"
                    >

                </label>

                <label>

                    <span>Runtime (minutes)</span>

                    <input
                        type="number"
                        id="movie-runtime"
                        name="runtime"
                        required
                        min="1"
                        max="500"
                    >

                </label>

            </div>

            <label>

                <span>Ticket Price (LKR)</span>

                <input
                    type="number"
                    id="movie-price"
                    name="ticket_price"
                    required
                    min="0"
                    step="0.01"
                >

            </label>

            <label>

                <span>Poster URL</span>

                <input
                    type="url"
                    id="movie-poster"
                    name="poster_url"
                    required
                >

            </label>

            <label>

                <span>Description</span>

                <textarea
                    id="movie-description"
                    name="description"
                    required
                    rows="4"
                ></textarea>

            </label>

            <label>

                <span>Status</span>

                <select
                    id="movie-status"
                    name="status"
                >

                    <option value="Showing">
                        Showing
                    </option>

                    <option value="Ended">
                        Ended
                    </option>

                </select>

            </label>

            <button
                class="button primary full"
                type="submit"
            >
                Save Movie
            </button>

        </form>

        <div
            id="movie-form-message"
            class="form-message"
        ></div>

    </div>

</div>

<script>

window.csrfToken = <?= json_encode(csrf_token()) ?>;

</script>

<script src="../js/admin.js"></script>

<?php require_once '../includes/footer.php'; ?>
