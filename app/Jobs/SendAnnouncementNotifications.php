<?php
namespace App\Jobs;

use App\Models\Announcement;
use App\Models\Member;
use App\Services\LineMessagingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendAnnouncementNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Announcement $announcement) {}

    public function handle(LineMessagingService $lineService): void
    {
        $query = Member::query()->where('is_active', true);

        match($this->announcement->target) {
            'district' => $query->where('district_id', $this->announcement->district_id),
            'club' => $query->where('club_id', $this->announcement->club_id),
            default => null,
        };

        $members = $query->whereNotNull('email')->get();

        foreach ($members as $member) {
            try {
                Mail::to($member->email)->queue(new \App\Mail\AnnouncementMail($this->announcement, $member));
            } catch (\Exception $e) {
                Log::error("Failed to send email to member {$member->id}: " . $e->getMessage());
            }
        }

        $text = "【公告】{$this->announcement->title}\n{$this->announcement->content}";
        if (mb_strlen($text) > 1000) {
            $text = mb_substr($text, 0, 997) . '...';
        }

        if ($this->announcement->target === 'all') {
            $lineService->broadcast($text);
        }
    }
}
