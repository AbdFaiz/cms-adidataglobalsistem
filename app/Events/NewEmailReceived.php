<?php

namespace App\Events;

use App\Models\Email;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class NewEmailReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $email;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('emails'),
        ];
    }

    public function broadcastAs()
    {
        return 'new.email';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->email->id,
            'ticket_number' => $this->email->ticket_number,
            'subject' => (string) $this->email->subject,
            'from' => (string) $this->email->from_email,
            'date' => $this->email->created_at->toDateTimeString(),
            'preview' => Str::limit(strip_tags((string) $this->email->body), 60),
            'status' => $this->email->status
        ];
    }
}
