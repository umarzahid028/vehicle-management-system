/**
 * Vehicles Import Notification Handler
 *
 * This script listens for real-time notifications about new imported vehicles
 * and plays a sound when new or modified vehicles are detected.
 */

// Sound context and elements
let audioContext = null;
let audioBuffer = null;
let notificationSound = null;
let soundInitialized = false;

// Queue to hold pending notifications when sound isn't initialized yet
const pendingNotifications = [];

// Initialize audio - must be called from a user interaction
function initAudio() {
    if (soundInitialized) return;

    try {
        // Create audio context
        window.AudioContext = window.AudioContext || window.webkitAudioContext;
        audioContext = new AudioContext();

        // Load sound file
        const request = new XMLHttpRequest();
        request.open("GET", "/audio/notification.mp3", true);
        request.responseType = "arraybuffer";

        request.onload = function () {
            audioContext.decodeAudioData(
                request.response,
                function (buffer) {
                    audioBuffer = buffer;
                    console.log("Sound loaded successfully");
                    soundInitialized = true;

                    // Process any pending notifications
                    processPendingNotifications();
                },
                function (error) {
                    console.error("Error decoding audio data", error);
                }
            );
        };

        request.onerror = function () {
            console.error("Error loading sound file");
        };

        request.send();

        // Also create the fallback HTML5 audio element
        createHtmlAudioElement();

        console.log("Audio initialized");
    } catch (e) {
        console.error("Web Audio API not supported or error initializing", e);
        // Fall back to HTML5 audio
        createHtmlAudioElement();
    }
}

// Create HTML5 audio element as fallback
function createHtmlAudioElement() {
    if (document.getElementById("vehicle-import-notification-sound")) return;

    const sound = document.createElement("audio");
    sound.id = "vehicle-import-notification-sound";
    sound.preload = "auto";
    sound.src = "/audio/notification.mp3";
    sound.style.display = "none";
    document.body.appendChild(sound);
    notificationSound = sound;

    // Preload the sound
    sound.load();
}

// Process pending notifications
function processPendingNotifications() {
    if (pendingNotifications.length > 0) {
        console.log(
            `Processing ${pendingNotifications.length} pending notifications`
        );

        // Process each pending notification
        pendingNotifications.forEach((data) => {
            // Only play sound once
            playNotificationSound();

            // Show notification toast if available
            showNotification(data);
        });

        // Clear the queue
        pendingNotifications.length = 0;
    }
}

// Initialize Pusher if it's already loaded
function initializePusher() {
    if (typeof window.Echo !== "undefined") {
        console.log("Setting up vehicle import notification listener...");

        // Listen to the public channel
        window.Echo.channel("vehicles-imported").listen(
            ".vehicles.imported",
            (data) => {
                console.log("Vehicle import notification received:", data);
                handleNotification(data);
            }
        );

        // Also listen without the dot prefix to handle different event formats
        window.Echo.channel("vehicles-imported").listen(
            "vehicles.imported",
            (data) => {
                console.log(
                    "Vehicle import notification received (alt format):",
                    data
                );
                handleNotification(data);
            }
        );

        console.log("Vehicle import notification listener initialized");
    } else {
        console.error(
            "Echo is not defined. Make sure Laravel Echo is properly configured."
        );
    }
}

// Handle incoming notifications
function handleNotification(data) {
    if (soundInitialized) {
        // If audio is initialized, play sound and show notification
        playNotificationSound();
        showNotification(data);
    } else {
        // Otherwise, queue the notification
        pendingNotifications.push(data);
        console.log("Audio not initialized. Notification queued.");

        // Show the initialization button if it exists
        const soundInitContainer = document.getElementById(
            "sound-init-container"
        );
        if (soundInitContainer) {
            soundInitContainer.style.display = "flex";
        }
    }
}

