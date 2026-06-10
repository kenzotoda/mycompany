import './bootstrap';
import './masks';
import './numeric';
import './form-validation';
import { syncMaskedInputs } from './masks';
import { destroySearchableSelects, initSearchableSelects } from './selects';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

function refreshSelects(root = document) {
    queueMicrotask(() => initSearchableSelects(root));
}

refreshSelects();

document.addEventListener('livewire:init', () => {
    refreshSelects();

    Livewire.hook('morph.updating', ({ el }) => {
        destroySearchableSelects(el);
    });

    Livewire.hook('morph.updated', ({ el }) => {
        refreshSelects(el);
    });

    Livewire.hook('commit', ({ succeed }) => {
        succeed(() => {
            queueMicrotask(() => {
                syncMaskedInputs();
                initSearchableSelects();
            });
        });
    });
});
