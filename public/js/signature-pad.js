/**
 * Signature Pad JS Library for Laravel
 * -----------------------------------------
 * This script initializes signature pad instances on the page,
 * handles events for clearing and saving signatures,
 * and manages color and thickness pickers.
 */

document.addEventListener("DOMContentLoaded", function () {
    initializeSignaturePads();
});

/**
 * Initialize all signature pads on the page
 */
function initializeSignaturePads() {
    const containers = document.querySelectorAll(
        "[data-signature-pad-container]"
    );

    containers.forEach((container) => {
        const canvas = container.querySelector("canvas");
        const hiddenInput = container.querySelector('input[type="hidden"]');
        const clearButton = container.querySelector("[data-clear-button]");
        const saveButton = container.querySelector("[data-save-button]");
        const colorPicker = container.querySelector("[data-color-picker]");
        const thicknessPicker = container.querySelector(
            "[data-thickness-picker]"
        );
        const requiredMessage = container.querySelector(
            "[data-required-message]"
        );

        if (!canvas) return;

        // Set canvas height based on data attribute or default
        const height = canvas.getAttribute("data-height") || 200;
        canvas.height = height;

        // Initialize the signature pad
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: "rgba(255, 255, 255, 0)",
            penColor: canvas.getAttribute("data-default-color") || "#000000",
            minWidth: 0.5,
            maxWidth: 2.5,
            throttle: 16, // Increase for better performance but less accurate rendering
            velocityFilterWeight: 0.7,
        });

        // Resize canvas to fit parent container
        resizeCanvas(canvas, signaturePad);
        window.addEventListener("resize", () =>
            resizeCanvas(canvas, signaturePad)
        );

        // Load existing signature if available
        if (hiddenInput && hiddenInput.value) {
            loadExistingSignature(signaturePad, hiddenInput.value);
        }

        // Clear button event
        if (clearButton) {
            clearButton.addEventListener("click", function () {
                signaturePad.clear();
                if (hiddenInput) hiddenInput.value = "";
                if (requiredMessage) requiredMessage.classList.add("hidden");
            });
        }

        // Save button event
        if (saveButton) {
            saveButton.addEventListener("click", function () {
                if (signaturePad.isEmpty()) {
                    if (
                        requiredMessage &&
                        hiddenInput &&
                        hiddenInput.hasAttribute("required")
                    ) {
                        requiredMessage.classList.remove("hidden");
                    }
                    return;
                }

                const dataURL = signaturePad.toDataURL("image/png");

                if (hiddenInput) {
                    hiddenInput.value = dataURL;
                    if (requiredMessage)
                        requiredMessage.classList.add("hidden");
                }
            });
        }

        // Color picker event
        if (colorPicker) {
            colorPicker.addEventListener("change", function () {
                signaturePad.penColor = this.value;
            });
        }

        // Thickness picker event
        if (thicknessPicker) {
            thicknessPicker.addEventListener("input", function () {
                signaturePad.minWidth = parseFloat(this.value) * 0.5;
                signaturePad.maxWidth = parseFloat(this.value) * 2.5;
            });
        }

        // Handle form submission
        const form = canvas.closest("form");
        if (form && hiddenInput && hiddenInput.hasAttribute("required")) {
            form.addEventListener("submit", function (event) {
                if (signaturePad.isEmpty() && !hiddenInput.value) {
                    event.preventDefault();
                    if (requiredMessage)
                        requiredMessage.classList.remove("hidden");
                    canvas.scrollIntoView({
                        behavior: "smooth",
                        block: "center",
                    });
                }
            });
        }
    });
}

/**
 * Resize the canvas to fit the parent container
 * @param {HTMLCanvasElement} canvas - The canvas element
 * @param {SignaturePad} signaturePad - The signature pad instance
 */
function resizeCanvas(canvas, signaturePad) {
    // When zoomed out to less than 100%, for some browsers, the canvas is scaled larger
    // than actual size. To fix this, we need to get the device pixel ratio and scale the canvas accordingly.
    const ratio = Math.max(window.devicePixelRatio || 1, 1);

    // Calculate new dimensions
    const width = canvas.offsetWidth * ratio;
    const height = canvas.height * ratio;

    // Scale the canvas and redraw the signature
    canvas.width = width;
    canvas.height = height;
    canvas.getContext("2d").scale(ratio, ratio);
    signaturePad.clear(); // Clears the canvas but not the hidden input value
}

/**
 * Load an existing signature from a data URL
 * @param {SignaturePad} signaturePad - The signature pad instance
 * @param {string} dataUrl - The data URL of the signature
 */
function loadExistingSignature(signaturePad, dataUrl) {
    if (!dataUrl) return;

    const image = new Image();
    image.onload = function () {
        const canvas = signaturePad._canvas;
        const ctx = canvas.getContext("2d");

        // Clear the canvas first
        signaturePad.clear();

        // Calculate the position to center the image
        const xScale = canvas.width / image.width;
        const yScale = canvas.height / image.height;
        const scale = Math.min(xScale, yScale);

        const x = (canvas.width - image.width * scale) / 2;
        const y = (canvas.height - image.height * scale) / 2;

        // Draw the image
        ctx.drawImage(image, x, y, image.width * scale, image.height * scale);

        // Need to call this to update the internal data
        signaturePad._isEmpty = false;
    };

    image.src = dataUrl;
}
