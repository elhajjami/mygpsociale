/**
 * Bootstrap file for Laravel Breeze
 * Sets up CSRF token handling for AJAX requests
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios = {
        defaults: {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token.content
            }
        }
    };
}
