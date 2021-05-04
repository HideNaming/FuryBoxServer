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
    public function pay(Request $request)
    {
        $order = Order::create([
            "pay_id" => Auth::id(),
            "user_id" => Auth::id(),
            "amount" => $request->input('amount'),
            "partner" => $request->input('partner'),
            "status" => 'process',
        ]);

        return $order->id;
    }

    public function getOrder($id)
    {
        $order = Order::findOrFail($id);
        $user = User::findOrFail($order->user_id);

        $order->cashback = $order->amount * $this->Bonus($order, $user);
        return $order;
    }

    public function link(Request $request)
    {
        $order = Order::findOrFail($request->input('id'));
        $order->method = $request->input('method');

        if ($order->method == 'freekassa') {
            $rows = [
                'id' => Auth::id(),
                'partner' => $request->input('partner')
            ];
            $order->save();
            $url = FreeKassa::getPayUrl($order->amount, $order->id, null, null, $rows) . '&i=' . $request->input('payment_id');
        } else {
            $billPayments = new \Qiwi\Api\BillPayments(config('qiwi.secret_key'));
            $params = [
                'publicKey' => config('qiwi.public_key'),
                'amount' => $order->amount,
                'billId' => $order->id,
                'customFields' => [
                    'themeCode' => 'Yvan-T8FpAz5afI',
                    'paySourcesFilter' => $request->input('payment_id')
                ]
            ];
            $order->save();
            $url = $billPayments->createPaymentForm($params);
        }

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

    public function Bonus(Order $order, User $user)
    {
        $bonuses = [
            ["input" => 300, "bonus" => 5],
            ["input" => 500, "bonus" => 10],
            ["input" => 1000, "bonus" => 20],
            ["input" => 2000, "bonus" => 30],
            ["input" => 3000, "bonus" => 40],
            ["input" => 5000, "bonus" => 50],
        ];
        $bonus_percent = 0;

        foreach ($bonuses as $bonus) {
            if ($order->amount >= $bonus['input']) $bonus_percent = $bonus['bonus'] / 100;
        }

        if ($user->created_at->addHours(12)->gt(Carbon::now()) && $user->orders->count() == 0) {
            $bonus_percent = $bonus_percent + 0.2;
        }

        return $bonus_percent;
    }

    public function paidOrder(Request $request, $order)
    {
        $user = User::findOrFail($order->user_id);
        $calc = $order->amount + $order->amount * $this->Bonus($order, $user);

        $user->money = $user->money + $calc;
        $order->status = 'paid';
        $order->save();
        $user->save();
        return true;
    }

    public function handleQiwi(Request $request)
    {
        $billPayments = new \Qiwi\Api\BillPayments(config('qiwi.secret_key'));
        $orders = Order::where('method', 'qiwi')->where('status', '!=', 'paid')->each(function($order) use($billPayments, $request) {
            $response = $billPayments->getBillInfo($order->id);
            $order->amount = $response['amount']['value'];
            $order->save();
            if ($response['status']['value'] == 'PAID')
                $this->paidOrder($request, $order);
        });
    }

    public function handleFreekassa(Request $request)
    {
        return FreeKassa::handle($request);
    }
}
