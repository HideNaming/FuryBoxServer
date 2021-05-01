<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Cart;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function sell($id)
    {
        $cart_item = Cart::findOrFail($id);
        $user = User::findOrFail(Auth::id());
        $user->money = (float) $user->money + (float) $cart_item->price;
        $user->save();
        $cart_item->delete();
    }

    protected function list()
    {
        return Cart::where('user_id', Auth::user()->id)->get();
    }

    protected function delivery(Request $request)
    {
        if (count($request->input('data')) < 2) return response()->json(['error' => 'Count'], 403);
        if (count($request->input('data')) > 4) return response()->json(['error' => 'Count'], 403);
        if (Auth::user()->money < 200) 
            return response()->json(['error' => 'NotEnought'], 403);
        else {
            $user = User::findOrFail(Auth::id());
            $user->money = (float) $user->money - 200;
            $user->save();
        }

        foreach ($request->input('data') as $row) {
            $item = Cart::findOrFail($row['id']);
            $item->delivery = 'process';
            $item->save();
        }
    }
}
