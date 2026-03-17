<?php

namespace App\Http\Controllers;

use App\Models\Payout;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttachmentController extends Controller
{
    public function download(Payout $payout): BinaryFileResponse
    {
        abort_unless($payout->hasAttachment(), 404);

        $disk = Storage::disk('local');

        abort_unless($disk->exists($payout->attachment_path), 404);

        return response()->download(
            $disk->path($payout->attachment_path),
            $payout->attachment_name
        );
    }
}
