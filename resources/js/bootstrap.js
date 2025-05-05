import axios from "axios";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
window.Pusher = Pusher;

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Add CSRF token to all requests
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
} else {
    console.error("CSRF token not found");
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? "ap2",
    wsHost: import.meta.env.VITE_PUSHER_HOST
        ? import.meta.env.VITE_PUSHER_HOST
        : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS: true,
    encrypted: true,
    enabledTransports: ["ws", "wss"],
    auth: {
        headers: {
            "X-CSRF-TOKEN": token ? token.content : "",
            Accept: "application/json",
        },
    },
});
