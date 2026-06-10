import jQuery from './jquery-global.js';
import select2Factory from 'select2/dist/js/select2.full.js';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

if (typeof select2Factory === 'function' && typeof jQuery.fn.select2 !== 'function') {
    select2Factory(window, jQuery);
}

function getPlaceholder(select) {
    const emptyOption = select.querySelector('option[value=""]');

    return emptyOption?.textContent?.trim() || 'Selecione...';
}

function isInitialized(select) {
    return jQuery(select).hasClass('select2-hidden-accessible');
}

function focusSearchField() {
    queueMicrotask(() => {
        document
            .querySelector('.select2-container--open .select2-search__field')
            ?.focus();
    });
}

export function destroySearchableSelects(root = document) {
    const $root = root === document ? jQuery(document) : jQuery(root);

    $root.find('select[data-searchable-select]').each((_, element) => {
        const $element = jQuery(element);

        if ($element.data('select2')) {
            $element.off('.mcSelect2');
            $element.select2('destroy');
        }
    });
}

export function initSearchableSelects(root = document) {
    if (typeof jQuery.fn.select2 !== 'function') {
        console.error('Select2 não foi carregado. Verifique jQuery e select2.full.js.');

        return;
    }

    const $root = root === document ? jQuery(document) : jQuery(root);

    $root.find('select[data-searchable-select]').each((_, element) => {
        const $element = jQuery(element);

        if (isInitialized(element)) {
            return;
        }

        const placeholder = getPlaceholder(element);
        const $dropdownParent = $element.closest('.mc-select2-wrap');

        $element.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder,
            allowClear: false,
            minimumResultsForSearch: 0,
            dropdownParent: $dropdownParent.length ? $dropdownParent : jQuery('body'),
            language: {
                noResults: () => 'Nenhum resultado encontrado',
                searching: () => 'Buscando...',
                inputTooShort: () => 'Digite para buscar',
            },
        });

        $element.on('select2:open.mcSelect2', focusSearchField);

        $element.on('change.mcSelect2', function handleSelectChange() {
            this.dispatchEvent(new Event('input', { bubbles: true }));
            this.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });
}

window.destroySearchableSelects = destroySearchableSelects;
window.initSearchableSelects = initSearchableSelects;
