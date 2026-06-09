const CONTROL_KEYS = new Set([
    'Backspace', 'Delete', 'Tab', 'Escape', 'Enter',
    'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
    'Home', 'End',
]);

export function blockNumericKey(event, { decimal = false } = {}) {
    if (CONTROL_KEYS.has(event.key)) {
        return;
    }

    if (event.ctrlKey || event.metaKey) {
        return;
    }

    if (/^\d$/.test(event.key)) {
        return;
    }

    if (decimal && (event.key === '.' || event.key === ',')) {
        const value = event.target.value ?? '';
        const separator = value.includes('.') || value.includes(',');
        if (! separator) {
            return;
        }
    }

    event.preventDefault();
}

export function sanitizeNumericInput(event, { decimal = false } = {}) {
    let value = event.target.value ?? '';

    if (decimal) {
        value = value.replace(/[^\d.,]/g, '');
        const parts = value.replace(/,/g, '.').split('.');
        if (parts.length > 2) {
            value = `${parts.shift()}.${parts.join('')}`;
        } else {
            value = parts.join('.');
        }
    } else {
        value = value.replace(/\D/g, '');
    }

    event.target.value = value;
}

window.blockNumericKey = blockNumericKey;
window.sanitizeNumericInput = sanitizeNumericInput;
