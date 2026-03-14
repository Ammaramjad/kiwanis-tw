<?php
namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function download(Document $document)
    {
        // Check visibility
        $user = auth()->user();
        if ($document->visibility === 'club_admin' && !$user->hasAnyRole(['super_admin', 'hq_admin', 'district_admin', 'club_admin'])) {
            abort(403);
        }
        if ($document->visibility === 'hq_admin' && !$user->hasAnyRole(['super_admin', 'hq_admin'])) {
            abort(403);
        }

        $document->increment('download_count');
        return Storage::download($document->file_path, $document->file_name);
    }
}
