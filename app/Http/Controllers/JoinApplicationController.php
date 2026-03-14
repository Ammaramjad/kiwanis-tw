<?php
namespace App\Http\Controllers;

use App\Models\JoinApplication;
use Illuminate\Http\Request;

class JoinApplicationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'city' => 'nullable|string|max:100',
            'profession' => 'nullable|string|max:100',
            'preferred_club_id' => 'nullable|exists:clubs,id',
            'message' => 'nullable|string|max:2000',
        ]);

        JoinApplication::create($validated);

        return redirect()->route('join')->with('success', '您的入會申請已送出，我們將盡快與您聯繫。');
    }
}
