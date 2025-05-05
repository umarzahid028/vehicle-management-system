/**
 * Signature Pad Component
 *
 * A reusable component for capturing user signatures
 * Can be included in any form that requires signature capture
 */

import SignaturePad from "signature_pad";

export default class SignaturePadComponent {
    /**
     * Initialize a new signature pad component
     *
     * @param {Object} options - Configuration options
     * @param {string} options.canvasSelector - CSS selector for the canvas element
     * @param {string} options.clearButtonSelector - CSS selector for clear button
     * @param {string} options.saveButtonSelector - CSS selector for save button
     * @param {string} options.inputSelector - CSS selector for the hidden input that will store signature data
     * @param {string} options.containerSelector - CSS selector for the container element
     * @param {string} options.formSelector - CSS selector for the form element
     * @param {Object} options.padOptions - Options for the SignaturePad instance
     * @param {boolean} options.showToolbar - Whether to show the toolbar for pen options
     */
    constructor(options) {
        this.options = {
            canvasSelector: "#signature-pad",
            clearButtonSelector: "#clearButton",
            saveButtonSelector: "#saveButton",
            inputSelector: "#signature",
            containerSelector: ".signature-pad-container",
            formSelector: "#form",
            padOptions: {},
            showToolbar: false,
            ...options,
        };

        this.canvas = document.querySelector(this.options.canvasSelector);
        this.clearButton = document.querySelector(
            this.options.clearButtonSelector
        );
        this.saveButton = document.querySelector(
            this.options.saveButtonSelector
        );
        this.hiddenInput = document.querySelector(this.options.inputSelector);
        this.container = document.querySelector(this.options.containerSelector);
        this.form = document.querySelector(this.options.formSelector);

        if (!this.canvas) {
            console.error("Canvas element not found");
            return;
        }

        // Disable save button initially
        if (this.saveButton) {
            this.saveButton.disabled = true;
            this.saveButton.classList.add("opacity-50", "cursor-not-allowed");
        }

        this.resizeCanvas();
        this.signaturePad = new SignaturePad(this.canvas, {
            backgroundColor: "rgb(255, 255, 255)",
            penColor: "rgb(0, 0, 100)",
            velocityFilterWeight: 0.7,
            ...this.options.padOptions,
        });

        this.init();

        if (this.options.showToolbar) {
            this.setupToolbar();
        }
    }

    /**
     * Initialize the component
     */
    init() {
        // Handle window resize
        window.addEventListener("resize", this.resizeCanvas.bind(this));

        // Handle clear button click
        if (this.clearButton) {
            this.clearButton.addEventListener(
                "click",
                this.handleClear.bind(this)
            );
        }

        // Handle canvas events
        this.canvas.addEventListener(
            "mousedown",
            this.handleBeginStroke.bind(this)
        );
        this.canvas.addEventListener(
            "touchstart",
            this.handleBeginStroke.bind(this),
            { passive: true }
        );

        // Handle form submission
        if (this.form) {
            this.form.addEventListener("submit", this.handleSubmit.bind(this));
        }
    }

    /**
     * Resize canvas to maintain proper dimensions
     */
    resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const containerWidth = this.canvas.offsetWidth;

        this.canvas.width = containerWidth * ratio;
        this.canvas.height = 240 * ratio; // Fixed height

        this.canvas.getContext("2d").scale(ratio, ratio);

