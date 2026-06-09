<div class="mc-upload-zone" wire:key="document-upload-{{ $this->getId() }}">
    <i class="fa-solid fa-paperclip mr-2 text-brand-orange"></i>
    {{ $label ?? 'Anexar documentos' }}
    <input
        type="file"
        wire:model="documents"
        multiple
        class="mt-2 block w-full text-xs text-brand-muted"
    >
    @error('documents.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

    @if (count($uploadedDocuments) > 0)
        <ul class="mt-3 space-y-2">
            @foreach ($uploadedDocuments as $index => $document)
                <li class="flex items-center justify-between gap-2 rounded-lg border border-brand-border bg-white px-3 py-2 text-sm">
                    <span class="truncate">
                        <i class="fa-solid fa-file mr-2 text-brand-orange"></i>
                        {{ $document->getClientOriginalName() }}
                    </span>
                    <button
                        type="button"
                        wire:click="removeUploadedDocument({{ $index }})"
                        class="shrink-0 text-xs text-red-600 hover:underline"
                    >
                        Remover
                    </button>
                </li>
            @endforeach
        </ul>
        <p class="mt-2 mc-hint">{{ count($uploadedDocuments) }} arquivo(s) selecionado(s). Você pode adicionar mais.</p>
    @else
        <p class="mt-2 mc-hint">Selecione um ou mais arquivos. Pode adicionar em várias etapas.</p>
    @endif

    <div wire:loading wire:target="documents" class="mt-2 text-xs text-brand-orange">
        <i class="fa-solid fa-spinner fa-spin mr-1"></i> Enviando arquivo...
    </div>
</div>
