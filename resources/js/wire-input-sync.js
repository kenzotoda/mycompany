export function getSyncedInputValue(input) {
    if (window.Alpine) {
        try {
            const alpine = window.Alpine.$data(input);

            if (input.dataset.storeDigits !== undefined && alpine && typeof alpine.digits === 'string') {
                return alpine.digits;
            }

            if (alpine && typeof alpine.value === 'string') {
                return alpine.value;
            }
        } catch (_) {
            // Campo sem contexto Alpine.
        }
    }

    if (input.dataset.storeDigits !== undefined && input.dataset.mask) {
        return window.digitsFromMask?.(input.dataset.mask, input.value ?? '') ?? (input.value ?? '');
    }

    return input.value ?? '';
}

function syncEntangledValue(input, value) {
    if (!window.Alpine) {
        return;
    }

    try {
        const alpine = window.Alpine.$data(input);

        if (input.dataset.storeDigits !== undefined && alpine && Object.prototype.hasOwnProperty.call(alpine, 'digits')) {
            alpine.digits = value;

            return;
        }

        if (alpine && Object.prototype.hasOwnProperty.call(alpine, 'value')) {
            alpine.value = value;
        }
    } catch (_) {
        // Campo sem contexto Alpine.
    }
}

export function normalizeDomFromAlpine(form) {
    if (!form) {
        return;
    }

    form.querySelectorAll('[data-wire-model]').forEach((input) => {
        const value = getSyncedInputValue(input);

        if (input.dataset.mask && input.dataset.storeDigits === undefined) {
            window.applyMask?.({ target: input }, input.dataset.mask);
        }

        if (input.dataset.storeDigits !== undefined && input.dataset.mask) {
            input.value = window.formatByMask?.(input.dataset.mask, value) ?? value;
        } else if (input.value !== value) {
            input.value = value;
        }

        syncEntangledValue(input, value);
    });
}

export function syncWireInputs(form, wire) {
    if (!form || !wire) {
        return;
    }

    form.querySelectorAll('[data-wire-model]').forEach((input) => {
        const property = input.dataset.wireModel;

        if (!property) {
            return;
        }

        const value = getSyncedInputValue(input);

        if (input.dataset.storeDigits !== undefined && input.dataset.mask) {
            const digits = window.digitsFromMask?.(input.dataset.mask, value) ?? value;
            input.value = window.formatByMask?.(input.dataset.mask, digits) ?? value;
            syncEntangledValue(input, digits);
            wire.set(property, digits);

            return;
        }

        if (input.dataset.mask) {
            window.applyMask?.({ target: input }, input.dataset.mask);
        }

        if (input.value !== value) {
            input.value = value;
        }

        syncEntangledValue(input, value);
        wire.set(property, value);
    });
}

window.getSyncedInputValue = getSyncedInputValue;
window.normalizeDomFromAlpine = normalizeDomFromAlpine;
window.syncWireInputs = syncWireInputs;
