<?php
namespace App\Events;

use App\Models\Box;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

  

class BoxEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

  

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public function __construct(Box $box)
    {
        $this->box = $box;
    }

  

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */

    public function broadcastOn()
    {
        return ['box.'.$this->box->slug];
    }

  

    /**
     * The event's broadcast name.
     *
     * @return string
     */

    public function broadcastAs()
    {
        return 'Box';
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */

    public function broadcastWith()
    {
        $data = [
            'opens' => $this->box->opens,
            'views' => $this->box->views
        ];
        return ['data' => $data];
    }

}