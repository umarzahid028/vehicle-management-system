import Pusher from "pusher-js";
import Swal from "sweetalert2";

export class NotificationListener {
    constructor(userId) {
        this.userId = userId;
        this.notificationCount = 0;
        this.vehicleCount = 0;

        // Check if SweetAlert2 is available
        this.swalAvailable = typeof Swal !== "undefined";
        if (!this.swalAvailable) {
            console.warn(
                "SweetAlert2 is not available. Will use alternative notification method."
            );
        }

        this.initialize();
    }

    initialize() {
        if (!window.Echo) {
            try {
                const Pusher = window.Pusher || require("pusher-js");
                const Echo = require("laravel-echo").default;

                window.Echo = new Echo({
                    broadcaster: "pusher",
                    key: import.meta.env.VITE_PUSHER_APP_KEY,
                    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
                    forceTLS: true,
                });

                console.log("Echo initialized in NotificationListener");
            } catch (error) {
                console.error(
                    "Failed to initialize Echo in NotificationListener:",
                    error
                );
                // Instead of failing silently, show a message to the developer
                setTimeout(() => {
                    console.warn(
                        "PUSHER CONFIG ISSUE: Make sure your .env has the required VITE_PUSHER_* variables set"
                    );
                }, 1000);
                return; // Exit the initialization to prevent further errors
            }
        }

        try {
            window.Echo.private(`users.${this.userId}`).notification(
                (notification) => {
                    console.log("Received notification:", notification);
                    this.showNotification(notification);
                    this.updateUI(notification);
                }
            );
        } catch (error) {
            console.error("Error subscribing to the private channel:", error);
        }

        // Initialize existing counts
        this.notificationCount = parseInt(
            document.getElementById("notification-counter")?.textContent || "0"
        );
        this.vehicleCount = parseInt(
            document.getElementById("vehicle-notification-counter")
                ?.textContent || "0"
        );
    }

    showNotification(notification) {
        try {
            console.log("Showing notification:", notification);

            // Extract data - handle both direct notification objects and Laravel notification format
            const data = notification.data || notification;

            // Check if SweetAlert2 is available and handle accordingly
            if (
                this.swalAvailable &&
                typeof Swal !== "undefined" &&
                typeof Swal.fire === "function"
            ) {
                // Get notification config
                const swalConfig = this.createNotification(data);

                // Directly use Swal.fire with the config object
                Swal.fire(swalConfig);
            } else {
                // Fallback to browser notification or custom alert
                this.showFallbackNotification(data);
            }
        } catch (error) {
            console.error("Error showing notification:", error);
            // Silently recover and try alternate method
            try {
                this.showFallbackNotification(
                    notification.data || notification
                );
            } catch (fallbackError) {
                console.error(
                    "Fallback notification also failed:",
                    fallbackError
                );
            }
        }
    }

    showFallbackNotification(data) {
        try {
            // Simple fallback using browser notification or alert
            if (window.Notification && Notification.permission === "granted") {
                new Notification(data.title || "New Notification", {
                    body: data.message || "You have a new notification",
                    icon: "/favicon.ico",
                });
            } else {
                // Create a simple toast notification element
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

                const message = data.message || "You have a new notification";
                toast.textContent = message;

                document.body.appendChild(toast);

                // Remove after 3 seconds
                setTimeout(() => {
                    toast.style.opacity = "0";
                    toast.style.transition = "opacity 0.5s";
                    setTimeout(() => toast.remove(), 500);
                }, 3000);
            }
        } catch (error) {
            console.error("Error in fallback notification:", error);
        }
    }

    createNotification(data) {
        let title = "New Notification";
        let icon = "info";
        let message = "You have a new notification";

        // Extract the right message from the notification
        if (data.message) {
            message = data.message;
        } else if (data.data && data.data.message) {
            message = data.data.message;
        }

        // Get the notification type - handle both formats
        const type = data.type || (data.data && data.data.type);

        // Customize notification based on type
        if (
            type &&
            (type.includes("NewVehicleArrival") ||
                type.includes("NewVehicleImported"))
        ) {
            title = "New Vehicle";
            icon = "info";
        }

        // Return a configuration object for Swal.fire()
        return {
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            icon: icon,
            title: title,
            text: message,
            didOpen: (toast) => {
                if (typeof Swal !== "undefined") {
                    toast.addEventListener("mouseenter", Swal.stopTimer);
                    toast.addEventListener("mouseleave", Swal.resumeTimer);
                }
            },
        };
    }

