<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::published()->whereIn('target', ['all', 'members', 'public']);
        if ($request->search) $query->where('title', 'like', "%{$request->search}%");
        return response()->json($query->orderBy('is_pinned', 'desc')->orderBy('publish_date', 'desc')->paginate(15));
    }

    public function show(Announcement $announcement)
    {
        abort_if($announcement->status !== 'published', 404);
        return response()->json($announcement);
    }
}
