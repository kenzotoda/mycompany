function digitsOnly(value) {
    return (value ?? '').replace(/\D/g, '');
}

export function formatCpf(value) {
    const digits = digitsOnly(value).slice(0, 11);

    if (digits.length <= 3) return digits;
    if (digits.length <= 6) return `${digits.slice(0, 3)}.${digits.slice(3)}`;
    if (digits.length <= 9) return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6)}`;

    return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6, 9)}-${digits.slice(9)}`;
}

export function formatCnpj(value) {
    const digits = digitsOnly(value).slice(0, 14);

    if (digits.length <= 2) return digits;
    if (digits.length <= 5) return `${digits.slice(0, 2)}.${digits.slice(2)}`;
    if (digits.length <= 8) return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5)}`;
    if (digits.length <= 12) return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5, 8)}/${digits.slice(8)}`;

    return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5, 8)}/${digits.slice(8, 12)}-${digits.slice(12)}`;
}

export function formatPhone(value) {
    const digits = digitsOnly(value).slice(0, 11);

    if (digits.length === 0) return '';
    if (digits.length <= 2) return `(${digits}`;
    if (digits.length <= 6) return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
    if (digits.length <= 10) return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;

    return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7)}`;
}

export function blockMaskKey(event) {
    const controlKeys = ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight', 'Home', 'End'];

    if (controlKeys.includes(event.key) || event.ctrlKey || event.metaKey) {
        return;
    }

    if (!/^\d$/.test(event.key)) {
        event.preventDefault();
    }
}

export function digitsFromMask(mask, value) {
    const limits = { cpf: 11, cnpj: 14, phone: 11 };

    return digitsOnly(value).slice(0, limits[mask] ?? 20);
}

export function formatByMask(mask, value) {
    const formatters = {
        cpf: formatCpf,
        cnpj: formatCnpj,
        phone: formatPhone,
    };

    return formatters[mask]?.(value) ?? value;
}

export function applyMask(event, mask) {
    const formatters = {
        cpf: formatCpf,
        cnpj: formatCnpj,
        phone: formatPhone,
    };

    const formatter = formatters[mask];

    if (! formatter) {
        return;
    }

    event.target.value = formatter(event.target.value);
}

export function formatAfterPaste(event, mask) {
    queueMicrotask(() => {
        applyMask({ target: event.target }, mask);
        event.target.dispatchEvent(new Event('input', { bubbles: true }));
    });
}

export function syncMaskedInputs(root = document) {
    root.querySelectorAll('[data-mask]').forEach((input) => {
        applyMask({ target: input }, input.dataset.mask);
    });
}

window.flushFormInputs = function (form) {
    form?.querySelectorAll('input, textarea, select').forEach((field) => {
        field.dispatchEvent(new Event('input', { bubbles: true }));
        field.dispatchEvent(new Event('change', { bubbles: true }));
    });
};

window.syncMaskedInputs = syncMaskedInputs;

window.blockMaskKey = blockMaskKey;
window.formatAfterPaste = formatAfterPaste;
window.applyMask = applyMask;
window.formatCpf = formatCpf;
window.formatCnpj = formatCnpj;
window.formatPhone = formatPhone;
window.digitsFromMask = digitsFromMask;
window.formatByMask = formatByMask;
