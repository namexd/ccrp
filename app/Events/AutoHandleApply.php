<?php

namespace App\Events;

use App\Models\Ccrp\EquipmentChangeApply;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AutoHandleApply
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $apply;

    /**
     * Create a new event instance.
     *
     * @param EquipmentChangeApply $apply
     */
    public function __construct(EquipmentChangeApply $apply)
    {
        $this->apply=$apply;
    }

    public function getApply()
    {
        return $this->apply;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
    */
//    public function broadcastOn()
//    {
//        return new PrivateChannel('channel-name');
//    }
}