    updateUI(data) {
        try {
            // Increment counters
            this.notificationCount++;
            if (
                data.type &&
                (data.type.includes("NewVehicleArrival") ||
                    data.type.includes("NewVehicleImported"))
            ) {
                this.vehicleCount++;
                this.playNotificationSound();
            }

            // Update notification counters in the UI - safely
            this.updateNotificationCounter();
            this.updateHeaderCounter();
            this.updateVehicleCounter();

            // Add notification to the dropdown list
            this.addNotificationToList(data);
        } catch (error) {
            console.error("Error updating UI:", error);
        }
    }

    playNotificationSound() {
        try {
            console.log("Attempting to play notification sound");

            // First try using our imported sound player if available
            if (
                window.VehicleImportNotification &&
                window.VehicleImportNotification.playNotificationSound
            ) {
                console.log("Using VehicleImportNotification to play sound");
                // Call initAudio before playing to ensure context is initialized
                if (window.VehicleImportNotification.initAudio) {
                    window.VehicleImportNotification.initAudio();
                }
                window.VehicleImportNotification.playNotificationSound();

                // Also try web audio API as backup - this is important
                this.tryWebAudioPlay();
                return;
            }

            // Fallback to direct audio playback
            this.tryHTMLAudioPlay();
        } catch (error) {
            console.error("Error playing notification sound:", error);
            // Final fallback - show the sound initialization button
            this.showSoundInitButton();
        }
    }

    tryHTMLAudioPlay() {
        // Fallback to direct audio playback
        const audio = new Audio("/audio/notification.mp3");
        audio.volume = 0.5;
        audio
            .play()
            .then(() => console.log("Sound played successfully"))
            .catch((e) => {
                console.error("Sound playback error:", e);
                // Try with Web Audio API as a last resort
                this.tryWebAudioPlay();
            });
    }

    tryWebAudioPlay() {
        try {
            if (window.AudioContext || window.webkitAudioContext) {
                const context = new (window.AudioContext ||
                    window.webkitAudioContext)();
                const source = context.createBufferSource();

                fetch("/audio/notification.mp3")
                    .then((response) => response.arrayBuffer())
                    .then((arrayBuffer) => context.decodeAudioData(arrayBuffer))
                    .then((audioBuffer) => {
                        source.buffer = audioBuffer;
                        source.connect(context.destination);
                        source.start(0);
                        console.log("Sound played with Web Audio API");
                    })
                    .catch((err) => {
                        console.error("Web Audio API error:", err);
                        this.showSoundInitButton();
                    });
            }
        } catch (webAudioError) {
            console.error("All sound playback methods failed", webAudioError);
            this.showSoundInitButton();
        }
    }

    showSoundInitButton() {
        // Show the sound initialization button
        const soundInitContainer = document.getElementById(
            "sound-init-container"
        );
        if (soundInitContainer) {
            soundInitContainer.style.display = "flex";
        }
    }

    updateNotificationCounter() {
        const counter = document.getElementById("notification-counter");
        if (counter) {
            counter.textContent = this.notificationCount;
            counter.classList.remove("hidden");
        }
    }

    updateHeaderCounter() {
        const headerCounter = document.getElementById(
            "header-notification-counter"
        );
        if (headerCounter) {
            headerCounter.textContent = this.notificationCount;
            headerCounter.classList.remove("hidden");
        }
    }

    updateVehicleCounter() {
        const vehicleCounter = document.getElementById(
            "vehicle-notification-counter"
        );
        if (vehicleCounter) {
            vehicleCounter.textContent = this.vehicleCount;
            vehicleCounter.classList.remove("hidden");
        }
    }

    addNotificationToList(data) {
        const dropdownList = document.getElementById("notification-list");
        if (!dropdownList) return;

        // Create the notification item
        const notificationItem = document.createElement("a");
        notificationItem.href = data.url || "#";
        notificationItem.className =
            "block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100";

        // Use the message from data, or create a default message
        notificationItem.textContent =
            data.message ||
            (data.data && data.data.message) ||
            "New notification";

        // Insert at the top of the list
        if (dropdownList.firstChild) {
            dropdownList.insertBefore(
                notificationItem,
                dropdownList.firstChild
            );
        } else {
            dropdownList.appendChild(notificationItem);
        }
    }
}
