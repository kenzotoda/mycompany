<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AttachmentService
{
    public function attachToModel(Model $model, array $files, User $user, string $category = 'other'): void
    {
        $files = array_values(array_filter($files));

        $disk = $this->diskForModel($model);
        $folder = strtolower(class_basename($model));

        foreach ($files as $file) {
            if (! $file) {
                continue;
            }

            $filename = Str::uuid().'_'.$file->getClientOriginalName();
            $path = $file->storeAs(
                "attachments/{$user->company_id}/{$folder}/{$model->id}",
                $filename,
                $disk,
            );

            Attachment::create([
                'company_id' => $user->company_id,
                'attachable_type' => $model::class,
                'attachable_id' => $model->id,
                'category' => $category,
                'disk' => $disk,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => $user->id,
            ]);
        }
    }

    private function diskForModel(Model $model): string
    {
        return match ($model::class) {
            Purchase::class => config('supabase.disks.compras'),
            Sale::class => config('supabase.disks.vendas'),
            default => 'public',
        };
    }
}
