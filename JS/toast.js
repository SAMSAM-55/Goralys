// This file is part of the "Toast" feature for displaying notifications.
// It handles the display of toast notifications based on URL parameters.

let toast_element = null

// Function to add the toast message to the screen
// @param title : The title of the toast message
// @param message : The message of the toast
// @param type : The type of the toast message (success, error, warning, info)
function add_toast(toast_element, title, message, type, duration, is_new) {
    toast_element.className = `feedback-container ${type}`;
    toast_element.querySelector('.toast-image').getElementsByClassName('fas')[0].className =
        `fas ${type === 'success' ? 'fa-circle-check' :
               type === 'error' ?   'fa-circle-xmark' : 
               type === 'warning' ? 'fa-circle-exclamation' : 
               type === 'info' ?    'fa-circle-info': ''}`
    toast_element.querySelector('.toast-content').innerHTML = `
    <p class="toast-title">${title}</p>
    <p class="feedback-message">${message}</p>
    `;

    // Show the toast
    toast_element.style.display = 'flex';

    if (is_new) {
        setTimeout(() => {
            toast_element.classList.add('new');
            toast_element.classList.add('show');
        }, 10); // Small delay to allow the browser to render the element
    } else {
        setTimeout(() => {
            toast_element.classList.add('old');
        }, 10); // Small delay to allow the browser to render the element
    }

    // Hide the toast after 3 seconds
    setTimeout(() => {
        toast_element.classList.remove('show');
        toast_element.classList.add('hide');

        // Remove the toast from view after the fade-out transition
        setTimeout(() => {
            toast_element.style.display = 'none';
            toast_element.classList.remove('hide');
        }, 750);// Match the CSS transition duration

        sessionStorage.removeItem('toast');
        sessionStorage.removeItem('toast-type');
        sessionStorage.removeItem('toast-title');
        sessionStorage.removeItem('toast-message');
        sessionStorage.removeItem('toast-expires');
    }, duration);

    sessionStorage.setItem('toast', 'true');
    sessionStorage.setItem('toast-type', type);
    sessionStorage.setItem('toast-title', title)
    sessionStorage.setItem('toast-message', message);
    sessionStorage.setItem('toast-expires', (Date.now() + duration).toString());
    console.log("Test", duration);
}

// Function to show a toast message
// @param title : The title of the toast message
// @param message : The message of the toast
// @param type : The type of the toast message (success, error, warning, info)
export function show_toast(title, message, type = 'warning', duration = 3500, is_new = true) {
    const toast = document.getElementById('toast');

    if (toast.classList.contains('show')) {
        toast.classList.remove('show');
        toast.classList.add('hide');

        setTimeout(() => {
            toast.style.display = 'none';
            toast.classList.remove('hide');
            add_toast(toast, title, message, type, duration, is_new);
        }, 750);
    } else {
        add_toast(toast, title, message, type, duration, is_new);
    }
}

// This script listens for the DOMContentLoaded event and checks for URL parameters to display a toast notification.
// If the 'toast' parameter is set to 'true', it retrieves the toast title, message, and type from the URL parameters and displays the toast notification.
addEventListener("DOMContentLoaded", () => {
    toast_element = document.getElementById("toast")
    const url_params = new URLSearchParams(window.location.search);

    if (url_params.get('toast') === 'true') {
        const toast_title = url_params.get('toast-title');
        const toast_message = url_params.get('toast-message');
        const toast_type = url_params.get('toast-type');

        const cleanUrl = window.location.origin + window.location.pathname;
        window.history.replaceState({}, document.title, cleanUrl);

        show_toast(toast_title, toast_message, toast_type);
    } else if (sessionStorage.getItem('toast') === 'true') {

        const toast_type = sessionStorage.getItem('toast-type');
        const toast_title = sessionStorage.getItem('toast-title');
        const toast_message = sessionStorage.getItem('toast-message');
        const toast_expires = parseInt(sessionStorage.getItem('toast-expires'));
        const remaining_duration = toast_expires - Date.now();

        if (remaining_duration > 0) {
            show_toast(toast_title, toast_message, toast_type, remaining_duration, false);
        } else {
            sessionStorage.removeItem('toast');
            sessionStorage.removeItem('toast-type');
            sessionStorage.removeItem('toast-title');
            sessionStorage.removeItem('toast-message');
            sessionStorage.removeItem('toast-expires');
        }
    }
});