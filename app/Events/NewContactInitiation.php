<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ContactInitiation; 
use App\Http\Resources\ContactInitiationResource;

class NewContactInitiation implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $initiation;
    
    /**
     * The number of times the job may be attempted.
     * @var int
     */
    public $tries = 10;

    /**
     * Create a new event instance.
     * @return void
     */
    public function __construct(ContactInitiation $initiation)
    {
        $this->initiation = $initiation;
    }

    /**
     * Get the data to broadcast.
     * @return array
     */
    public function broadcastWith()
    {
        return ['initiation' => new ContactInitiationResource($this->initiation)];
    }

    /**
     * Get the channels the event should broadcast on.
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('contact_initiations_.' .$this->initiation->to);
    }
}
