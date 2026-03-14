<?php
namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Club;
use App\Models\Document;
use App\Models\Event;
use App\Models\Member;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MemberPortalController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $member = $user->member;
        $announcements = Announcement::published()
            ->whereIn('target', ['all', 'members'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('publish_date', 'desc')
            ->limit(5)
            ->get();
        $events = Event::where('status', 'published')
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->limit(5)
            ->get();
        return view('portal.dashboard', compact('member', 'announcements', 'events'));
    }

    public function directory(Request $request)
    {
        $query = Member::with(['club', 'district'])->where('is_active', true);
        if ($request->search) $query->where(function($q) use ($request) {
            $q->where('name_zh', 'like', "%{$request->search}%")
              ->orWhere('name_en', 'like', "%{$request->search}%");
        });
        if ($request->club_id) $query->where('club_id', $request->club_id);
        if ($request->district_id) $query->where('district_id', $request->district_id);
        if ($request->city) $query->where('city', $request->city);
        if ($request->profession) $query->where('profession', 'like', "%{$request->profession}%");
        $members = $query->paginate(20);
        $clubs = Club::where('is_active', true)->orderBy('name')->get();
        $districts = \App\Models\District::where('is_active', true)->orderBy('name')->get();
        return view('portal.directory', compact('members', 'clubs', 'districts'));
    }

    public function clubMembers()
    {
        $user = auth()->user();
        $member = $user->member;
        if (!$member || !$member->club_id) {
            return view('portal.club-members', ['members' => collect(), 'club' => null]);
        }
        $club = $member->club->load(['president', 'secretary', 'treasurer']);
        $members = Member::where('club_id', $club->id)->where('is_active', true)->get();
        return view('portal.club-members', compact('members', 'club'));
    }

    public function events(Request $request)
    {
        $query = Event::where('status', 'published')
            ->whereIn('visibility', ['public', 'members'])
            ->orderBy('start_time');
        if ($request->search) $query->where('title', 'like', "%{$request->search}%");
        $events = $query->paginate(12);
        return view('portal.events', compact('events'));
    }

    public function eventDetail(Event $event)
    {
        abort_if($event->status !== 'published', 404);
        $member = auth()->user()->member;
        $isRegistered = $member ? $event->registrations()->where('member_id', $member->id)->exists() : false;
        return view('portal.event-detail', compact('event', 'isRegistered'));
    }

    public function documents(Request $request)
    {
        $user = auth()->user();
        $member = $user->member;
        $query = Document::whereIn('visibility', ['public', 'members']);
        if ($request->category) $query->where('category', $request->category);
        if ($request->search) $query->where('title', 'like', "%{$request->search}%");
        $documents = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('portal.documents', compact('documents'));
    }

    public function announcements(Request $request)
    {
        $announcements = Announcement::published()
            ->whereIn('target', ['all', 'members'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('publish_date', 'desc')
            ->paginate(15);
        return view('portal.announcements', compact('announcements'));
    }

    public function announcementDetail(Announcement $announcement)
    {
        return view('portal.announcement-detail', compact('announcement'));
    }

    public function memberCard(Member $member)
    {
        $qrCode = QrCode::format('svg')->size(200)->generate(route('portal.member.card', $member));
        return view('portal.member-card', compact('member', 'qrCode'));
    }
}
