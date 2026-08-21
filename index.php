<?php

// Load the database connection and common helper functions.
require_once 'includes/database.php';
require_once 'includes/functions.php';

// Retrieve all currently showing movies from MySQL.
$stmt = $conn->query(
    'SELECT *
     FROM movies
     WHERE status = "Showing"
     ORDER BY created_at DESC'
);

$movies = $stmt->fetchAll();

// Create a list of genres for the filter.
$genreStmt = $conn->query(
    'SELECT DISTINCT genre
     FROM movies
     WHERE status = "Showing"
     ORDER BY genre'
);

$genres = $genreStmt->fetchAll();

$pageTitle = 'CineVault | Movies';

require_once 'includes/header.php';

?>

<section class="hero">

    <div class="container hero-content">

        <div>

            <span class="eyebrow">NOW SHOWING</span>

            <h1>
                Your next movie night
                <span>starts here.</span>
            </h1>

            <p>
                Browse current movies, choose your tickets,
                and complete your cinema booking in a few clicks.
            </p>

            <a
                class="button primary"
                href="#movie-catalog"
            >
                Browse Movies
            </a>

        </div>

        <div class="hero-poster">

            <div class="hero-poster-content">

                <span>FEATURED</span>

                <strong>INCEPTION</strong>

                <small>
                    A dream within a dream.
                </small>

            </div>

        </div>

    </div>

</section>

<section
    id="movie-catalog"
    class="section"
>

    <div class="container">

        <div class="section-heading">

            <div>

                <span class="eyebrow">MOVIE CATALOG</span>

                <h2>Currently Showing</h2>

            </div>

            <p id="movie-count">
                <?= count($movies) ?> movies
            </p>

        </div>

        <div class="filters">

            <label class="search-box">

                <span>Search</span>

                <input
                    type="text"
                    id="movie-search"
                    placeholder="Search by movie title..."
                    autocomplete="off"
                >

            </label>

            <label>

                <span>Genre</span>

                <select id="genre-filter">

                    <option value="all">
                        All Genres
                    </option>

                    <?php foreach ($genres as $genre): ?>

                        <option value="<?= e($genre['genre']) ?>">
                            <?= e($genre['genre']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </label>

        </div>

        <div
            class="movie-grid"
            id="movie-grid"
        >

            <?php foreach ($movies as $movie): ?>

                <article
                    class="movie-card"
                    data-title="<?= e(strtolower($movie['title'])) ?>"
                    data-genre="<?= e($movie['genre']) ?>"
                >

                    <div class="poster-wrap">

                        <img
                            src="<?= e($movie['poster_url']) ?>"
                            alt="<?= e($movie['title']) ?> poster"
                            loading="lazy"
                            onerror="this.classList.add('poster-error');"
                        >

                        <span class="poster-fallback">
                            <?= e($movie['title']) ?>
                        </span>

                        <span class="status-badge">
                            <?= e($movie['status']) ?>
                        </span>

                    </div>

                    <div class="movie-card-body">

                        <span class="movie-genre">
                            <?= e($movie['genre']) ?>
                        </span>

                        <h3><?= e($movie['title']) ?></h3>

                        <div class="movie-meta">

                            <span>
                                <?= e($movie['runtime']) ?> min
                            </span>

                            <span>
                                <?= money($movie['ticket_price']) ?>
                            </span>

                        </div>

                        <a
                            class="button full"
                            href="movie.php?movie_id=<?= (int)$movie['id'] ?>"
                        >
                            View &amp; Book
                        </a>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

        <div
            id="no-results"
            class="empty-state hidden"
        >
            <h3>No movies found</h3>
            <p>
                Try another title or select a different genre.
            </p>
        </div>

    </div>

</section>

<?php require_once 'includes/footer.php'; ?>
