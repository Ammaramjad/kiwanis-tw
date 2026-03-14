<?php
namespace App\Mail;

use App\Models\Announcement;
use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Announcement $announcement, public Member $member) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '【公告】' . $this->announcement->title);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.announcement');
    }
}
