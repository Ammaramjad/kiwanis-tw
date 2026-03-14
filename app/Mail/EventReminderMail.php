<?php
namespace App\Mail;

use App\Models\Event;
use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Event $event, public Member $member) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '【活動提醒】' . $this->event->title);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.event-reminder');
    }
}
