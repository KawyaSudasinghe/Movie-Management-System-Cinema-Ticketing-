// Wait until the page has loaded before using its elements.
document.addEventListener('DOMContentLoaded', function () {

    // Find the catalog controls used on the homepage.
    const searchInput = document.querySelector('#movie-search');
    const genreFilter = document.querySelector('#genre-filter');
    const movieCards = document.querySelectorAll('.movie-card');
    const noResults = document.querySelector('#no-results');
    const movieCount = document.querySelector('#movie-count');

    // Filter the movie cards using the search text and genre.
    function filterMovies() {

        const searchText = (searchInput?.value || '').toLowerCase().trim();
        const selectedGenre = genreFilter?.value || 'all';

        let visibleCount = 0;

        movieCards.forEach(function (card) {

            const title = card.dataset.title || '';
            const genre = card.dataset.genre || '';

            const titleMatches = title.includes(searchText);

            const genreMatches =
                selectedGenre === 'all'
                || genre === selectedGenre;

            if (titleMatches && genreMatches) {

                card.classList.remove('hidden');
                visibleCount++;

            } else {

                card.classList.add('hidden');

            }

        });

        if (movieCount) {
            movieCount.textContent =
                visibleCount + (visibleCount === 1 ? ' movie' : ' movies');
        }

        if (noResults) {

            noResults.classList.toggle(
                'hidden',
                visibleCount !== 0
            );

        }

    }

    // Update the catalog immediately whenever a filter changes.
    searchInput?.addEventListener('input', filterMovies);
    genreFilter?.addEventListener('change', filterMovies);

    // Update the booking total without reloading the page.
    const quantityInput = document.querySelector('#ticket-quantity');
    const totalElement = document.querySelector('#live-total');

    if (quantityInput && totalElement && typeof window.movieTicketPrice !== 'undefined') {

        function updateBookingTotal() {

            let quantity = parseInt(quantityInput.value, 10);

            if (Number.isNaN(quantity) || quantity < 1) {
                quantity = 1;
            }

            if (quantity > 10) {
                quantity = 10;
            }

            quantityInput.value = quantity;

            const total =
                window.movieTicketPrice * quantity;

            totalElement.textContent =
                'LKR ' + total.toLocaleString(
                    'en-LK',
                    {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }
                );

        }

        quantityInput.addEventListener(
            'input',
            updateBookingTotal
        );

        updateBookingTotal();

    }

    // Validate the ticket booking form before it is submitted.
    const bookingForm = document.querySelector('#booking-form');

    bookingForm?.addEventListener('submit', function (event) {

        const quantity =
            parseInt(quantityInput?.value || '0', 10);

        if (quantity < 1 || quantity > 10) {

            event.preventDefault();

            alert('Please select between 1 and 10 tickets.');

        }

    });

    // Format the card number into groups of four digits.
    const cardNumber = document.querySelector('#card-number');

    cardNumber?.addEventListener('input', function () {

        const digits =
            this.value.replace(/\D/g, '').slice(0, 16);

        this.value =
            digits.replace(/(.{4})/g, '$1 ').trim();

    });

    // Format the expiry date as MM/YY.
    const expiry = document.querySelector('#expiry');

    expiry?.addEventListener('input', function () {

        const digits =
            this.value.replace(/\D/g, '').slice(0, 4);

        if (digits.length > 2) {

            this.value =
                digits.slice(0, 2) + '/' + digits.slice(2);

        } else {

            this.value = digits;

        }

    });

    // Allow only digits in the CVV field.
    const cvv = document.querySelector('#cvv');

    cvv?.addEventListener('input', function () {

        this.value =
            this.value.replace(/\D/g, '').slice(0, 4);

    });

    // Validate the simulated payment form.
    const paymentForm = document.querySelector('#payment-form');

    paymentForm?.addEventListener('submit', function (event) {

        const cardDigits =
            cardNumber.value.replace(/\D/g, '');

        const expiryValue =
            expiry.value.trim();

        const cvvValue =
            cvv.value.trim();

        if (cardDigits.length !== 16) {

            event.preventDefault();

            alert('Please enter a 16-digit card number.');

            cardNumber.focus();

            return;

        }

        if (!/^\d{2}\/\d{2}$/.test(expiryValue)) {

            event.preventDefault();

            alert('Please enter expiry as MM/YY.');

            expiry.focus();

            return;

        }

        if (!/^\d{3,4}$/.test(cvvValue)) {

            event.preventDefault();

            alert('Please enter a valid CVV.');

            cvv.focus();

        }

    });

});
