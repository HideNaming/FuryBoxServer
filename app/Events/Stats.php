<?php

namespace App\Events;


use Carbon\Carbon;
use App\Models\Box;
use App\Models\Feed;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;



class Stats implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;



    /**
     * Create a new event instance.
     *
     * @return void
     */

    public function __construct($feed)
    {
        $this->feed = $feed;
    }



    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */

    public function broadcastOn()
    {
        return ['stat-chanel'];
    }



    /**
     * The event's broadcast name.
     *
     * @return string
     */

    public function broadcastAs()
    {
        return 'StatsEvent';
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */

    public function broadcastWith()
    {
        return [
            "online" => Box::sum('views'),
            "feed" => $this->feed
        ];
    }
}
