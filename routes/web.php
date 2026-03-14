<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\MemberPortalController;
use App\Http\Controllers\ContactLogController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\JoinApplicationController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/organization', [PublicController::class, 'organization'])->name('organization');
Route::get('/clubs', [PublicController::class, 'clubs'])->name('clubs');
Route::get('/clubs/{club}', [PublicController::class, 'clubDetail'])->name('clubs.show');
Route::get('/news', [PublicController::class, 'news'])->name('news');
Route::get('/events', [PublicController::class, 'events'])->name('events');
Route::get('/events/{event}', [PublicController::class, 'eventDetail'])->name('events.show');
Route::get('/join', [PublicController::class, 'joinForm'])->name('join');
Route::post('/join', [JoinApplicationController::class, 'store'])->name('join.store');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');

// Auth routes (Breeze)
require __DIR__.'/auth.php';

// Member portal (authenticated)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [MemberPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/portal/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/portal/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/portal/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/portal/directory', [MemberPortalController::class, 'directory'])->name('portal.directory');
    Route::get('/portal/club-members', [MemberPortalController::class, 'clubMembers'])->name('portal.club-members');
    Route::get('/portal/events', [MemberPortalController::class, 'events'])->name('portal.events');
    Route::get('/portal/events/{event}', [MemberPortalController::class, 'eventDetail'])->name('portal.events.show');
    Route::post('/portal/events/{event}/register', [EventRegistrationController::class, 'register'])->name('portal.events.register');
    Route::delete('/portal/events/{event}/unregister', [EventRegistrationController::class, 'unregister'])->name('portal.events.unregister');
    Route::get('/portal/documents', [MemberPortalController::class, 'documents'])->name('portal.documents');
    Route::get('/portal/documents/{document}/download', [DocumentController::class, 'download'])->name('portal.documents.download');
    Route::get('/portal/announcements', [MemberPortalController::class, 'announcements'])->name('portal.announcements');
    Route::get('/portal/announcements/{announcement}', [MemberPortalController::class, 'announcementDetail'])->name('portal.announcements.show');
    Route::post('/portal/contact-log', [ContactLogController::class, 'store'])->name('portal.contact-log');
    Route::get('/portal/member/{member}/card', [MemberPortalController::class, 'memberCard'])->name('portal.member.card');
});
