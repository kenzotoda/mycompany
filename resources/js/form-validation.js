function digitsOnly(value) {
    return (value ?? '').replace(/\D/g, '');
}

function parseNumber(value) {
    if (! value) {
        return 0;
    }

    return parseFloat(String(value).replace(/\./g, '').replace(',', '.')) || 0;
}

function isVisible(field) {
    if (! field || field.disabled) {
        return false;
    }

    if (field.type === 'hidden') {
        return false;
    }

    if (field.matches('select[data-searchable-select]')) {
        const container = getSelect2Container(field);

        return container?.offsetParent !== null;
    }

    return field.offsetParent !== null;
}

function getFieldWrapper(field) {
    return field.closest('.mc-field') ?? field.closest('[data-validate-group]');
}

function getSelect2Container(field) {
    const next = field?.nextElementSibling;

    if (next?.classList.contains('select2-container')) {
        return next;
    }

    return null;
}

function clearFieldError(field) {
    field.classList.remove('mc-input-invalid');

    const container = getSelect2Container(field);

    if (container) {
        container.classList.remove('is-invalid');
    }

    const fieldWrapper = getFieldWrapper(field);
    const errorEl = fieldWrapper?.querySelector('[data-frontend-error]');

    if (errorEl) {
        errorEl.textContent = '';
        errorEl.classList.add('hidden');
    }
}

export function clearFormErrors(form) {
    form.querySelectorAll('.mc-input-invalid').forEach((field) => field.classList.remove('mc-input-invalid'));
    form.querySelectorAll('.select2-container.is-invalid').forEach((container) => container.classList.remove('is-invalid'));
    form.querySelectorAll('[data-frontend-error]').forEach((errorEl) => {
        errorEl.textContent = '';
        errorEl.classList.add('hidden');
    });
}

function showFieldError(field, message) {
    field.classList.add('mc-input-invalid');

    const container = getSelect2Container(field);

    if (container) {
        container.classList.add('is-invalid');
    }

    const fieldWrapper = getFieldWrapper(field);
    const errorEl = fieldWrapper?.querySelector('[data-frontend-error]');

    if (errorEl) {
        errorEl.textContent = message;
        errorEl.classList.remove('hidden');
    }
}

