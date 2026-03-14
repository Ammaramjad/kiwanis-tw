<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with(['club:id,name', 'district:id,name'])->where('is_active', true);
        if ($request->search) $query->where(function($q) use ($request) {
            $q->where('name_zh', 'like', "%{$request->search}%")->orWhere('name_en', 'like', "%{$request->search}%");
        });
        if ($request->club_id) $query->where('club_id', $request->club_id);
        if ($request->district_id) $query->where('district_id', $request->district_id);
        if ($request->city) $query->where('city', $request->city);
        return response()->json($query->paginate(20));
    }

    public function show(Member $member)
    {
        return response()->json($member->load(['club', 'district', 'officerTerms']));
    }

    public function update(Request $request, Member $member)
    {
        $authMember = $request->user()->member;
        if (!$authMember || ($authMember->id !== $member->id && !$request->user()->hasAnyRole(['super_admin', 'hq_admin', 'club_admin']))) {
            abort(403);
        }
        $validated = $request->validate([
            'name_zh' => 'sometimes|required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'line_id' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'profession' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:2000',
        ]);
        $member->update($validated);
        return response()->json($member);
    }
}
