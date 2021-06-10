<?php

namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\User;

class AccountDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;
    public $contacts;
    public $deletedAccount;
        /**
     * The number of times the job may be attempted.
     * @var int
     */
    public $tries = 10;

    /**
     * Create a new event instance.
     * Save the full model instance as json instead of serializing model id only, as the model is being deleted and cannot be retrieved from DB on broadcasting
     * @return void
     */
    public function __construct(User $deletedAccount, Array $contactIds)
    {
        $this->deletedAccount = json_encode($deletedAccount);
        $this->contactIds = $contactIds;
    }

    /**
     * Get the data to broadcast.
     * @return array
     */
    public function broadcastWith()
    {
        $this->deletedAccount = User::createFromObject(json_decode($this->deletedAccount));
        return ['contact' => $this->deletedAccount ];
    }


    /**
     * Get the channels the event should broadcast on.
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
       $channels = [];
       foreach($this->contactIds as $id){
           array_push($channels, new PrivateChannel('account_deleted_.'.$id));
        }
        return $channels;
    }
}