function focusInvalidField(field) {
    if (field.tomselect) {
        field.tomselect.focus();
    } else {
        field.focus({ preventScroll: true });
    }

    getFieldWrapper(field)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function validateStandardField(field) {
    if (! isVisible(field)) {
        return true;
    }

    const value = (field.value ?? '').trim();
    const isSelect = field.tagName === 'SELECT';
    const isEmpty = value === '';

    if (field.required && isEmpty) {
        showFieldError(
            field,
            field.dataset.errorRequired ?? (isSelect ? 'Selecione uma opção.' : 'Preencha este campo.'),
        );

        return false;
    }

    if (isEmpty) {
        return true;
    }

    if (field.dataset.validateCnpj !== undefined && digitsOnly(value).length !== 14) {
        showFieldError(field, 'O CNPJ deve ter 14 dígitos no formato 00.000.000/0000-00.');

        return false;
    }

    if (field.dataset.validateCpf !== undefined && digitsOnly(value).length !== 11) {
        showFieldError(field, 'O CPF deve ter 11 dígitos no formato 000.000.000-00.');

        return false;
    }

    if (field.dataset.validatePhoneIfFilled !== undefined) {
        const phoneDigits = digitsOnly(value);

        if (phoneDigits.length < 10 || phoneDigits.length > 11) {
            showFieldError(field, 'Informe um telefone válido com DDD.');

            return false;
        }
    }

    if (field.type === 'email' && ! field.checkValidity()) {
        showFieldError(field, 'Informe um e-mail válido.');

        return false;
    }

    if (field.dataset.validateMin !== undefined) {
        const min = Number(field.dataset.validateMin);
        const numericValue = field.dataset.numericInteger !== undefined
            ? parseInt(digitsOnly(value), 10)
            : parseNumber(value);

        if (Number.isNaN(numericValue) || numericValue < min) {
            showFieldError(field, field.dataset.errorMin ?? `Informe um valor maior ou igual a ${min}.`);

            return false;
        }
    }

    if (field.dataset.validateGtZero !== undefined) {
        const numericValue = parseNumber(value);

        if (numericValue <= 0) {
            showFieldError(field, 'Informe um valor maior que zero.');

            return false;
        }
    }

    return true;
}

function validateCustomerDocument(form) {
    const documentType = form.querySelector('input[name="customer_document_type"]:checked')?.value ?? '';
    const documentField = form.querySelector('[data-validate-document]');

    if (! documentField || ! isVisible(documentField)) {
        return true;
    }

    const digits = digitsOnly(documentField.value);

    if (documentType && ! digits) {
        showFieldError(documentField, `Informe o ${documentType.toUpperCase()} ou remova o tipo selecionado.`);

        return false;
    }

    if (documentType === 'cpf' && digits && digits.length !== 11) {
        showFieldError(documentField, 'O CPF deve ter 11 dígitos no formato 000.000.000-00.');

        return false;
    }

    if (documentType === 'cnpj' && digits && digits.length !== 14) {
        showFieldError(documentField, 'O CNPJ deve ter 14 dígitos no formato 00.000.000/0000-00.');

        return false;
    }

    return true;
}

function validateSaleInstallments(form) {
    const installmentsField = form.querySelector('[data-validate-installments]');

    if (! installmentsField || ! isVisible(installmentsField)) {
        return true;
    }

    const value = parseInt(digitsOnly(installmentsField.value), 10);

    if (! value) {
        showFieldError(installmentsField, 'Informe em quantas parcelas será o crédito.');

        return false;
    }

    if (value < 2) {
        showFieldError(installmentsField, 'O crédito parcelado deve ter no mínimo 2 parcelas.');

        return false;
    }

    return true;
}

export function validateForm(form) {
    if (! form) {
        return false;
    }

    clearFormErrors(form);

    let isValid = true;
    let firstInvalidField = null;

    form.querySelectorAll('input, select, textarea').forEach((field) => {
        if (field.type === 'radio' || field.type === 'checkbox' || field.type === 'file') {
            return;
        }

        if (! validateStandardField(field)) {
            isValid = false;
            firstInvalidField ??= field;
        }
    });

    if (form.dataset.validateCustomerDocument !== undefined && ! validateCustomerDocument(form)) {
        isValid = false;
        firstInvalidField ??= form.querySelector('[data-validate-document]');
    }

    if (form.dataset.validateSaleForm !== undefined && ! validateSaleInstallments(form)) {
        isValid = false;
        firstInvalidField ??= form.querySelector('[data-validate-installments]');
    }

    if (! isValid && firstInvalidField) {
        focusInvalidField(firstInvalidField);
    }

    return isValid;
}

export function submitValidatedForm(form, submitFn) {
    window.flushFormInputs?.(form);

    return new Promise((resolve) => {
        queueMicrotask(() => {
            if (! validateForm(form)) {
                resolve(false);

                return;
            }

            submitFn();
            resolve(true);
        });
    });
}

function bindFormValidationClear() {
    document.addEventListener('input', (event) => {
        const field = event.target;

        if (! field.matches('input, select, textarea')) {
            return;
        }

        clearFieldError(field);
    }, true);

    document.addEventListener('change', (event) => {
        const field = event.target;

        if (! field.matches('select, input[type="radio"]')) {
            return;
        }

        clearFieldError(field);
    }, true);
}

bindFormValidationClear();

window.validateForm = validateForm;
window.submitValidatedForm = submitValidatedForm;
window.clearFormErrors = clearFormErrors;
window.clearFieldError = clearFieldError;
