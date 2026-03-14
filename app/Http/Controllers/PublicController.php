<?php
namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Club;
use App\Models\District;
use App\Models\Event;
use App\Models\Member;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $announcements = Announcement::published()
            ->where('target', 'public')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('publish_date', 'desc')
            ->limit(6)
            ->get();

        $events = Event::where('status', 'published')
            ->where('visibility', 'public')
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->limit(6)
            ->get();

        $stats = [
            'members' => Member::where('is_active', true)->count(),
            'clubs' => Club::where('is_active', true)->count(),
            'districts' => District::where('is_active', true)->count(),
        ];

        return view('public.home', compact('announcements', 'events', 'stats'));
    }

    public function about()
    {
        return view('public.about');
    }

    public function organization()
    {
        $districts = District::with(['clubs' => fn($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->get();
        return view('public.organization', compact('districts'));
    }

    public function clubs(Request $request)
    {
        $query = Club::with('district')->where('is_active', true);
        if ($request->district) $query->where('district_id', $request->district);
        if ($request->city) $query->where('city', $request->city);
        if ($request->search) $query->where('name', 'like', "%{$request->search}%");
        $clubs = $query->paginate(12);
        $districts = District::where('is_active', true)->get();
        return view('public.clubs', compact('clubs', 'districts'));
    }

    public function clubDetail(Club $club)
    {
        $club->load(['district', 'president', 'secretary', 'treasurer']);
        $members = Member::where('club_id', $club->id)->where('is_active', true)->get();
        $events = Event::where('club_id', $club->id)->where('status', 'published')->orderBy('start_time', 'desc')->limit(5)->get();
        return view('public.club-detail', compact('club', 'members', 'events'));
    }

    public function news(Request $request)
    {
        $announcements = Announcement::published()
            ->whereIn('target', ['all', 'public'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('publish_date', 'desc')
            ->paginate(12);
        return view('public.news', compact('announcements'));
    }

    public function events(Request $request)
    {
        $query = Event::where('status', 'published')
            ->where('visibility', 'public')
            ->where('start_time', '>=', now())
            ->orderBy('start_time');
        if ($request->search) $query->where('title', 'like', "%{$request->search}%");
        $events = $query->paginate(12);
        return view('public.events', compact('events'));
    }

    public function eventDetail(Event $event)
    {
        abort_if($event->status !== 'published' || $event->visibility !== 'public', 404);
        return view('public.event-detail', compact('event'));
    }

    public function joinForm()
    {
        $clubs = Club::where('is_active', true)->orderBy('name')->get();
        return view('public.join', compact('clubs'));
    }

    public function contact()
    {
        return view('public.contact');
    }
}
