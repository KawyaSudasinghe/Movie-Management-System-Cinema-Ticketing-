-- Create the database used by the Movie Management System.
CREATE DATABASE IF NOT EXISTS movie_management
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE movie_management;

-- Remove old tables when importing this file again during development.
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS movies;
DROP TABLE IF EXISTS admins;

-- Store cinema manager login details.
CREATE TABLE admins (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    username VARCHAR(50) NOT NULL UNIQUE,

    password_hash VARCHAR(255) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB;

-- Store the movie catalog.
CREATE TABLE movies (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(150) NOT NULL,

    genre VARCHAR(80) NOT NULL,

    runtime INT UNSIGNED NOT NULL,

    ticket_price DECIMAL(10, 2) NOT NULL,

    poster_url VARCHAR(500) NOT NULL,

    description TEXT NOT NULL,

    status ENUM('Showing', 'Ended') NOT NULL DEFAULT 'Showing',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB;

-- Store customer ticket purchases.
CREATE TABLE bookings (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    booking_reference VARCHAR(50) NOT NULL UNIQUE,

    movie_id INT UNSIGNED NOT NULL,

    customer_name VARCHAR(100) NOT NULL,

    customer_email VARCHAR(150) NOT NULL,

    quantity INT UNSIGNED NOT NULL,

    total_amount DECIMAL(10, 2) NOT NULL,

    payment_status ENUM('Pending', 'Completed', 'Failed')
        NOT NULL DEFAULT 'Pending',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_bookings_movie
        FOREIGN KEY (movie_id)
        REFERENCES movies(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT

) ENGINE=InnoDB;

-- Create the default administrator.
-- Username: admin
-- Password: admin123
INSERT INTO admins
    (username, password_hash)
VALUES
    (
        'admin',
        '$2y$12$6a3/Nj5r.JHNCI4CDmuokOwA.YbSeU5Lcjl5X7vj68YzSTcuCUyB6'
    );

-- Insert 15 real movies with related movie poster images.
INSERT INTO movies
    (title, genre, runtime, ticket_price, poster_url, description, status)
VALUES

(
    'Inception',
    'Sci-Fi',
    148,
    1200.00,
    'https://image.tmdb.org/t/p/w500/9gk7adHYeDvHkCSEqAvQNLV5Uge.jpg',
    'A skilled thief who steals secrets through shared dreams is offered a chance to erase his past by performing an impossible inception.',
    'Showing'
),

(
    'Interstellar',
    'Sci-Fi',
    169,
    1300.00,
    'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
    'Explorers travel through a wormhole in space in search of a new home for humanity.',
    'Showing'
),

(
    'The Dark Knight',
    'Action',
    152,
    1250.00,
    'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg',
    'Batman faces a criminal mastermind whose actions push Gotham City and its heroes toward chaos.',
    'Showing'
),

(
    'Avengers: Endgame',
    'Action',
    181,
    1400.00,
    'https://image.tmdb.org/t/p/w500/or06FN3Dka5tukK1e9sl16pB3iy.jpg',
    'The surviving Avengers gather for one final mission to reverse the devastating events that changed the universe.',
    'Showing'
),

(
    'Spider-Man: No Way Home',
    'Action',
    148,
    1300.00,
    'https://image.tmdb.org/t/p/w500/1g0dhYtq4irTY1GPXvft6k4YLjm.jpg',
    'Spider-Man asks for help after his identity is exposed, but a dangerous spell opens the door to unexpected visitors.',
    'Showing'
),

(
    'Titanic',
    'Romance',
    194,
    1100.00,
    'https://image.tmdb.org/t/p/w500/9xjZS2rlVxm8SFx8kPC3aIGCOYQ.jpg',
    'A young couple from different social backgrounds meet aboard the legendary RMS Titanic.',
    'Showing'
),

(
    'Avatar',
    'Sci-Fi',
    162,
    1350.00,
    'https://image.tmdb.org/t/p/w500/kyeqWdyUXW608qlYkRqosgbbJyK.jpg',
    'A former marine becomes part of the world of Pandora while caught between competing human and Na’vi interests.',
    'Showing'
),

(
    'Oppenheimer',
    'Drama',
    181,
    1450.00,
    'https://image.tmdb.org/t/p/w500/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg',
    'A dramatic portrait of the scientist whose work helped develop the first atomic bomb.',
    'Showing'
),

(
    'Parasite',
    'Thriller',
    132,
    1150.00,
    'https://image.tmdb.org/t/p/w500/7IiTTgloJzvGI1TAYymCfbfl3vT.jpg',
    'A struggling family gradually becomes entangled with a wealthy household in a tense story of class and ambition.',
    'Showing'
),

(
    'Joker',
    'Drama',
    122,
    1200.00,
    'https://image.tmdb.org/t/p/w500/udDclJoHjfjb8Ekgsd4FDteOkCU.jpg',
    'A troubled man living on the margins of society begins a transformation that changes Gotham City.',
    'Showing'
),

(
    'Frozen',
    'Animation',
    102,
    1000.00,
    'https://image.tmdb.org/t/p/w500/mINJaa34MtknCYl5AjtNJzWj8cD.jpg',
    'A fearless young woman sets out to find her sister and bring summer back to a kingdom trapped in eternal winter.',
    'Showing'
),

(
    'Toy Story',
    'Animation',
    81,
    950.00,
    'https://image.tmdb.org/t/p/w500/uXDfjJbdP4ijW5hWSBrPrlKpxab.jpg',
    'A group of toys comes to life when humans are not around, led by the friendship between Woody and Buzz.',
    'Showing'
),

(
    'The Matrix',
    'Sci-Fi',
    136,
    1150.00,
    'https://image.tmdb.org/t/p/w500/f89U3ADr1oiB1s9GkdPOEpXUk5H.jpg',
    'A computer hacker discovers that the reality he knows is an artificial world and joins a rebellion against its creators.',
    'Showing'
),

(
    'Dune',
    'Sci-Fi',
    155,
    1350.00,
    'https://image.tmdb.org/t/p/w500/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg',
    'A gifted young nobleman must travel to a dangerous desert world and face a destiny greater than he imagined.',
    'Showing'
),

(
    'Inside Out',
    'Animation',
    95,
    1050.00,
    'https://image.tmdb.org/t/p/w500/2H1TmgdfNtsKlU9jKdeNyYL5y8T.jpg',
    'Inside a young girl’s mind, five emotions work together as she adjusts to a major change in her life.',
    'Showing'
);

-- Helpful indexes for common searches.
CREATE INDEX idx_movies_status
    ON movies(status);

CREATE INDEX idx_movies_genre
    ON movies(genre);

CREATE INDEX idx_bookings_movie
    ON bookings(movie_id);

CREATE INDEX idx_bookings_created
    ON bookings(created_at);

