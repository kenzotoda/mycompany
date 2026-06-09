<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function preview(Attachment $attachment)
    {
        abort_unless(auth()->check() && auth()->user()->company_id === $attachment->company_id, 403);

        abort_unless(Storage::disk($attachment->disk)->exists($attachment->path), 404);

        $stream = Storage::disk($attachment->disk)->readStream($attachment->path);

        return response()->stream(function () use ($stream): void {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $attachment->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.$attachment->original_name.'"',
        ]);
    }
}
