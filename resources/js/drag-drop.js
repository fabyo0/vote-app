// Drag and Drop File Upload
(function() {
    const initDragDrop = (dropZone, inputElement) => {
        if (!dropZone || !inputElement) return;

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('drag-over');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('drag-over');
            }, false);
        });

        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                // Create a new FileList-like object
                const dataTransfer = new DataTransfer();
                Array.from(files).forEach(file => {
                    // Only add image files
                    if (file.type.startsWith('image/')) {
                        dataTransfer.items.add(file);
                    }
                });

                // Update the input element
                inputElement.files = dataTransfer.files;
                
                // Trigger change event for Livewire
                const event = new Event('change', { bubbles: true });
                inputElement.dispatchEvent(event);
            }
        }, false);
    };

    // Initialize drag and drop for all drop zones on page load
    document.addEventListener('DOMContentLoaded', () => {
        const dropZones = document.querySelectorAll('[data-drop-zone]');
        dropZones.forEach(zone => {
            const inputId = zone.getAttribute('data-drop-zone');
            const inputElement = document.getElementById(inputId);
            if (inputElement) {
                initDragDrop(zone, inputElement);
            }
        });
    });

    // Also initialize when Livewire updates the DOM
    document.addEventListener('livewire:load', () => {
        const dropZones = document.querySelectorAll('[data-drop-zone]');
        dropZones.forEach(zone => {
            const inputId = zone.getAttribute('data-drop-zone');
            const inputElement = document.getElementById(inputId);
            if (inputElement) {
                initDragDrop(zone, inputElement);
            }
        });
    });
})();

