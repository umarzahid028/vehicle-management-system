import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

// Import notification components and make them available globally
import { NotificationListener } from "./components/NotificationListener.js";
import * as VehicleImportNotification from "./vehicles-import-notification.js";

// Import signature pad initialization
import "./signature-pad-init.js";

// Make NotificationListener available globally
window.NotificationListener = NotificationListener;

// Make VehicleImportNotification available globally
window.VehicleImportNotification = VehicleImportNotification;

// Add a helper for testing if Laravel Echo is properly initialized
window.testEchoConnection = function () {
    if (!window.Echo) {
        console.error("Echo is not initialized");
        return false;
    }

    try {
        // Test by subscribing to a simple public channel
        const testChannel = window.Echo.channel("test-channel");
        console.log("Echo connection test passed");
        return true;
    } catch (error) {
        console.error("Echo connection test failed:", error);
        return false;
    }
};

// Add a diagnostic function for sound playback
window.diagnoseSoundIssues = function () {
    console.group("🔊 Sound Diagnostic Report");

    // Check if audio context exists and what state it's in
    console.log(
        "AudioContext available:",
        !!(window.AudioContext || window.webkitAudioContext)
    );

    // Check if notification sound can be loaded
    const testAudio = new Audio("/audio/notification.mp3");
    testAudio.addEventListener("canplaythrough", () => {
        console.log("✅ Notification sound file can be loaded");
    });
    testAudio.addEventListener("error", () => {
        console.error("❌ Notification sound file failed to load");
    });

    // Check notification initialization
    console.log(
        "NotificationListener available:",
        !!window.NotificationListener
    );
    console.log(
        "NotificationListener instance:",
        !!window.notificationListenerInstance
    );

    // Check Echo connection
    console.log(
        "Echo connection status:",
        window.testEchoConnection() ? "Connected" : "Not connected"
    );

    // Check if Notification permissions are granted
    if (window.Notification) {
        console.log("Notification permission:", Notification.permission);
    } else {
        console.log("Browser Notifications not supported");
    }

    // Check VehicleImportNotification module
    console.log(
        "VehicleImportNotification available:",
        !!window.VehicleImportNotification
    );
    if (window.VehicleImportNotification) {
        console.log(
            "initAudio function:",
            !!window.VehicleImportNotification.initAudio
        );
        console.log(
            "playNotificationSound function:",
            !!window.VehicleImportNotification.playNotificationSound
        );
    }

    console.groupEnd();

    return "Diagnostic complete. Check console for results.";
};

// Test notification sound on demand (can be called from console)
window.playTestSound = function () {
    try {
        // Try each sound method in sequence
        if (
            window.VehicleImportNotification &&
            window.VehicleImportNotification.playNotificationSound
        ) {
            window.VehicleImportNotification.playNotificationSound();
            return "Using VehicleImportNotification to play sound";
        }

        if (window.notificationListenerInstance) {
            window.notificationListenerInstance.playNotificationSound();
            return "Using NotificationListener instance to play sound";
        }

        // Direct audio playback
        const audio = new Audio("/audio/notification.mp3");
        audio.volume = 0.5;
        audio.play();
        return "Using direct Audio API";
    } catch (error) {
        console.error("Error playing test sound:", error);
        return "Failed to play sound. See console for details.";
    }
};
