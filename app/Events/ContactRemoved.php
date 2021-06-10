<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class ContactRemoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $contactRemoved;
    public $currentUser;
    
    /**
     * The number of times the job may be attempted.
     * @var int
     */
    public $tries = 10;

    /**
     * Create a new event instance.
     * @return void
     */
    public function __construct(User $contactRemoved)
    {
        $this->contactRemoved = $contactRemoved;
        $this->currentUser = auth()->user();
    }

    /**
     * Get the data to broadcast.
     * @return array
     */
    public function broadcastWith()
    {
        return ['removed' => $this->contactRemoved, 'contact' => $this->currentUser];
    }


    /**
     * Get the channels the event should broadcast on.
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('removed_from_contacts_.' .$this->contactRemoved->id);
    }
}
