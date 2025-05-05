/**
 * SignaturePadComponent
 *
 * A JavaScript class to handle signature pad functionality including:
 * - Drawing signatures
 * - Changing pen color and thickness
 * - Clearing signatures
 * - Saving signatures as images
 * - Form validation to ensure a signature is present
 *
 * @requires signature_pad (https://github.com/szimek/signature_pad)
 */
export class SignaturePadComponent {
    /**
     * Constructor - initializes the signature pad
     *
     * @param {Object} options Configuration options
     * @param {String} options.canvasSelector Selector for the canvas element
     * @param {String|Element} options.formSelector Selector or element for the form
     * @param {String} options.signatureInputName Name of the input field to store signature data
     * @param {String} options.clearButtonSelector Selector for the clear button
     * @param {String} options.saveButtonSelector Selector for the save button
     * @param {String} [options.toolbarSelector] Selector for the toolbar container
     * @param {String} [options.colorPickerSelector] Selector for the color picker container
     * @param {String} [options.thicknessPickerSelector] Selector for the thickness picker container
     * @param {Array} [options.colors] Array of color values to use
     * @param {Array} [options.thicknesses] Array of thickness values to use
     * @param {String} [options.defaultColor] Default pen color
     * @param {Number} [options.defaultThickness] Default pen thickness
     * @param {String} [options.backgroundColor] Background color for the canvas
     * @param {String} [options.requiredMessage] Error message for required validation
     */
    constructor(options) {
        // Default configuration
        const defaultOptions = {
            canvasSelector: null,
            formSelector: null,
            signatureInputName: "signature",
            clearButtonSelector: null,
            saveButtonSelector: null,
            toolbarSelector: null,
            colorPickerSelector: null,
            thicknessPickerSelector: null,
            colors: ["#000000", "#1a73e8", "#e53935", "#43a047"],
            thicknesses: [1, 2, 3, 4],
            defaultColor: "#000000",
            defaultThickness: 2,
            backgroundColor: "rgba(255, 255, 255, 0)",
            requiredMessage: "Please provide a signature",
        };

        // Merge default options with provided options
        this.options = { ...defaultOptions, ...options };

        // Current state
        this.signaturePad = null;
        this.canvas = null;
        this.form = null;
        this.signatureInput = null;
        this.clearButton = null;
        this.saveButton = null;
        this.toolbarContainer = null;
        this.colorPicker = null;
        this.thicknessPicker = null;
        this.errorElement = null;

        // Current settings
        this.currentColor = this.options.defaultColor;
        this.currentThickness = this.options.defaultThickness;

        // Initialize
        this.init();
    }

    /**
     * Initialize the signature pad
     */
    init() {
        // Find elements
        this.canvas = document.querySelector(this.options.canvasSelector);
        if (!this.canvas) {
            console.error(
                `Canvas element not found: ${this.options.canvasSelector}`
            );
            return;
        }

        // Find or create form
        if (typeof this.options.formSelector === "string") {
            this.form = document.querySelector(this.options.formSelector);
        } else if (this.options.formSelector instanceof Element) {
            this.form = this.options.formSelector;
        } else {
            this.form = this.canvas.closest("form");
        }

        if (!this.form) {
            console.error("Form element not found");
            return;
        }

        // Find buttons
        if (this.options.clearButtonSelector) {
            this.clearButton = document.querySelector(
                this.options.clearButtonSelector
            );
        }

        if (this.options.saveButtonSelector) {
            this.saveButton = document.querySelector(
                this.options.saveButtonSelector
            );
        }

        // Find or create input for signature data
        this.signatureInput = this.form.querySelector(
            `input[name="${this.options.signatureInputName}"]`
        );
        if (!this.signatureInput) {
            this.signatureInput = document.createElement("input");
            this.signatureInput.type = "hidden";
            this.signatureInput.name = this.options.signatureInputName;
            this.form.appendChild(this.signatureInput);
        }

        // Find optional toolbar elements
        if (this.options.toolbarSelector) {
            this.toolbarContainer = document.querySelector(
                this.options.toolbarSelector
            );
        }

        if (this.options.colorPickerSelector) {
            this.colorPicker = document.querySelector(
                this.options.colorPickerSelector
            );
        }

        if (this.options.thicknessPickerSelector) {
            this.thicknessPicker = document.querySelector(
                this.options.thicknessPickerSelector
            );
        }

        // Initialize the signature pad
        this.initSignaturePad();

        // Setup event listeners
        this.setupEventListeners();

        // Setup toolbar if needed
        this.setupToolbar();

        // Initial resize
        this.resizeCanvas();
    }