// Play the notification sound using Web Audio API if available, fallback to HTML5 Audio
function playNotificationSound() {
    console.log("Attempting to play notification sound");

    // Try Web Audio API first
    if (audioContext && audioBuffer && soundInitialized) {
        try {
            // Make sure the context is running (may be suspended)
            if (audioContext.state === "suspended") {
                audioContext.resume();
            }

            // Create a new source for each play
            const source = audioContext.createBufferSource();
            source.buffer = audioBuffer;
            source.connect(audioContext.destination);
            source.start(0);
            console.log("Playing sound with Web Audio API");
            return;
        } catch (e) {
            console.warn(
                "Web Audio API playback failed, falling back to HTML5 Audio",
                e
            );
        }
    }

    // Fallback to HTML5 Audio
    if (notificationSound) {
        try {
            // Make sure audio context is running (needed for modern browsers)
            if (audioContext && audioContext.state === "suspended") {
                audioContext.resume();
            }

            // Stop and reset the sound if it's already playing
            notificationSound.pause();
            notificationSound.currentTime = 0;

            // Try to play the sound with a user interaction workaround
            const playPromise = notificationSound.play();

            // Handle autoplay restrictions
            if (playPromise !== undefined) {
                playPromise
                    .then(() => {
                        console.log("HTML5 Audio playback successful");
                    })
                    .catch((error) => {
                        console.warn("HTML5 Audio playback failed:", error);
                        // Try alternative method for sticky browsers
                        setTimeout(() => {
                            notificationSound
                                .play()
                                .catch((e) =>
                                    console.error("Second attempt failed:", e)
                                );
                        }, 100);
                    });
            }
        } catch (e) {
            console.error("All audio playback methods failed:", e);
        }
    } else {
        console.error("No audio element available for playback");
    }
}

// Show notification toast if SweetAlert2 or Toastr is available
function showNotification(data) {
    const message =
        data.message ||
        `Import complete: ${data.new_count} new vehicles, ${data.modified_count} modified vehicles`;

    console.log("Showing notification:", message);

    try {
        // Try using SweetAlert2 if available
        if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
            Swal.fire({
                title: "Vehicle Import Update",
                text: message,
                icon: "info",
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
            });
            return;
        }

        // Try using Toastr if available
        if (
            typeof toastr !== "undefined" &&
            typeof toastr.info === "function"
        ) {
            toastr.info(message, "Vehicle Import Update");
            return;
        }
    } catch (error) {
        console.error("Error showing notification with library:", error);
    }

    // Fallback to custom toast notification
    try {
        // Create a simple toast notification
        const toast = document.createElement("div");
        toast.style.position = "fixed";
        toast.style.top = "20px";
        toast.style.right = "20px";
        toast.style.backgroundColor = "#4CAF50";
        toast.style.color = "white";
        toast.style.padding = "16px";
        toast.style.borderRadius = "4px";
        toast.style.zIndex = "9999";
        toast.style.boxShadow = "0 2px 10px rgba(0,0,0,0.2)";

        toast.textContent = message;

        document.body.appendChild(toast);

        // Remove after 5 seconds
        setTimeout(() => {
            toast.style.opacity = "0";
            toast.style.transition = "opacity 0.5s";
            setTimeout(() => toast.remove(), 500);
        }, 5000);
    } catch (fallbackError) {
        console.error("All notification methods failed:", fallbackError);
    }
}

// Attempt to initialize audio on various user interactions
function setupUserInteractionHandlers() {
    // List of events that can be used to initialize audio
    const userEvents = ["click", "touchstart", "keydown", "mousedown"];

    // One-time initialization function
    const initOnUserAction = function () {
        initAudio();
        // Remove all event listeners after first interaction
        userEvents.forEach((event) => {
            document.removeEventListener(event, initOnUserAction);
        });
        console.log("Audio initialized from user interaction");
    };

    // Add event listeners
    userEvents.forEach((event) => {
        document.addEventListener(event, initOnUserAction);
    });
}

// Make sure we initialize when the DOM is loaded or when Echo becomes available
document.addEventListener("DOMContentLoaded", () => {
    // Set up audio initialization on user interaction
    setupUserInteractionHandlers();

    // Initialize pusher
    if (typeof window.Echo !== "undefined") {
        initializePusher();
    } else {
        // Wait for Echo to be defined
        const checkEchoInterval = setInterval(() => {
            if (typeof window.Echo !== "undefined") {
                initializePusher();
                clearInterval(checkEchoInterval);
            }
        }, 300);

        // Stop checking after 10 seconds
        setTimeout(() => clearInterval(checkEchoInterval), 10000);
    }
});

// Create a global method to test sound playback
window.testVehicleNotificationSound = function () {
    initAudio(); // Try to initialize in case it hasn't been done
    playNotificationSound();
    return "Testing sound...";
};

// Export functions for use in other parts of the application
export { playNotificationSound, showNotification, initAudio };
