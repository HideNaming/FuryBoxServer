<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Support\Facades\Auth;
use App\Models\Stat;
use App\Models\Box;
use App\Models\Feed;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class StatController extends Controller

{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function stat()
    {
        return ['stat' => [
            "online" => Box::sum('views'),
            "award" => ceil(array_sum(array_column(array_column(Feed::whereDate('created_at', Carbon::yesterday())->limit(40)->get()->toArray(), 'loot'), 'price'))),
            "feed" => Feed::orderBy('id', 'desc')->limit(15)->get()
        ]];
    }
}