    /**
     * Initialize the signature pad
     */
    initSignaturePad() {
        if (!window.SignaturePad) {
            console.error("SignaturePad library not loaded");
            return;
        }

        // Initialize the signature pad
        this.signaturePad = new window.SignaturePad(this.canvas, {
            backgroundColor: this.options.backgroundColor,
            penColor: this.currentColor,
            velocityFilterWeight: 0.7,
            minWidth: this.currentThickness,
            maxWidth: this.currentThickness * 2,
            throttle: 16,
        });

        // Restore existing signature if any
        if (this.signatureInput.value) {
            try {
                this.signaturePad.fromDataURL(this.signatureInput.value);
            } catch (e) {
                console.warn("Could not restore signature from input", e);
            }
        }
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Clear button
        if (this.clearButton) {
            this.clearButton.addEventListener("click", (e) => {
                e.preventDefault();
                this.clear();
            });
        }

        // Save button
        if (this.saveButton) {
            this.saveButton.addEventListener("click", (e) => {
                e.preventDefault();
                this.save();
            });
        }

        // Form submission
        this.form.addEventListener("submit", (e) => {
            if (!this.validateSubmit()) {
                e.preventDefault();
                return false;
            }
            return true;
        });

        // Window resize
        window.addEventListener("resize", () => {
            this.resizeCanvas();
        });
    }

    /**
     * Setup toolbar with color and thickness options
     */
    setupToolbar() {
        // Setup color picker
        if (this.colorPicker) {
            // Clear existing content
            this.colorPicker.innerHTML = "";

            // Create color options
            this.options.colors.forEach((color) => {
                const colorOption = document.createElement("div");
                colorOption.classList.add("signature-color-option");
                colorOption.style.backgroundColor = color;

                if (color === this.currentColor) {
                    colorOption.classList.add("active");
                }

                colorOption.addEventListener("click", () => {
                    // Update active state
                    this.colorPicker
                        .querySelectorAll(".signature-color-option")
                        .forEach((el) => {
                            el.classList.remove("active");
                        });
                    colorOption.classList.add("active");

                    // Update color
                    this.currentColor = color;
                    if (this.signaturePad) {
                        this.signaturePad.penColor = color;
                    }
                });

                this.colorPicker.appendChild(colorOption);
            });
        }

        // Setup thickness picker
        if (this.thicknessPicker) {
            // Clear existing content
            this.thicknessPicker.innerHTML = "";

            // Create thickness options
            this.options.thicknesses.forEach((thickness) => {
                const thicknessOption = document.createElement("div");
                thicknessOption.classList.add("signature-thickness-option");

                // Create a preview line
                const preview = document.createElement("div");
                preview.classList.add("thickness-preview");
                preview.style.height = `${thickness}px`;
                preview.style.backgroundColor = this.currentColor;
                thicknessOption.appendChild(preview);

                if (thickness === this.currentThickness) {
                    thicknessOption.classList.add("active");
                }

                thicknessOption.addEventListener("click", () => {
                    // Update active state
                    this.thicknessPicker
                        .querySelectorAll(".signature-thickness-option")
                        .forEach((el) => {
                            el.classList.remove("active");
                        });
                    thicknessOption.classList.add("active");

                    // Update thickness
                    this.currentThickness = thickness;
                    if (this.signaturePad) {
                        this.signaturePad.minWidth = thickness;
                        this.signaturePad.maxWidth = thickness * 2;
                    }
                });

                this.thicknessPicker.appendChild(thicknessOption);
            });
        }
    }

