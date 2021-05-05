<?php
namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

  

class AuctionTop implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

  

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public function __construct($lots, $count)
    {
        $this->lots = $lots;
        $this->count = $count;
    }

  

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */

    public function broadcastOn()
    {
        return ['bot_integration'];
    }

  

    /**
     * The event's broadcast name.
     *
     * @return string
     */

    public function broadcastAs()
    {
        return 'AuctionTop';
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */

    public function broadcastWith()
    {
        return ['data' => $this->lots, 'count' => $this->count];
    }

}