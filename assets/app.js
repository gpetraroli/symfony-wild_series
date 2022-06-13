/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

const linkWatchlist = document.querySelector('#watchlist');

linkWatchlist.addEventListener('click', ev => {
    ev.preventDefault();
    const icon = linkWatchlist.querySelector('i');
    icon.classList.toggle('bi-heart');
    icon.classList.toggle('bi-heart-fill');

    fetch(linkWatchlist.href)
        .then(res => res.json())
        .then(data => {
            if (data.isInWatchlist) {
                icon.classList.add('bi-heart-fill');
                icon.classList.remove('bi-heart');
            } else {
                icon.classList.add('bi-heart');
                icon.classList.remove('bi-heart-fill');
            }
        });
});