    /**
     * Resize the canvas to fit its container
     */
    resizeCanvas() {
        if (!this.canvas || !this.signaturePad) {
            return;
        }

        // Get the devicePixelRatio to support high-DPI devices
        const ratio = Math.max(window.devicePixelRatio || 1, 1);

        // Get the parent element's dimensions
        const parentWidth = this.canvas.parentElement.clientWidth;

        // Adjust the canvas dimensions
        this.canvas.width = parentWidth * ratio;
        this.canvas.height = parentWidth * 0.5 * ratio; // 2:1 aspect ratio or adjust as needed

        // Scale the context to correspond to actual device dimensions
        const context = this.canvas.getContext("2d");
        context.scale(ratio, ratio);

        // Adjust canvas CSS dimensions
        this.canvas.style.width = `${parentWidth}px`;
        this.canvas.style.height = `${parentWidth * 0.5}px`;

        // Clear the canvas
        this.signaturePad.clear();

        // Restore existing signature if any
        if (this.signatureInput.value) {
            try {
                this.signaturePad.fromDataURL(this.signatureInput.value);
            } catch (e) {
                console.warn("Could not restore signature after resize", e);
            }
        }
    }

    /**
     * Clear the signature
     */
    clear() {
        if (this.signaturePad) {
            this.signaturePad.clear();
            this.signatureInput.value = "";
            this.removeError();
        }
    }

    /**
     * Save the signature to the input field
     */
    save() {
        if (!this.signaturePad) {
            return false;
        }

        if (this.signaturePad.isEmpty()) {
            this.showError(this.options.requiredMessage);
            return false;
        }

        try {
            const dataUrl = this.signaturePad.toDataURL("image/png");
            this.signatureInput.value = dataUrl;
            this.removeError();
            return true;
        } catch (error) {
            console.error("Error saving signature", error);
            return false;
        }
    }

    /**
     * Validate the form submission
     *
     * @returns {Boolean} Whether the form is valid
     */
    validateSubmit() {
        // Check if signature is required
        if (
            this.signatureInput.hasAttribute("required") ||
            this.canvas.hasAttribute("required")
        ) {
            if (this.isEmpty()) {
                this.save(); // This will show the error if needed
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the signature pad is empty
     *
     * @returns {Boolean} Whether the signature pad is empty
     */
    isEmpty() {
        return (
            !this.signatureInput.value ||
            (this.signaturePad && this.signaturePad.isEmpty())
        );
    }

    /**
     * Get the signature data
     *
     * @returns {String} The signature data URL
     */
    getSignatureData() {
        return this.signatureInput.value;
    }

    /**
     * Show an error message
     *
     * @param {String} message The error message
     */
    showError(message) {
        this.removeError();

        // Create error element
        this.errorElement = document.createElement("div");
        this.errorElement.classList.add("signature-pad-error");
        this.errorElement.textContent = message;
        this.errorElement.style.color = "#e53935";
        this.errorElement.style.marginTop = "0.5rem";
        this.errorElement.style.fontSize = "0.875rem";

        // Add to DOM after the canvas
        this.canvas.parentNode.insertBefore(
            this.errorElement,
            this.canvas.nextSibling
        );

        // Add error class to canvas
        this.canvas.classList.add("signature-pad-error");
        this.canvas.style.borderColor = "#e53935";
    }

    /**
     * Remove the error message
     */
    removeError() {
        if (this.errorElement) {
            this.errorElement.remove();
            this.errorElement = null;
        }

        // Remove error class from canvas
        this.canvas.classList.remove("signature-pad-error");
        this.canvas.style.borderColor = "";
    }
}
