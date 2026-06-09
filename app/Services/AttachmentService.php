<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AttachmentService
{
    public function attachToModel(Model $model, array $files, User $user, string $category = 'other'): void
    {
        $files = array_values(array_filter($files));

        foreach ($files as $file) {
            if (! $file) {
                continue;
            }

            $path = $file->store('attachments/'.$user->company_id.'/'.strtolower(class_basename($model)), 'public');

            Attachment::create([
                'company_id' => $user->company_id,
                'attachable_type' => $model::class,
                'attachable_id' => $model->id,
                'category' => $category,
                'disk' => 'public',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => $user->id,
            ]);
        }
    }
}