        // Clear the canvas when resizing
        this.signaturePad.clear();
    }

    /**
     * Handle beginning a stroke
     */
    handleBeginStroke() {
        // Enable save button when drawing starts
        if (this.saveButton && this.saveButton.disabled) {
            setTimeout(() => {
                if (!this.signaturePad.isEmpty()) {
                    this.saveButton.disabled = false;
                    this.saveButton.classList.remove(
                        "opacity-50",
                        "cursor-not-allowed"
                    );
                }
            }, 100);
        }
    }

    /**
     * Handle clearing the signature pad
     */
    handleClear() {
        this.signaturePad.clear();

        // Clear hidden input
        if (this.hiddenInput) {
            this.hiddenInput.value = "";
        }

        // Disable save button
        if (this.saveButton) {
            this.saveButton.disabled = true;
            this.saveButton.classList.add("opacity-50", "cursor-not-allowed");
        }

        // Visual feedback for clearing
        const feedback = document.createElement("div");
        feedback.textContent = "Signature cleared";
        feedback.className =
            "text-sm text-yellow-500 mt-2 transition-opacity duration-1000 ease-out opacity-100";
        this.container.appendChild(feedback);

        setTimeout(() => {
            feedback.classList.replace("opacity-100", "opacity-0");
            setTimeout(() => feedback.remove(), 1000);
        }, 1500);
    }

    /**
     * Handle form submission
     * @param {Event} e - Form submission event
     */
    handleSubmit(e) {
        if (this.hiddenInput.hasAttribute("required") && this.isEmpty()) {
            e.preventDefault();
            const errorElement = document.querySelector(
                `${this.options.canvasSelector}-error`
            );
            if (errorElement) {
                errorElement.textContent = "Signature is required";
            }
            this.container.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
            return false;
        }

        // Save signature to hidden input
        if (!this.isEmpty()) {
            this.hiddenInput.value = this.getSignatureData();
        }

        return true;
    }

    /**
     * Check if signature pad is empty
     * @returns {boolean} - Whether the signature pad is empty
     */
    isEmpty() {
        return this.signaturePad.isEmpty();
    }

    /**
     * Get signature data as base64 data URL
     * @returns {string} - Base64 data URL of signature
     */
    getSignatureData() {
        return this.signaturePad.toDataURL("image/png");
    }

    /**
     * Set up toolbar with pen options
     */
    setupToolbar() {
        const toolbarDiv = document.createElement("div");
        toolbarDiv.className =
            "flex items-center space-x-4 mb-2 py-2 px-2 border-t border-gray-200";

        // Pen colors
        const colors = [
            { name: "Black", value: "#000000" },
            { name: "Blue", value: "#0000ff" },
            { name: "Red", value: "#ff0000" },
        ];

        const colorContainer = document.createElement("div");
        colorContainer.className = "flex items-center space-x-2";

        const colorLabel = document.createElement("span");
        colorLabel.className = "text-xs text-gray-500";
        colorLabel.textContent = "Color:";
        colorContainer.appendChild(colorLabel);

        colors.forEach((color) => {
            const colorBtn = document.createElement("button");
            colorBtn.type = "button";
            colorBtn.className =
                "w-6 h-6 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500";
            colorBtn.style.backgroundColor = color.value;
            colorBtn.title = color.name;

            colorBtn.addEventListener("click", () => {
                this.signaturePad.penColor = color.value;
                // Highlight selected color
                colorContainer.querySelectorAll("button").forEach((btn) => {
                    btn.classList.remove("ring-2");
                });
                colorBtn.classList.add("ring-2");
            });

            if (color.value === this.signaturePad.penColor) {
                colorBtn.classList.add("ring-2");
            }

            colorContainer.appendChild(colorBtn);
        });

        toolbarDiv.appendChild(colorContainer);

        // Pen thickness
        const thicknessContainer = document.createElement("div");
        thicknessContainer.className = "flex items-center space-x-2";

        const thicknessLabel = document.createElement("span");
        thicknessLabel.className = "text-xs text-gray-500";
        thicknessLabel.textContent = "Thickness:";
        thicknessContainer.appendChild(thicknessLabel);

        const thicknessInput = document.createElement("input");
        thicknessInput.type = "range";
        thicknessInput.min = "1";
        thicknessInput.max = "5";
        thicknessInput.step = "0.5";
        thicknessInput.value = this.signaturePad.dotSize || "2";
        thicknessInput.className = "w-20";

        thicknessInput.addEventListener("change", () => {
            this.signaturePad.dotSize = parseFloat(thicknessInput.value);
        });

        thicknessContainer.appendChild(thicknessInput);
        toolbarDiv.appendChild(thicknessContainer);

        // Insert toolbar before the canvas container
        const canvasContainer = this.canvas.parentElement;
        canvasContainer.insertAdjacentElement("beforebegin", toolbarDiv);
    }
}
