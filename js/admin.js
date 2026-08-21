// Wait for the admin dashboard HTML before attaching event handlers.
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.querySelector('#movie-modal');
    const form = document.querySelector('#movie-form');
    const message = document.querySelector('#movie-form-message');

    const modalTitle = document.querySelector('#movie-modal-title');

    const movieId = document.querySelector('#movie-id');
    const movieTitle = document.querySelector('#movie-title');
    const movieGenre = document.querySelector('#movie-genre');
    const movieRuntime = document.querySelector('#movie-runtime');
    const moviePrice = document.querySelector('#movie-price');
    const moviePoster = document.querySelector('#movie-poster');
    const movieDescription = document.querySelector('#movie-description');
    const movieStatus = document.querySelector('#movie-status');

    // Open the blank form for a new movie.
    document.querySelector('#open-add-movie')
        ?.addEventListener('click', function () {

            form.reset();

            movieId.value = '';

            modalTitle.textContent = 'Add Movie';

            message.textContent = '';

            modal.classList.remove('hidden');

        });

    // Close the movie modal.
    document.querySelector('#close-movie-modal')
        ?.addEventListener('click', closeModal);

    // Close the modal when the dark background is clicked.
    modal?.addEventListener('click', function (event) {

        if (event.target === modal) {
            closeModal();
        }

    });

    function closeModal() {

        modal?.classList.add('hidden');

    }

    // Send an AJAX request to the PHP movie endpoint.
    async function sendMovieRequest(data) {

        const response = await fetch(
            'api/movie.php',
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            }
        );

        const result = await response.json();

        if (!response.ok || !result.success) {

            throw new Error(
                result.message || 'Request failed.'
            );

        }

        return result;

    }

    // Load a movie into the form when Edit is clicked.
    document.querySelectorAll('.edit-movie')
        .forEach(function (button) {

            button.addEventListener('click', async function () {

                try {

                    const result =
                        await sendMovieRequest({
                            action: 'get',
                            id: this.dataset.id,
                            csrf_token: window.csrfToken
                        });

                    const movie = result.movie;

                    movieId.value = movie.id;
                    movieTitle.value = movie.title;
                    movieGenre.value = movie.genre;
                    movieRuntime.value = movie.runtime;
                    moviePrice.value = movie.ticket_price;
                    moviePoster.value = movie.poster_url;
                    movieDescription.value = movie.description;
                    movieStatus.value = movie.status;

                    modalTitle.textContent = 'Edit Movie';

                    message.textContent = '';

                    modal.classList.remove('hidden');

                } catch (error) {

                    alert(error.message);

                }

            });

        });

    // Save a movie using the AJAX PHP endpoint.
    form?.addEventListener('submit', async function (event) {

        event.preventDefault();

        const formData =
            new FormData(form);

        const data =
            Object.fromEntries(formData.entries());

        data.action = 'save';

        message.textContent = 'Saving...';

        try {

            const result =
                await sendMovieRequest(data);

            message.textContent =
                result.message;

            setTimeout(function () {
                window.location.reload();
            }, 700);

        } catch (error) {

            message.textContent =
                error.message;

        }

    });

    // Delete a movie or mark it as ended through AJAX.
    document.querySelectorAll('.delete-movie')
        .forEach(function (button) {

            button.addEventListener('click', async function () {

                const confirmed =
                    confirm(
                        'Delete this movie? If it has bookings, it will be marked as Ended.'
                    );

                if (!confirmed) {
                    return;
                }

                try {

                    const result =
                        await sendMovieRequest({
                            action: 'delete',
                            id: this.dataset.id,
                            csrf_token: window.csrfToken
                        });

                    alert(result.message);

                    window.location.reload();

                } catch (error) {

                    alert(error.message);

                }

            });

        });

});
