<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function broadcastOn()
    {
        // Kirim hanya ke teknisi yang ditugaskan
        return new PrivateChannel('jobs.'.$this->ticket->user_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->ticket->id,
            'issue_description' => $this->ticket->issue_description,
            'priority' => $this->ticket->priority,
            'status' => $this->ticket->status,
            'customer_name' => $this->ticket->pelanggan->nama_lengkap ?? null,
        ];
    }
}
