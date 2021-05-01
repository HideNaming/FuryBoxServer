<?php
namespace App\Events;

use App\Models\AuctionLot;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

  

class AuctionEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

  

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public function __construct(AuctionLot $lot)
    {
        $this->lot = $lot;
    }

  

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */

    public function broadcastOn()
    {
        return ['auction'];
    }

  

    /**
     * The event's broadcast name.
     *
     * @return string
     */

    public function broadcastAs()
    {
        return 'AuctionLot';
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */

    public function broadcastWith()
    {
        return ['data' => $this->lot];
    }

}