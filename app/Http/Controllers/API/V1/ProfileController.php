<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Rules\MatchOldPassword;

class ProfileController extends Controller
{
    use AuthenticatesUsers;

    protected function changePassword(Request $request)
    {
        request()->validate([
            'password' => ['required', new MatchOldPassword],
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        $user = User::find(Auth::user()->id);
        $user->password = Hash::make($request->input('new_password'));
        $user->save();
    }

    protected function changeData(Request $request)
    {
        $user = User::find(Auth::user()->id);
        request()->validate([
            'name' => 'required|max:255',
            'email' => 'required|email:filter|max:255|unique:users,email,' . $user->id
        ]);
        $user->name = $request->input('name');
        $user->address = $request->input('address');
        $user->city = $request->input('city');
        $user->region = $request->input('region');
        $user->country = $request->input('country');
        $user->index = $request->input('index');
        $user->email = $request->input('email');
        $user->save();
    }

    protected function imageUpload(Request $request)
    {
        request()->validate([
            'file' => 'required|mimes:jpeg,jpg,png|max:5000',
        ]);

        $user = User::find(Auth::user()->id);

        $imageName = uniqid() . '.' . $request->file->extension();
        $request->file->move(public_path('avatars'), $imageName);

        if (!is_null($user->avatar) && file_exists(public_path('avatars') . '/' . $user->avatar)) {
            unlink(public_path('avatars') . '/' . $user->avatar);
        }

        $user->avatar = $imageName;
        $user->save();
    }

    protected function avatar($id)
    {
        $user = User::findOrFail($id);
        if (file_exists(public_path('avatars') . '/' . $user->avatar) && $user->avatar != null) {
            return response()->file(public_path('avatars') . '/' . $user->avatar);
        } else {
            return response()->file(public_path('avatars') . '/avatar.png');
        }
    }
}
