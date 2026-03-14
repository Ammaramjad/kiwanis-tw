<?php
namespace App\Jobs;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEventReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Event $event) {}

    public function handle(): void
    {
        $registrations = EventRegistration::with('member')
            ->where('event_id', $this->event->id)
            ->where('status', 'registered')
            ->get();

        foreach ($registrations as $registration) {
            if ($registration->member?->email) {
                try {
                    Mail::to($registration->member->email)
                        ->queue(new \App\Mail\EventReminderMail($this->event, $registration->member));
                } catch (\Exception $e) {
                    Log::error("Failed to send event reminder: " . $e->getMessage());
                }
            }
        }
    }
}
