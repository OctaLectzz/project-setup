<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\VerifyEmail;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'avatar' => 'nullable',
            'username' => 'required|string|max:20|unique:users,username',
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
            'phone_number' => 'nullable|string|max:255',
            'domicile' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:255',
            'pronouns' => 'nullable|string|max:255',
            'bio' => 'nullable'
        ]);

        // Avatar
        if ($request->hasFile('avatar')) {
            $avatarName = time() . '-' . auth()->user()->username . '.' . $request->avatar->getClientOriginalExtension();
            $request->avatar->move(public_path('avatars'), $avatarName);
            $data['avatar'] = $avatarName;
        }

        // Password
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        // Verification Code
        $input['verification_code'] = random_int(100000, 999999);

        $user = User::create($input);

        // Mail::to($user->email)->send(new VerifyEmail($user));

        Auth::login($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Register Successfully',
            'data' => new UserResource($user)
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::where('email', $request->email)->first();
            $success['token'] =  $user->createToken('japanetz', ['*'], now()->addWeek())->plainTextToken;
            $success['user'] =  new UserResource($user);

            return response()->json([
                'status' => 'success',
                'message' => 'Login Successfully',
                'data' => $success
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau Kata Sandi masih salah'
            ], 403);
        }
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'verification_code' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->where('verification_code', $request->verification_code)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode verifikasi salah'
            ], 422);
        }

        $user->email_verified = true;
        $user->verification_code = null;
        $token =  $user->createToken('japanetz', ['*'], now()->addWeek())->plainTextToken;
        $user->save();

        $data = [
            'token' => $token,
            'user' => new UserResource($user)
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Email Verification Successfully',
            'data' => $data
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout Successfully'
        ]);
    }
}
