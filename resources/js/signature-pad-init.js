document.addEventListener('DOMContentLoaded', function() { initializeSignaturePads(); });

function initializeSignaturePads() {
    const containers = document.querySelectorAll('[data-signature-pad-container]');
    if (containers.length === 0) return;
    
    if (typeof SignaturePad === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js';
        script.onload = () => initPads(containers);
        document.head.appendChild(script);
    } else {
        initPads(containers);
    }
}

window.initializeSignaturePads = initializeSignaturePads;
