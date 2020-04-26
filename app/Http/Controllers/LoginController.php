<?php namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $data['email'])->first();

        if (is_null($user)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials'
            ]);
        }

        if ($user->password != $data['password']) {
            abort(400);
        }

        $user->api_token = $accessToken = Str::random();
        $user->save();

        return response()->json([
            'data' => [
                'access_token' => $accessToken
            ]
        ]);
    }
}
