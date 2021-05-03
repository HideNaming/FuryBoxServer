<?php

namespace App\Http\Controllers\API\V1;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Box;
use App\Models\User;
use App\Models\Cart;
use App\Models\Feed;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maksa988\FreeKassa\Facades\FreeKassa;
use App\Events\Stats;
use App\Events\BoxEvent;

class PaymentController extends Controller

{
    public function redirect(Request $request)
    {
        $rows = [
            'id' => Auth::id(),
            'partner' => $request->input('partner')
        ];

        $order = Order::create([
            "user_id" => Auth::id(),
            "amount" => $request->input('amount'),
            "partner" => $request->input('partner'),
            "status" => 'process',
        ]);

        $url = FreeKassa::getPayUrl($request->input('amount'), $order->id, null, null, $rows);
        return $url;
    }

    public function searchOrder(Request $request, $order_id)
    {
        $order = Order::where('id', $order_id)->first();

        if ($order) {
            $order['amount'] = $request->input('AMOUNT');
            return $order;
        }

        return false;
    }

    public function paidOrder(Request $request, $order)
    {
        $bonuses = [
            [
                "input" => 300,
                "bonus" => 5
            ],
            [
                "input" => 500,
                "bonus" => 10
            ],
            [
                "input" => 1000,
                "bonus" => 20
            ],
            [
                "input" => 2000,
                "bonus" => 30
            ],
            [
                "input" => 3000,
                "bonus" => 40
            ],
            [
                "input" => 5000,
                "bonus" => 50
            ],
        ];

        $calc = $order->amount;
        $bonus_percent = 0;

        foreach ($bonuses as $bonus) {
            if ($calc >= $bonus['input']) $bonus_percent = $bonus['bonus'] / 100;
        }

        $user = User::findOrFail($order->user_id);

        if ($user->created_at->addHours(12)->lt(Carbon::now()) && count((array) $user->orders) == 0) {
            $bonus_percent = $bonus_percent + 0.2;
        }

        $calc = $calc + $calc * $bonus_percent;
        $user->money = $user->money + $calc;

        $order->status = 'paid';
        $order->save();
        $user->save();
        return true;
    }

    public function handlePayment(Request $request)
    {
        return FreeKassa::handle($request);
    }
}
