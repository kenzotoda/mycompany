<?php

namespace App\Livewire\Concerns;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait ManagesDocumentUploads
{
    /** @var array<int, TemporaryUploadedFile> */
    public array $uploadedDocuments = [];

    /** @var array<int, TemporaryUploadedFile> */
    public array $documents = [];

    public function updatedDocuments(): void
    {
        $incoming = $this->normalizeDocuments($this->documents);

        if ($incoming === []) {
            return;
        }

        $this->validate([
            'documents.*' => ['file', 'max:10240'],
        ]);

        foreach ($incoming as $file) {
            $this->uploadedDocuments[] = $file;
        }

        $this->documents = [];
        $this->resetValidation('documents');
    }

    public function removeUploadedDocument(int $index): void
    {
        if (! array_key_exists($index, $this->uploadedDocuments)) {
            return;
        }

        unset($this->uploadedDocuments[$index]);
        $this->uploadedDocuments = array_values($this->uploadedDocuments);
    }

    /**
     * @return array<int, TemporaryUploadedFile>
     */
    protected function documentsForSave(): array
    {
        return $this->uploadedDocuments;
    }

    /**
     * @return array<int, TemporaryUploadedFile>
     */
    protected function normalizeDocuments(mixed $files): array
    {
        if ($files === null || $files === []) {
            return [];
        }

        if (is_array($files)) {
            return array_values(array_filter($files));
        }

        return [$files];
    }
}
