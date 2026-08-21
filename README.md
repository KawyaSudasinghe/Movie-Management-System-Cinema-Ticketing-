# CineVault Movie Management System

## Project basis

This project follows the supplied University of Kelaniya SENG 21253 Web Application Development practical specification.

The specification requires:

- Customer movie catalog viewing.
- Movie title search and genre filtering.
- Ticket quantity selection and checkout.
- Administrator authentication.
- Movie CRUD operations.
- Booking management.
- HTML, CSS, Vanilla JavaScript, PHP and MySQL.
- No front-end or back-end frameworks.

The project does not use Bootstrap, Tailwind, React, Laravel, or another framework.

## Folder setup in XAMPP

1. Install XAMPP.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Copy the `movie-management-system` folder into:

   `C:\xampp\htdocs\`

4. Open phpMyAdmin:

   `http://localhost/phpmyadmin/`

5. Create/import the database by importing:

   `database/movie_management.sql`

6. Open the website:

   `http://localhost/movie-management-system/`

## Database settings

The project is already configured for:

- Host: `localhost`
- Database: `movie_management`
- Username: `root`
- Password: empty

These settings are in:

`includes/database.php`

If your MySQL root account has a password, change the `$password` value there.

## Administrator login

- Username: `admin`
- Password: `admin123`

Open:

`http://localhost/movie-management-system/admin/login.php`

## Customer flow

1. Open the movie catalog.
2. Search by title or filter by genre.
3. Select a movie.
4. Enter customer name and email.
5. Select 1 to 10 tickets.
6. Continue to payment.
7. Enter demo payment details.
8. The system records the booking with `Completed` payment status.
9. A booking reference is displayed.

No external payment API or real payment gateway is included.

## Administrator flow

1. Log in.
2. View movie statistics.
3. Add movies.
4. Edit movie details.
5. Delete movies that have no bookings.
6. Movies that already have bookings are safely marked as `Ended` instead of being deleted.
7. View incoming customer bookings, ticket quantities and payment statuses.

## JavaScript functionality

Vanilla JavaScript is used for:

- Movie title searching.
- Genre filtering.
- Live ticket total calculation.
- Booking quantity validation.
- Card number formatting.
- Expiry date formatting.
- CVV input validation.
- Payment form validation.
- Admin add movie modal.
- Admin movie editing through AJAX.
- Admin movie deletion through AJAX.
- Admin CRUD communication with PHP.

## Important note about poster images

The seeded movies use direct TMDB image-hosting URLs for related movie poster images. The application does not call a movie API.

If an external poster is temporarily unavailable, the CSS displays the movie title as a fallback.

## Security-related implementation

- PHP sessions are used for administrator authentication.
- Administrator passwords are stored as password hashes.
- PDO prepared statements are used for database queries.
- CSRF tokens are used for administrator AJAX operations and checkout.
- Server-side validation is performed even when JavaScript validation exists.
- Booking prices are calculated from the database rather than trusting a browser total.

## Main database tables

### admins

Stores administrator login credentials.

### movies

Stores movie title, genre, runtime, ticket price, poster URL, description and showing status.

### bookings

Stores booking reference, customer details, selected movie, ticket quantity, total amount and payment status.

## Presentation demonstration

A simple demonstration can be:

1. Log in as Admin.
2. Add a new movie.
3. Return to the customer catalog.
4. Show the new movie.
5. Book multiple tickets.
6. Complete the simulated payment.
7. Return to the Admin dashboard.
8. Show the booking, quantity and completed payment status.

