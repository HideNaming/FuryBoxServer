<?php

namespace App\Http\Controllers\API\V1;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\AuctionLot;
use App\Models\AuctionRate;
use App\Models\Box;
use Validator;
use App\Events\AuctionEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Maksa988\FreeKassa\Facades\FreeKassa;
use App\Events\Stats;
use App\Events\BoxEvent;

class AuctionController extends Controller

{

    public function top()
    {
        $all = AuctionLot::whereNull('finish')->get()->toArray();
        usort($all, function ($a, $b) {
            return $a['benefit'] < $b['benefit'] ? 1 : -1;
        });
        return array_slice($all, 0, 5);
    }

    public function rate(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $lot = AuctionLot::findOrFail($request->input('id'));
        if ($lot->user_id == $user->id) return response()->json(['error' => 'You'], 403);

        if ($lot->rate == null) {
            $min_price = ceil($lot->price);
        } else {
            $min_price = ceil($lot->price + $lot->stap);
        }

        $validator = Validator::make($request->all(), [
            'payment' => 'required|integer|min:' . $min_price,
        ]);

        if ($validator->fails()) {
            $lot->box = Box::findOrFail($lot->loot->box_id);
            new AuctionEvent($lot);
            $validator->validate();
        }

        if (Auth::user()->money < (float) $request->input('payment'))
            return response()->json(['error' => 'NotEnought'], 403);

        $user->money = (float) $user->money - (float) $request->input('payment');
        $user->save();

        $lot->rate = $user->name;
        $lot->user_id = $user->id;
        $lot->price = $request->input('payment');
        $lot->updated = Carbon::now()->timestamp;
        $lot->save();

        AuctionRate::create([
            "auction_id" => $lot->id,
            "user_id" => $user->id,
            "price" =>  $request->input('payment')
        ]);
        $update = AuctionRate::where('price', '<', $request->input('payment'))->where('auction_id', $lot->id)->get()->each(function ($item) {
            $item->status = 'lose';
            $item->save();
            $user = User::find($item->user_id);
            if (!is_null($user)) {
                $user->money = $user->money + $item->price;
                $user->save();
            }
        });
        $lot->box = Box::findOrFail($lot->loot->box_id);
        event(new AuctionEvent($lot));
    }

    public function list()
    {
        return AuctionLot::whereNull('finish')->orderBy('finished', 'asc')->paginate(9);
    }

    public function get($id)
    {
        $lot = AuctionLot::findOrFail($id);
        $lot->box = Box::findOrFail($lot->loot->box_id);
        return $lot;
    }

    protected function personal()
    {
        $rates = AuctionRate::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get()->each(function ($item) {
            $item->lot = AuctionLot::find($item->auction_id);
        });

        return $rates;
    }
}
