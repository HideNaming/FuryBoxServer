<?php

namespace App\Http\Controllers\API\V1;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Box;
use App\Models\User;
use App\Models\Cart;
use App\Models\Feed;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\Stats;
use App\Events\BoxEvent;

class OpenController extends Controller

{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function demo($id)
    {

        $box_obj = Box::findOrFail($id);
        $box = $box_obj->toArray();
        usort($box['loot'], function ($a, $b) {
            return $b['price'] <=> $a['price'];
        });
        $filter = array_filter((array) $box['loot'], function($data) use ($box) {
            if ((float) $data['price'] > (float) $box['price'] && (Boolean) $data['stock'] === true) {
                return $data;
            }
        });
        $filter = array_slice($filter,0, ceil(count($filter)  * 0.7 - 1));

        (integer) $box_obj->opens++;
        $box_obj->save();

        event(new BoxEvent($box_obj));

        return $filter[array_rand($filter)];
    }

    public function real($id, Request $request)
    {
        $changes = [
            1 => 1,
            2 => 1.2,
            3 => 1.5
        ];
        if (!array_key_exists($request->input('change'), $changes))
            $change = $changes[1];
        else
            $change = $changes[$request->input('change')];

        $box_obj = Box::findOrFail($id);
        $box = $box_obj->toArray();
        if (Auth::user()->money < ((float) $box['price'] * $change - (float) $box['price'] * $change * (float) $box['sale'] / 100)) 
            return response()->json(['error' => 'NotEnought'], 403);
        else {
            $user = User::findOrFail(Auth::id());
            $user->money = (float) $user->money - ((float) $box['price'] * $change - (float) $box['price'] * $change * (float) $box['sale'] / 100);
            $user->save();
        }

        usort($box['loot'], function ($a, $b) {
            return $a['price'] <=> $b['price'];
        });
        $filter = array_filter((array) $box['loot'], function($data) use ($box, $change) {
            if ((float) $data['price'] < ((float) $box['price'] * $change - (float) $box['price'] * $change * (float) $box['sale'] / 100) && (Boolean) $data['stock'] === true) {
                return $data;
            }
        });
        $drop = $filter[array_rand($filter)];
        $cart_item = Cart::create([
            'user_id' => Auth::id(),
            'name' => $drop['name'],
            'price' => $drop['price'],
            'image' => $drop['image'],
        ])->toArray();

        (integer) $box_obj->opens++;
        $box_obj->save();

        event(new BoxEvent($box_obj));

        return $cart_item;
    }

    public function last_top_gift($id) {
        $box = Box::where('slug', $id)->first();
        $loot = Feed::where("box_id", $box->id)->whereDate('created_at', Carbon::today())->orderBy('id', 'desc')->get()->sortByDesc('loot.price')->first();
        return $loot;
    }
}
