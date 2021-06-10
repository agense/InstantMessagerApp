<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use App\Models\ContactInitiation;
use App\Http\Resources\ContactInitiationResource;

class ContactInitiationStatusChange implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    private $from;
    private $initiation;
    /**
     * The number of times the job may be attempted.
     * @var int
     */
    public $tries = 10;

    /**
     * Create a new event instance.
     * !!! Save all model data in json instead of using model serialization as the related model will be deleted at this time and cannot be accesed from db
     * @return void
     */
    public function __construct(ContactInitiation $initiation)
    {
        $this->from = $initiation->getRelationValue('from')->id;
        $this->initiation = json_encode($initiation);
    }

    /**
     * Get the data to broadcast.
     * @return array
     */
    public function broadcastWith()
    {
        return ['initiation' => new ContactInitiationResource($this->getInitiation()) ];
    }

    /**
     * Get the channels the event should broadcast on.
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('contact_initiation_status_change_.' .$this->from );
    }

    /**
     * Retrieves ContactInitiation model instance from DB or creates a new one from data saved in json
     */
    private function getInitiation(){
        $this->initiation = json_decode($this->initiation);
        if($this->initiation->status == 'rejected'){
            $initiation = ContactInitiation::findOrFail($this->initiation->id);
        }else{
            $initiation = ContactInitiation::createFromObjWithRelations($this->initiation);
        }
        return $initiation;
    }
}
