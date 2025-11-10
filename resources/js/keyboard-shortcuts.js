// Keyboard Shortcuts
(function() {
    const shortcuts = {
        // Navigation shortcuts
        'g h': () => window.location.href = '/', // Go Home
        'g p': () => window.location.href = '/profile', // Go Profile
        'g n': () => {
            const notificationButton = document.querySelector('[data-shortcut="notifications"]');
            if (notificationButton) notificationButton.click();
        }, // Go Notifications
        
        // Action shortcuts
        'c': (e) => {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                const createButton = document.querySelector('[data-shortcut="create-idea"]');
                if (createButton) {
                    e.preventDefault();
                    createButton.focus();
                    createButton.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        }, // Create idea
        
        // Search shortcut
        '/': (e) => {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                const searchInput = document.querySelector('[data-shortcut="search"]');
                if (searchInput) {
                    e.preventDefault();
                    searchInput.focus();
                }
            }
        },
        
        // Escape to close modals
        'Escape': (e) => {
            const modals = document.querySelectorAll('[x-show]');
            modals.forEach(modal => {
                if (modal.__x && modal.__x.$data && modal.__x.$data.isOpen) {
                    modal.__x.$data.isOpen = false;
                }
            });
        },
    };

    let pressedKeys = [];
    const MAX_KEY_SEQUENCE = 2;

    document.addEventListener('keydown', (e) => {
        // Don't trigger shortcuts when typing in inputs
        if (e.target.tagName === 'INPUT' || 
            e.target.tagName === 'TEXTAREA' || 
            e.target.isContentEditable) {
            // Allow Escape and some special shortcuts
            if (e.key === 'Escape') {
                if (shortcuts['Escape']) shortcuts['Escape'](e);
            }
            return;
        }

        pressedKeys.push(e.key.toLowerCase());
        
        // Keep only last MAX_KEY_SEQUENCE keys
        if (pressedKeys.length > MAX_KEY_SEQUENCE) {
            pressedKeys.shift();
        }

        // Check for sequence shortcuts (e.g., 'g' then 'h')
        const sequence = pressedKeys.join(' ');
        if (shortcuts[sequence]) {
            e.preventDefault();
            shortcuts[sequence](e);
            pressedKeys = [];
            return;
        }

        // Check for single key shortcuts
        if (pressedKeys.length === 1 && shortcuts[pressedKeys[0]]) {
            e.preventDefault();
            shortcuts[pressedKeys[0]](e);
            pressedKeys = [];
        }
    });

    document.addEventListener('keyup', () => {
        // Reset sequence after a delay
        setTimeout(() => {
            pressedKeys = [];
        }, 1000);
    });

    // Dark mode toggle with Ctrl+Shift+D or Cmd+Shift+D
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
            e.preventDefault();
            toggleDarkMode();
        }
    });

    // Show help modal with Ctrl+? or Cmd+?
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === '?') {
            e.preventDefault();
            const helpModal = document.querySelector('[data-shortcut="help-modal"]');
            if (helpModal && helpModal.__x) {
                helpModal.__x.$data.isOpen = !helpModal.__x.$data.isOpen;
            }
        }
    });
})();

